<?php
/**
 * Date: 2017-04-25
 * Time: 11:10
 * 浦发支付宝的 统一下单，返回通知，接收异步
 */
namespace App\Http\Controllers\PuFa;

use App\Http\Controllers\Push\JpushController;
use App\Models\WXNotify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PuFa\Tools;
use App\Http\Controllers\PuFa\Map;
use App\Models\PufaStores;
use Illuminate\Support\Facades\Config;

// use App\Models\PufaTradeQueries;
use App\Models\Order;

use App\Models\PufaConfig;
use App\Http\Controllers\Push\AopClient;
use Illuminate\Support\Facades\DB;
use App\Models\PushConfig;
use App\Models\PushPrintShopList;
//支付宝异步通知收银员
use App\Models\WeixinPayNotify;
use App\Models\WeixinPayConfig;
use EasyWeChat\Foundation\Application;
use App\Models\PageSets;

class AlipayTradeCreateController extends Controller
{
    static function log($data,$file='')
    {
        return;
        $file=$file ? $file : (storage_path().'/logs/pufa_error_log_store_alipay.txt');
        file_put_contents($file, "\n\n\n".date('Y-m-d H:i:s')."\n".var_export($data,TRUE),FILE_APPEND);
    }

    /**
     * 通过浦发银行向支付宝  统一生成订单
     * https://isv.umxnt.com/api/pufa/AliUnify
     */

    public function AliUnify(Request $request)
    {
        //获取浦发 支付接口 接口配置
        try
        {
            $pufaconfig = PufaConfig::where("id", '1')->first();
            $request_url =$pufaconfig->payurl;
        }
        catch(\Exception $e)
        {

            self::log($e->getMessage().$e->getFile().$e->getLine());
            $return = [
                'status' => 0,
                "trade_no" => "",
                "msg" => "服务商数据库错误！",
            ];
            return json_encode($return);
        }

        try
        {
            // 请求浦发的地址
            $shijian=date('YmdHis');
            $out_trade_no = $shijian . rand(1000000, 9999999);//服务商生成的交易流水号

            // 把钱转给商铺   根据商户号找到该商户
            $store_id = $request->get('store_id');//商户号，传递过来的
            $cashier_id = $request->get('cashier_id');
            $total_amount = trim($request->get('total_amount'));
            $remark = trim($request->get('remark'))?trim($request->get('remark')):'';

            $shop = PufaStores::where('store_id', $store_id)->first();
            if(!$shop)
            {
                $return = [
                    'status' => 0,
                    "trade_no" => "",
                    "msg" => "商户号不存在",
                ];
                return json_encode($return);
            }

            $shop=$shop->toArray();

            // 商户秘钥
            /*            if(empty($shop['merchant_pwd']))
                        {

                            $return = [
                                    'status' => 0,
                                    "trade_no" => "",
                                    "msg" => "商户未开通支付！",
                                ];
                            return json_encode($return);
                            echo '该商户没有设置交易钥匙';
                            die;
                        }
            */

            // 用户授权后，服务商拿到的用户资料---user_id有用
            $user = $request->session()->get('user_data');

            if(empty($user[0]->user_id))
            {
                $return = [
                    'status' => 0,
                    "trade_no" => "",
                    "msg" => "用户授权失败",
                ];
                return json_encode($return);
            }
            $key=trim($pufaconfig->security_key);
            if($shop['ch_pay_auth']==0){
                $key=$shop->merchant_pwd;
            }
            // $key=trim($shop['merchant_pwd']);
            // 异步通知地址   订单状态修改以及店铺的微信提醒
            $nturl=url('api/pufa/notify');
            $ordermktime=$shijian;


            $sign_agentno=$pufaconfig->partner;
            $data=[
                'service'=>'pay.alipay.jspay',
                'version'=>'2.0',
                'charset'=>'UTF-8',
                'sign_type'=>'MD5',
//                'sign_agentno'=>$sign_agentno,
                'mch_id'=>$shop['merchant_id'],//商户号
                'out_trade_no'=>$out_trade_no,
                // 'device_info'=>'设备号',
                'body'=>$shop['merchant_short_name'].'收款',
                'attach'=>$shop['merchant_short_name'].'收款',//'附加信息'
                'total_fee'=>$total_amount*100,//单位为：分
                'mch_create_ip'=> $request->getClientIp(),
                'notify_url'=>$nturl,
                'time_start'=>$ordermktime,
                // 'time_expire'=>'',
                // 'op_user_id'=>$shop['store_name'],//'操作员'
                // 'product_id'=>'商品标记',
                'nonce_str'=>md5($out_trade_no),
                // 'buyer_logon_id'=>'买家支付宝账号',
                'buyer_id'=>$user[0]->user_id,
                // 'sign'=>'签名',
            ];
            if($shop['ch_pay_auth']==0){
                $key=trim($shop['merchant_pwd']);
            }else
                $data['sign_agentno']=$sign_agentno;

            self::log($data);

            // 生成签名、生成xml数据
            $data=Tools::createSign($data,$key);
            $xmldata=Tools::toXml($data);//生成xml数据

            // 向浦发接口发送xml下单数据
            $xmlresult=Tools::curl($xmldata,$request_url);//获取银行xml数据
            self::log($xmlresult);

            //获取到数据
            if($xmlresult)
            {
                $thirddata=Tools::setContent($xmlresult);//返回银行结果数组
                //记录日志
                // self::log($thirddata);


                $verfysign=Tools::isTenpaySign($thirddata,$key);//验证签名
                if($verfysign)
                {
                    //1.下单成功，订单信息入库，其他状态均返回重新下单的提示
                    if($thirddata['status'] == 0 && $thirddata['result_code'] == 0){

                        // 订单信息---支付宝的订单流水号
                        $thirdpayinfo=json_decode($thirddata['pay_info'],true);

                        // 将该订单入库，状态标记为订单支付进行中
                        $lastid=Order::insertGetId([
                            'out_trade_no'=>$out_trade_no,//我方订单号
                            'trade_no'=>$thirdpayinfo['tradeNO'],//三方订单号
                            'store_id'=>$shop['store_id'],
                            'total_amount'=>$total_amount,
                            'merchant_id'=>$cashier_id,//收银员id
                            'pay_status'=>'3',
                            'buyer_id'=>$data['buyer_id'],
                            'type'=>'601',//支付宝支付方式
                            'created_at'=>date('Y-m-d H:i:s',strtotime($ordermktime)),
                            'remark'=>$remark,
                        ]);

                        // 服务器数据库错误
                        if(empty($lastid))
                        {
                            $data = [
                                'status' => 0,
                                "trade_no" => "",
                                "msg" => "数据库错误，支付已进行！",
                            ];
                            return json_encode($data);
                        }

                        $return = [
                            'status' => 1,
                            "trade_no" => $thirdpayinfo['tradeNO'],//支付宝的订单流水号
                            "msg" => "下单成功",
                        ];
                        return json_encode($return);
                    }

                    $return = [
                        'status' => 0,
                        "trade_no" => $out_trade_no,
                        "msg" => "下单失败，请重试",
                    ];
                    return json_encode($return);
                }

                // 3验签失败
                // echo '验证签名失败';die;
            }



        }
        catch(\Exception $e)
        {
            self::log($e->getMessage().$e->getFile().$e->getLine());
            $return = [
                'status' => 0,
                "trade_no" => $out_trade_no,
                "msg" => "下单失败，请重试",
            ];
            return json_encode($return);
        }



        // 4未获取到数据 //curl未返回参数，去数据库读取
        $return = [
            'status' => 0,
            "trade_no" => $out_trade_no,
            "msg" => "下单失败，请重试",
        ];
        return json_encode($return);

    }




