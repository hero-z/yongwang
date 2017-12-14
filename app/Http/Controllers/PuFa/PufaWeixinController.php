<?php
/**
 * 微信扫码支付
 */

namespace App\Http\Controllers\PuFa;


use App\Http\Controllers\Push\JpushController;
use App\Models\WXNotify;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Push\AopClient;
use App\Http\Controllers\Controller;
use App\Models\PufaStores;
use Illuminate\Http\Request;
use App\Http\Controllers\PuFa\Tools;
use App\Http\Controllers\PuFa\Map;
use Illuminate\Support\Facades\Config;
// use App\Models\PufaTradeQueries;
use App\Models\Order;
use App\Models\PushConfig;
use App\Models\PushPrintShopList;
use App\Models\PufaConfig;



//支付宝异步通知收银员
use App\Models\WeixinPayNotify;
use App\Models\WeixinPayConfig;
use EasyWeChat\Foundation\Application;
use App\Models\PageSets;


class PufaWeixinController extends Controller
{

    static function log($data,$file='')
    {
        return;
        $file=$file ? $file : (storage_path().'/logs/pufa_error_log_store_weixin.txt');
        file_put_contents($file, "\n\n\n".date('Y-m-d H:i:s')."\n".var_export($data,TRUE),FILE_APPEND);
    }


    /*
        显示表单--

    */
    function wxform(Request $request)
    {

// self::log('xxxxxxxxxxxx');
        $store_id = $request->get('store_id');//服务商生成的商户id
        $cashier_id = $request->get('cashier_id','0');//子商户号
        try {
            $shop = PufaStores::where('store_id', $store_id)->first();
        } catch (\Exception $exception) {
            Log::info($exception);
        }

        if(empty($shop))
        {
            echo '<h1>商户不存在！</h1>';
            die;
        }
        $shop=$shop->toArray();

        // var_dump($shop->toArray());die;
        return view('pufa.weixin.wxform', compact('shop','cashier_id'));

    }



