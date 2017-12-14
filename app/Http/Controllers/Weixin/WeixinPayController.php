<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Push\JpushController;
use App\Merchant;
use App\Models\PageSets;
use App\Models\PushConfig;
use App\Models\PushPrintShopList;
use App\Models\WeixinPayConfig;
use App\Models\WeixinPayNotify;
use App\Models\WeixinShopList;
use App\Models\WXNotify;
use App\Models\WxPayOrder;
use App\Models\Order as weixinOrder;
use Illuminate\Http\Request;
use EasyWeChat\Foundation\Application;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use EasyWeChat\Payment\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeixinPayController extends BaseController
{
    //订单金额页面
    public function orderview(Request $request)
    {
        $store_id = $request->get('store_id');//子商户号
        $m_id=$request->get('m_id');
        $shop = WeixinShopList::where('store_id', $store_id)->first();
        return view('admin.weixin.orderview', compact('shop','store_id','m_id'));
    }

    //输入金额付款
    public function order(Request $request)
    {
        $remark=$request->get("remark");
        $wx_user_data = $request->session()->get('wx_user_data');
        $store_id = $request->get('store_id');
        $m_id=$request->get('m_id');//收银员
        $options = $this->Options();
        $shop = WeixinShopList::where('store_id',$store_id)->first();
        $options['payment']['sub_merchant_id'] = $shop->mch_id;
        $out_trade_no = date('Ymdhis', time()) . rand(10000,99999);//订单号
        $total_fee = (int)($request->get('total_fee') * 100);//金额
        $app = new Application($options);
        $payment = $app->payment;
        $attributes = [
            'trade_type' => 'JSAPI', // JSAPI，NATIVE，APP...
            'body' => $shop->store_name . '商家收款',
            'detail' => $shop->store_name . '商家收款',
            'out_trade_no' => $out_trade_no,
            'total_fee' => $total_fee,
            'notify_url' => url('/admin/weixin/ordernotify'), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid' => $wx_user_data[0]['id']
            // ...
        ];
        $order = new Order($attributes);
        $result = $payment->prepare($order);
        Log::info($result);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
            $merchant_name = "";
            try {
                if ($request->get('m_id')) {
                    $merchant_name = Merchant::where('id', $m_id)->first()->name;//收营员名称
                }
            } catch (\Exception $exception) {
            }
            $prepayId = $result->prepay_id;
            $data = [
                'mch_id' => $shop->mch_id,
                'store_id'=>$store_id,
                'out_trade_no' => $out_trade_no,
                'merchant_id'=>(int)$m_id,
                "type"=>"201",
                "buyer_id"=>$wx_user_data[0]['id'],
                "remark"=>$remark,
                'total_amount' =>$request->get('total_fee'),
                'open_id' => $wx_user_data[0]['id'],
                'status' => '',
            ];
            weixinOrder::create($data);
        }
        $json = $payment->configForPayment($prepayId);
        return $json;
    }

    //生成二维码付款
    public function createOrder()
    {

        $options = [
            // 前面的appid什么的也得保留哦
            'app_id' => 'wx789fb035be0b7481',
            // ...
            // payment
            'payment' => [
                'merchant_id' => '1273479101',
                'key' => 'dasdawdarwfesczzcaSADwrr3434fsfa',
                'cert_path' => app_path() . '/lib/cert/apiclient_cert.pem',// XXX: 绝对路径！！！！
                'key_path' => app_path() . '/lib/cert/apiclient_key.pem',// XXX: 绝对路径！！！！
                'notify_url' => url('/admin/weixin/ordernotify'),       // 你也可以在下单时单独设置来想覆盖它
                'device_info' => '013467007045764',
                //'sub_app_id'      => '',
                'sub_merchant_id' => '1405994502',
            ],
        ];
        $app = new Application($options);
        $payment = $app->payment;


        $attributes = [
            'trade_type' => 'NATIVE', // JSAPI，NATIVE，APP...
            'body' => '手机支付',
            'detail' => 'iPad mini 16G 白色',
            'out_trade_no' => date('Ymdhis', time()) . '8888' . date('Ymdhis', time()),//订单号
            'total_fee' => 50,
            'notify_url' => url('/admin/weixin/ordernotify'), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            // ...
        ];
        $order = new Order($attributes);

        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
            $prepayId = $result->prepay_id;
        }
        /*打印 $result
         * "return_code" => "SUCCESS"
    "return_msg" => "OK"
    "appid" => "wx789fb035be0b7481"
    "mch_id" => "1273479101"
    "sub_mch_id" => "1405994502"
    "device_info" => "013467007045764"
    "nonce_str" => "JCIGTp69E6U4zQ39"
    "sign" => "CC8EDF6A26FFB6EADA1D2849ACE92CC6"
    "result_code" => "SUCCESS"
    "prepay_id" => "wx20161030202137d82a8067b60501474833"
    "trade_type" => "NATIVE"
    "code_url" => "weixin://wxpay/bizpayurl?pr=GapY1jk"
         *
         */
        $code_url = $result->code_url;//获得二维码url

        return view('admin.weixin.createorder', compact('code_url'));
    }

    //支付结果通知网址
    public function ordernotify(Request $request)
    {
        $options = $this->Options();
        $app = new Application($options);
        $response = $app->payment->handleNotify(function ($notify, $successful) {
            $out_trade_no = $notify->out_trade_no;//订单号
            $result_code = $notify->result_code;
            // 你的逻辑
            try {
                $WxPayOrder = \App\Models\Order::where('out_trade_no', $out_trade_no)->first();
                if ($WxPayOrder) {
                    if ($WxPayOrder->status != $result_code) {
                        \App\Models\Order::where('out_trade_no', $out_trade_no)->update([
                            'status' => $result_code,
                            'pay_status'=>$this->pay_wx_status($result_code)
                        ]);
                        if ($result_code == "SUCCESS") {
                            try {
                                //设置成功提醒微信收款
                                $store_id =$WxPayOrder->store_id;//微信店铺id;
                                //安卓app语音播报
                                $jpush=new JpushController();
                                $jpush->push(''.$out_trade_no.'-微信', $WxPayOrder->total_amount,$out_trade_no);
                                $this->printY($store_id,$WxPayOrder,'微信支付');
                                $this->printU($store_id,$WxPayOrder,'微信支付');
                                $WeixinPayNotifyStore = WeixinPayNotify::where('store_id', $store_id)->first();
                                //实例化
                                $config = WeixinPayConfig::where('id', 1)->first();//微信支付参数 app_id

                                $optionsNotify = [
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
                                $app = new Application($optionsNotify);
                                $userService = $app->user;
                                $template = PageSets::where('id', 1)->first();
                                $notice = $app->notice;
                                $userIds = $WeixinPayNotifyStore->receiver;
                                $open_ids = explode(",", $userIds);
                                $templateId = $template->string1;
                                $url = $WeixinPayNotifyStore->linkTo;
                                $color = $WeixinPayNotifyStore->topColor;
                                $user = $userService->get($notify->openid);//买家open_id
                                $markstr='';
                                if($WxPayOrder&&!empty($WxPayOrder->remark)){
                                    $markstr.='(备注:'.$WxPayOrder->remark.')';
                                }
                                $data = array(
                                    "keyword1" => $WxPayOrder->total_amount,
                                    "keyword2" => '微信支付(' . $user->nickname . ')'.$markstr,
                                    "keyword3" => '' . $WxPayOrder->updated_at . '',
                                    "keyword4" => $out_trade_no,
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
                                Log::info($exception);
                                return false;
                            }
                        }
                    }
                }
            } catch (\Exception $exception) {
                Log::info($exception);
                return false;
            }
            return true; // 或者错误消息
        });
        return $response;
        /*$notify
         * {"appid":"wx789fb035be0b7481","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"Y","mch_id":"1273479101","nonce_str":"58a939461279b","openid":"opnT0s8Pltziuu2qATK3o8bKAWbA","out_trade_no":"20170219022054888820170219022054","result_code":"SUCCESS","return_code":"SUCCESS","sign":"75D69190E5D930EED252E43A83F457BC","sub_mch_id":"1419589702","time_end":"20170219142057","total_fee":"1","trade_type":"JSAPI","transaction_id":"4003762001201702190519905709"} */
    }

    public function paySuccess()
    {
        return view('admin.weixin.success');
    }

    public function pay_wx_status($status)
    {
        if ($status == "SUCCESS") {
            $pay_status = 1;
        } elseif ($status == "CLOSED") {
            $pay_status = 4;
        } elseif ($status == "USERPAYING") {
            $pay_status = 3;
        } else {
            $pay_status = 2;
        }
        return $pay_status;
    }

    public function printY($store_id,$Order,$type){
        try {
            $push = new \App\Http\Controllers\Push\AopClient();
            $PushConfig = PushConfig::where('id', 1)->first();
            $push_store = PushPrintShopList::where('store_id', $store_id)->where("type","yilianyun")->get();
            $info = PushPrintShopList::where('store_id', $store_id)->where("type","yilianyun")->first();

            if ($push_store) {
                $store=WeixinShopList::where('store_id',$store_id)->first();
                $store_name=$store->store_name;
                $phone=$info->phone;
                $str = "<center>".$store_name."</center>
            <center>联系方式:".$phone."</center>
订单号:" . $Order->out_trade_no . "
付款金额:" . $Order->total_amount . "
付款用户:".$type."
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
            // $print = $push->action_print(8978,'4004522408',$conent,'7a67e62b938e35dffdd1e0eee039bc83060070df','yshwr72kptab');
        } catch (\Exception $exception) {
            Log::info($exception);
        }
    }
    //U打印
    public function printU($store_id, $Order, $type)
    {
        //打印机 api 调用
        $Uprint = new \App\Http\Controllers\Push\Uprint();
        try {
            $list = PushPrintShopList::where("store_id", $store_id)->where("type", "Uprint")->first();
            $out_trade_no = $Order->out_trade_no;
            $total_amount = $Order->total_amount;
            $date = date('Y-m-d:H-i-s', time());
            $remark = $Order->remark;
            $store = WeixinShopList::where('store_id', $store_id)->first();
            if($store){
                $store_name = $store->store_name;
            }
            if($list){
                $phone = $list->phone;
                $DEVICE_NO = $list->machine_code; //U 印打印机身编号
                $number = $list->number;
                $code = $list->code;
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
                $message = $message . $type;
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
    }
}