    /*
        浦发支付宝的异步通知

    */
    public function notify(Request $request)
    {
        // 获取xml数据流
        $xmlcontent=file_get_contents('php://input');
        if(empty($xmlcontent))
            return ;
        // 将xml数据解析成数组
        $xmlarrdata=Tools::setContent($xmlcontent);
        self::log($xmlarrdata);
        /*  浦发返回的xml数据转成的数组----
                $xmlarrdata=array (
          'attach' => '开发测试商户号收款',
          'buyer_logon_id' => '134***@qq.com',
          'buyer_user_id' => '2088812605592949',
          'charset' => 'UTF-8',
          'fee_type' => 'CNY',
          'fund_bill_list' => '[{"amount":"0.01","fundChannel":"ALIPAYACCOUNT"}]',
          'mch_id' => '101520000465',
          'nonce_str' => '1490337983548',
          'openid' => '2088812605592949',
          'out_trade_no' => '201703241446186278632',
          'out_transaction_id' => '2017032421001004940290082875',
          'pay_result' => '0',
          'result_code' => '0',
          'sign' => 'BCD450835EC37B581DD2FA94B9BBC93A',
          'sign_type' => 'MD5',
          'status' => '0',
          'time_end' => '20170324144623',
          'total_fee' => '1',
          'trade_type' => 'pay.alipay.jspay',
          'transaction_id' => '101520000465201703243026970076',
          'version' => '2.0',
        )
        ;*/
        //相当于判断了两个status为0的情况
        self::log((bool)isset($xmlarrdata['out_trade_no']) && $xmlarrdata['out_trade_no']);
        if(isset($xmlarrdata['out_trade_no']) && $xmlarrdata['out_trade_no'])
        {
            $orderdata = Order::where('out_trade_no', $xmlarrdata['out_trade_no'])->first();
            self::log($orderdata);
            if(empty($orderdata))
            {
                return ;
            }

            //或者没有商户号的订单，统一返回支付失败。
            if(empty($orderdata->store_id))
            {
                return ;
            }

            $orderdata=$orderdata->toArray();

            // 订单状态已经标记为成功、失败、其他，
            if($orderdata['pay_status']=='1'||$orderdata['pay_status']=='2')
            {
                echo 'SUCCESS';
                return ;
            }

            // 找出商铺的秘钥，用于核对签名
            $store_id=$orderdata['store_id'];

            $shop = PufaStores::where('store_id', $store_id)->first();
            if(empty($shop))
            {
                return;
            }

            $pufaconfig = PufaConfig::where("id", '1')->first();
            $key=trim($pufaconfig->security_key);
            // $key=$shop->merchant_pwd;
            $verfysign=Tools::isTenpaySign($xmlarrdata,$key);//验证签名
            if($verfysign)
            {
                self::log('暗示法萨芬撒地方');
                // 支付成功
                if($xmlarrdata['pay_result']==0)
                {
                    $updatedata=[
                        'updated_at'=>date('Y-m-d H:i:s'),
                        'pay_status'=>'1',
                        // 'mark'=>isset($xmlarrdata['result_code'])?$xmlarrdata['result_code']:'',
                    ];
                    Order::where('out_trade_no','=',$xmlarrdata['out_trade_no'])->update($updatedata);
                    echo 'SUCCESS';
                    self::log('更新');


                    $zhang_dan=json_decode($xmlarrdata['fund_bill_list'],true);
////////////////////////////////////////
                    $Order = Order::where('out_trade_no', $xmlarrdata['out_trade_no'])->first();
                    //易联云打印机
                    try {
                        //小票机
                        $push = new AopClient();
                        $PushConfig = PushConfig::where('id', 1)->first();
                        $store=DB::table("pufa_stores")->where('store_id',$Order->store_id)->first();
                        $push_store = PushPrintShopList::where('store_id', $Order->store_id)->where("type","yilianyun")->get();
                        $info = PushPrintShopList::where('store_id', $Order->store_id)->where("type","yilianyun")->first();

                        $store_name=$store->merchant_short_name;
                        $phone=$info->phone;
                        if ($push_store){
                            $str = "<center>".$store_name."</center>
            <center>联系方式:".$phone."</center>
订单号:" . $Order->out_trade_no . "
付款金额:" . $Order->total_amount . "
付款用户:浦发支付宝
付款时间:" . date('Y-m-d:H-i-s', time()) . "
商户备注:" . $Order->remark . "
   ";
                            $conent = urlencode($str);
                            $arr = $push_store->toArray();//有可能多个
                            foreach ($arr as $v) {
                                try {
                                    $print = $push->action_print(
                                        $PushConfig->push_id,
                                        $v['machine_code'],
                                        $conent, $PushConfig->push_key,
                                        $v['msign']);
                                } catch (\Exception $exception) {
                                    continue;
                                }

                            }
                        }
                    } catch (\Exception $exception) {
                        Log::info($exception);
                    }
                    //安卓app语音播报
                    $jpush=new JpushController();
                    $jpush->push('支付宝', $Order->total_amount,$Order->out_trade_no);
                    //打印机 api 调用
                    try {
                        $Uprint = new \App\Http\Controllers\Push\Uprint();
                        $list = PushPrintShopList::where("store_id",$Order->store_id)->where("type", "Uprint")->first();
                        $out_trade_no = $Order->out_trade_no;
                        $total_amount = $Order->total_amount;
                        $date = date('Y-m-d:H-i-s', time());
                        $remark = $Order->remark;
                        if($list){
                            $DEVICE_NO = $list->machine_code; //U 印打印机身编号
                            $number = $list->number;
                            $code = $list->code;
                            $phone=$list->phone;
                            $code_description = $list->code_description;
                        }
                        if ($list) {
                            $message = "";
                            $message = $message . "^N" . $number; //将该订单打印两次
                            $message = $message . "^F1";//来电提醒
                            $message = $message . "\n";
                            //打印二维码//
                            $my_qrcode = $code;//要生成的二维码
                            $myurl_length = strlen($my_qrcode); //获取二维码链接长度
                            $ascii_length = chr($myurl_length); //把长度数字转换为 ASCII 码
                            $message = $message . "\n";
                            $message = $message . "^Q";
                            $message = $message . $ascii_length;
                            $message = $message . $my_qrcode;
                            //打印二维码操作完成//
                            $message = $message . "\n";
                            $message = $message . "^H2     " . $store_name; //将”打印演示”横纵放大 2 倍
                            $message = $message . "\n";
                            $message = $message . "\n";
                            $message = $message . " 联系方式： ";
                            $message = $message . $phone; //将”打印演示”横纵放大 2 倍
                            $message = $message . "\n";
                            $message = $message . "\n";
                            $message = $message . " 支付状态： ";//将”支付状态： ”纵向放大 2 倍
                            $message = $message . "已付款";
                            $message = $message . "\n";
                            $message = $message . " 订单号： ";
                            $message = $message . $out_trade_no;
                            $message = $message . "\n";
                            $message = $message . " 付款金额： ";
                            $message = $message . $total_amount;
                            $message = $message . "\n";
                            $message = $message . " 付款方式： ";
                            $message = $message . "浦发支付宝";
                            $message = $message . "\n";
                            $message = $message . " 付款时间： ";
                            $message = $message . $date;
                            $message = $message . "\n";
                            $message = $message . " 商户备注： ";
                            $message = $message . $remark;
                            $message = $message . "\n";
                            $message = $message . ". . . . . . . . . . . . . . . .  ";
                            $message = $message . $code_description;
                            $message = $message . "\n";
                            $Uprint->add_order($DEVICE_NO, $out_trade_no, $message);
                        }

                    } catch (\Exception $e) {
                        Log::info($e);
                    }











                    //支付宝通知商户收营员
                    try {
                        //微信提醒
                        $WeixinPayNotifyStore = WeixinPayNotify::where('store_id', $store_id)->first();
                        //实例化
                        $config = WeixinPayConfig::where('id', 1)->first();
                        $options = [
                            'app_id' => $config->app_id,
                            'secret' => $config->secret,
                            'token' => '18851186776',
                            'payment' => [
                                'merchant_id' => $config->merchant_id,
                                'key' => $config->key,
                                'cert_path' => $config->cert_path, // XXX: 绝对路径！！！！
                                'key_path' => $config->key_path,      // XXX: 绝对路径！！！！
                                'notify_url' => $config->notify_url,       // 你也可以在下单时单独设置来想覆盖它
                            ],
                        ];
                        $app = new Application($options);
                        $userService = $app->user;
                        $template = PageSets::where('id', 1)->first();
                        $notice = $app->notice;
                        $userIds = $WeixinPayNotifyStore->receiver;
                        $open_ids = explode(",", $userIds);
                        $templateId = $template->string1;
                        $url = $WeixinPayNotifyStore->linkTo;
                        $color = $WeixinPayNotifyStore->topColor;
                        $price=json_decode($xmlarrdata['fund_bill_list'],true);
                        $data = array(
                            "keyword1" => $price[0]['amount'],
                            "keyword2" => '支付宝(' . $xmlarrdata['buyer_logon_id'] . ')',
                            "keyword3" => '' . $orderdata['created_at'] . '',
                            "keyword4" => $orderdata['out_trade_no'],
                            "remark" => '祝' . $WeixinPayNotifyStore->store_name . '生意红火',
                        );

                        foreach ($open_ids as $v) {
                            $s = WXNotify::where('open_id', $v)->where('store_id', $store_id)->first();
                            if ($s) {
                                if ($s->status) {
                                    try {
                                        $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($v)->send();
                                    } catch (\Exception $exception) {
                                        Log::info($exception);
                                        continue;
                                    }
                                }
                            } else {
                                WXNotify::create([
                                    'store_id' => $store_id,
                                    'open_id'=>$v,
                                ]);
                                try {
                                    $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($v)->send();
                                } catch (\Exception $exception) {
                                    Log::info($exception);
                                    continue;
                                }
                            }
                        }

                    } catch (\Exception $exception) {
                        // Log::info($exception);
                        return json_encode([
                            'status' => 1,
                        ]);
                    }


///////////////////////////////////
                    return ;

                }
                // 支付失败
                $updatedata=[
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'pay_status'=>'2',
                    // 'mark'=>isset($xmlarrdata['result_code'])?$xmlarrdata['result_code']:'',
                ];
                Order::where('out_trade_no','=',$xmlarrdata['out_trade_no'])->update($updatedata);
                echo 'SUCCESS';
                return ;
            }

        }

        return ;
    }


    /**
     * 支付成功页面
     */
    public function PaySuccess(Request $request)
    {
        $price=$request->get('price');
        return view('pufa.alipayopen.paysuccess',compact('price'));

    }
    public function OrderErrors(Request $request){

        $code=$request->get('code');
        return view('pufa.alipayopen.ordererrors',compact('code'));
    }




}