    /*
        统一下单
http://isv.umxnt.com/api/pufa/wxorder
        

        表单提交过来
            浦发商户号
            金额
        session中存放用户授权信息
            $wx_user_data = $request->session()->get('wx_user_data');
            $user_id=$wx_user_data[0]['id'];
        其他需要的参数
            请求地址
            浦发商户秘钥


    */
    function wxorder(Request $request)
    {

        $store_id=$request->get('store_id');
            $cashier_id = $request->get('cashier_id','0');//商户号，传递过来的
            $remark = trim($request->get('remark'))?trim($request->get('remark')):'';
         Log::info($request->all());
        //获取浦发请求地址
        try
        {
            $pufaconfig = PufaConfig::where("id", '1')->first();
            $request_url =$pufaconfig->payurl;

            $haveshop = PufaStores::where('store_id', $store_id)->first();
            if(empty($haveshop))
            {
                return [
                    'status'=>0,
                    'message'=>'商铺不存在！'
                ];
            }
            $haveshop=$haveshop->toArray();
            $merchant_key=trim($pufaconfig->security_key);
            // $merchant_key=trim($haveshop['merchant_pwd']);
            // $merchant_key='9d101c97133837e13dde2d32a5054abb';
// 测试商户号和秘钥
// $merchant_id='7551000001';
// $merchant_key='9d101c97133837e13dde2d32a5054abb';

        }
        catch(\Exception $e)
        {

self::log($e->getMessage().$e->getFile().$e->getLine());
           $return = [
                'status' => 0,
                "trade_no" => "",
                "message" => "服务商数据库错误！",
            ];
            return json_encode($return);
        }


        try
        {
            // 用户授权微信的id    消费者微信id
            $wx_user_data = $request->session()->get('wx_user_data');
            $user_id=$wx_user_data[0]['id'];

            $shijian=date('YmdHis');
            $out_trade_no = $shijian . rand(1000000, 9999999);//服务商生成的交易流水号
            $ordermktime=$shijian;
            $notify_url=url('api/pufa/wxnotify');  
            $total_amount=trim($request->get('total_fee'));
            $total_fee=(int)($total_amount*100);
            $callback_url=url('api/pufa/resultPage');
            $sign_agentno=$pufaconfig->partner;

//请求浦发接口完成下单--start
            $data=array(
                    'service'=>'pay.weixin.jspay',
                'version'=>'2.0',
//                    'sign_agentno'=>$sign_agentno,
                'charset'=>'UTF-8',
                'sign_type'=>'MD5',
                    'mch_id'=>$haveshop['merchant_id'],
                'is_raw'=>'1',
                    'out_trade_no'=>$out_trade_no,//订单号
                'device_info'=>'',//设备号
                    'body'=>$haveshop['merchant_short_name'].'收款',//商品描述
                    'sub_openid'=>$user_id,//消费者的微信授权标识，测试账号留空
                'attach'=>'附加信息',
                    'total_fee'=>$total_fee,
                    'mch_create_ip'=>$request->getClientIp(),
                    'notify_url'=>$notify_url,
                'callback_url'=>$callback_url,
                'time_start'=>$shijian,
                // 'time_expire'=>'',
                'goods_tag'=>'flagwx',//微信优惠券的使用到的参数
                    'nonce_str'=>md5($out_trade_no),
                // 'limit_credit_pay'=>'1',
                    'sign'=>'',
                );

            if($haveshop['ch_pay_auth']==0){
                $merchant_key=trim($haveshop['merchant_pwd']);
            }else
                $data['sign_agentno']=$sign_agentno;
            // 生成签名、生成xml数据
            $data=Tools::createSign($data,$merchant_key);
//记录日志            
self::log($data);      
      
            $xmldata=Tools::toXml($data);//生成xml数据
// self::log($xmldata);      
            // 向浦发接口发送xml下单数据
            $xmlresult=Tools::curl($xmldata,$request_url);//获取银行xml数据

            //获取到数据
            if(!$xmlresult)
            {
               $return = [
                    'status' => 0,
                    "trade_no" => "",
                    "message" => "订单支付异常，请查看客户端通知情况！",
                ];
            }

            $thirddata=Tools::setContent($xmlresult);//返回银行结果数组
//记录日志            
self::log($thirddata); 

            $verfysign=Tools::isTenpaySign($thirddata,$merchant_key);//验证签名
            if($verfysign)
            {
                //1.下单成功，订单信息入库，其他状态均返回重新下单的提示
                if($thirddata['status'] == 0 && $thirddata['result_code'] == 0){

                    // 将该订单入库，状态标记为订单支付进行中
                    $lastid=Order::insertGetId([
                        'out_trade_no'=>$out_trade_no,
                        // 'transaction_id'=>$thirdpayinfo['tradeNO'],
                        'store_id'=>$haveshop['store_id'],
                        'total_amount'=>$total_amount,
                        'merchant_id'=>$cashier_id,
                        'pay_status'=>'3',
                        'buyer_id'=>$user_id,
                        'remark'=>$remark,
                        'type'=>'602',
                        'created_at'=>date('Y-m-d H:i:s',strtotime($ordermktime)),
                    ]);

                    // 服务器数据库错误
                    if(empty($lastid))
                    {
                        $data = [
                                'status' => 0,
                                "trade_no" => "",
                                "message" => "数据库错误，支付已进行",
                            ];
                        return json_encode($data);
                    }

                    $return = [
                            'status' => 1,
                            'data' => $thirddata['pay_info'],
                            // "trade_no" => $thirdpayinfo['tradeNO'],//支付宝的订单流水号
                            "message" => "支付成功",
                        ];
                    return json_encode($return);
                }

                $return = [
                        'status' => 0,
                        // "trade_no" => $out_trade_no,
                        "message" => "支付失败，请重试！",
                    ];
                return json_encode($return);
            }

            // 3验签失败
            // echo '验证签名失败';die;

            $return = [
                    'status' => 0,
                    // "trade_no" => $out_trade_no,
                    "message" => "支付失败，请重试！",
                ];
            return json_encode($return);
     
//请求浦发接口完成下单--end

        }
        catch(\Exception $e)
        {
self::log($e->getMessage()."\n".$e->getLine()."\n".$e->getFile());
            $data = [
                    'status' => 0,
                    "trade_no" => "",
                    "message" => "支付失败，请重试！",
                ];
            return json_encode($data);

        }


    }


// 异步通知地址
    function wxnotify()
    {

        // 获取xml数据流
        $xmlcontent=file_get_contents('php://input');
        if(empty($xmlcontent))
        {
            return ;
        }

        // 将xml数据解析成数组
        $xmlarrdata=Tools::setContent($xmlcontent);

self::log($xmlarrdata);

    //status 为0
        if(isset($xmlarrdata['status']) && $xmlarrdata['status']==0)
        {

/*


2017-03-29 13:57:48
array (
  'attach' => '附加信息',
  'bank_type' => 'CFT',
  'charset' => 'UTF-8',
  'fee_type' => 'CNY',
  'is_subscribe' => 'N',
  'mch_id' => '7551000001',
  'nonce_str' => '1490767007491',
  'openid' => 'oywgtuPi_VNVHjXWkhtTd4keQOlo',
  'out_trade_no' => '201703291355593483080',
  'out_transaction_id' => '4000272001201703295054894939',
  'pay_result' => '0',
  'result_code' => '0',
  'sign' => 'BA184A1948F5D47C9BE13210EC574D3C',
  'sign_type' => 'MD5',
  'status' => '0',
  'sub_appid' => 'wxce38685bc050ef82',
  'sub_is_subscribe' => 'N',
  'sub_openid' => 'oHmbktxdnBgDs0oN8vnVQCNfc1iU',
  'time_end' => '20170329135647',
  'total_fee' => '1',
  'trade_type' => 'pay.weixin.jspay',
  'transaction_id' => '7551000001201703293143332838',
  'version' => '2.0',
)



*/

            $orderdata = Order::where('out_trade_no', $xmlarrdata['out_trade_no'])->first();
            if(empty($orderdata))
            {
                return ;
            }
            
            $orderdata=$orderdata->toArray();
            // 订单状态已经标记为成功、失败、其他，
            if($orderdata['status']=='1'||$orderdata['status']=='2')
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
            if($shop->ch_pay_auth==0){
                $key=$shop->merchant_pwd;
            }

            $verfysign=Tools::isTenpaySign($xmlarrdata,$key);//验证签名
            if(!$verfysign)
            {return ;}
            // 支付成功
            if(isset($xmlarrdata['pay_result'])&&$xmlarrdata['pay_result']==0)
            {
                $updatedata=[
                    'trade_no'=>isset($xmlarrdata['out_transaction_id'])?$xmlarrdata['out_transaction_id']:'',
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'pay_status'=>'1',
                ];
                Order::where('out_trade_no','=',$xmlarrdata['out_trade_no'])->update($updatedata);
                echo 'SUCCESS';

                $Order = Order::where('out_trade_no', $xmlarrdata['out_trade_no'])->first();
//////////////////////////////////////

             //易联云打印机
                try {
                    //小票机
                    $push = new AopClient();
                    $PushConfig = PushConfig::where('id', 1)->first();
                    $store=DB::table("pufa_stores")->where('store_id',$Order->store_id)->first();
                    $push_store = PushPrintShopList::where('store_id', $Order->store_id)->where("type","yilianyun")->get();
                    $store_name=$store->merchant_short_name;
                    $phone=$push_store->phone;
                    if ($push_store){
                        $str = "<center>".$store_name."</center>
            <center>联系方式:".$phone."</center>
订单号:" . $Order->out_trade_no . "
付款金额:" . $Order->total_amount . "
付款用户:浦发微信
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
                        $message = $message . "浦发微信";
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


                //安卓app语音播报
                $jpush=new JpushController();
                $jpush->push('微信', $Order->total_amount,$Order->out_trade_no);



                //微信通知商户收营员
                try {
                    //店铺通知微信
                    $store_id = $orderdata['store_id'];
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
                    $user = $userService->get($xmlarrdata['sub_openid']);//买家open_id
                    $template = PageSets::where('id', 1)->first();
                    $notice = $app->notice;
                    $userIds = $WeixinPayNotifyStore->receiver;
                    $open_ids = explode(",", $userIds);
                    $templateId = $template->string1;
                    $url = $WeixinPayNotifyStore->linkTo;
                    $color = $WeixinPayNotifyStore->topColor;
                    $andData = array(
                        "keyword1" => $orderdata['total_amount'],
                        "keyword2" => '微信(' . $user->nickname . ')',
                        "keyword3" => '' . $orderdata['created_at']. '',
                        "keyword4" =>$orderdata['out_trade_no'],
                        "remark" => '祝' . $WeixinPayNotifyStore->store_name . '生意红火',
                    );

                    foreach ($open_ids as $v) {
                        $s = WXNotify::where('open_id', $v)->where('store_id', $store_id)->first();
                        if ($s) {
                            if ($s->status) {
                                try {
                                    $notice->uses($templateId)->withUrl($url)->andData($andData)->andReceiver($v)->send();
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
                                $notice->uses($templateId)->withUrl($url)->andData($andData)->andReceiver($v)->send();
                            } catch (\Exception $exception) {
                                Log::info($exception);
                                continue;
                            }
                        }
                    }


                } catch (\Exception $exception) {
                    Log::info($exception);
                    return json_encode([
                        'status' => 1,
                    ]);
                }



/////////////////////////////////////
                return ;

            }

            // 支付失败
            if(isset($xmlarrdata['pay_result'])&&$xmlarrdata['pay_result']!=0)
            {
                $updatedata=[
                    'trade_no'=>isset($xmlarrdata['out_transaction_id'])?$xmlarrdata['out_transaction_id']:'',
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'pay_status'=>'2',
                ];
                Order::where('out_trade_no','=',$xmlarrdata['out_trade_no'])->update($updatedata);
                echo 'SUCCESS';
                return ;
            }

            }
        return ;
    }



    public function resultPage(Request $request)
    {
        $price=$request->get('price','');
        if(!empty($price))
        {
            return view('pufa.weixin.wxsuccess',compact('price'));
        }
        else
        {
            return view('pufa.weixin.wxfail');
        }


    }
}