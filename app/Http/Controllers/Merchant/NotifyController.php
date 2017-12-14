<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/17
 * Time: 16:09
 */

namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\Controller;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayShopLists;
use App\Models\AlipayStoreInfo;
use App\Models\AlipayTradeQuery;
use App\Models\MerchantOrders;
use App\Models\Order;
use App\Models\PageSets;
use App\Models\WeixinPayConfig;
use App\Models\WeixinPayNotify;
use App\Models\WXNotify;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;


class NotifyController extends Controller
{
    //微信异步处理
    public function wxcodeurlnotify(Request $request){
        $config = WeixinPayConfig::where('id', 1)->first();
        $options = [
            'app_id' => $config->app_id,
            'payment' => [
                'merchant_id' => $config->merchant_id,
                'key' => $config->key,
                'cert_path' => $config->cert_path, // XXX: 绝对路径！！！！
                'key_path' => $config->key_path,      // XXX: 绝对路径！！！！
                'notify_url' => $config->notify_url,       // 你也可以在下单时单独设置来想覆盖它
            ],
        ];
        $app = new Application($options);
        $response = $app->payment->handleNotify(function ($notify, $successful) {
            $result_code = $notify->result_code;
            $out_trade_no = $notify->out_trade_no;//订单号
            // 你的逻辑
            try {
                $orderinfo=\App\Models\Order::where('out_trade_no',$out_trade_no)->first();
                if ($orderinfo) {
                    if ($orderinfo->status != $result_code) {
                        \App\Models\Order::where('out_trade_no', $out_trade_no)->update([
                            'trade_no'=>$notify->transaction_id,
                            'buyer_id'=>$notify->openid,
                            'status'=>$result_code,
                            'pay_status'=>1,
                        ]);

                        //微信通知商户收营员
                        try {
                            //店铺通知微信
                            if ($result_code == "SUCCESS") {
                                $store_id = $orderinfo->store_id;//微信店铺id;

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
//                                $user = $userService->get($notify->openid);//买家open_id
                                $template = PageSets::where('id', 1)->first();
                                $notice = $app->notice;
                                $userIds = $WeixinPayNotifyStore->receiver;
                                $open_ids = explode(",", $userIds);
                                $templateId = $template->string1;
                                $url = $WeixinPayNotifyStore->linkTo;
                                $color = $WeixinPayNotifyStore->topColor;
                                $andData = array(
                                    "keyword1" => $orderinfo->total_amount,
//                                    "keyword2" => '微信(' . $user->nickname . ')',
                                    "keyword2" => '微信支付',
                                    "keyword3" => '' . $orderinfo->updated_at . '',
                                    "keyword4" => $out_trade_no,
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
                                        continue;
                                    }
                                }
                            }

                        } catch (\Exception $exception) {
                            Log::info('微信固定二维码');
                            Log::info($exception);
                            return json_encode([
                                'status' => 1,
                            ]);
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
    //支付宝异步处理
    public function alicodeurlnotify(Request $request){
        //支付异步通知
        $config = AlipayIsvConfig::where('id', 1)->first();
        if($config){
            $alipayrsaPublicKey = $config->alipayrsaPublicKey;
            $config=$config->toArray();
            //1.接入参数初始化
            $aop = app('AopClient');
            $aop->gatewayUrl = Config::get('alipayopen.gatewayUrl');
            $aop->appId = $config['app_id'];
            $aop->rsaPrivateKey = $config['rsaPrivateKey'];
            $aop->alipayrsaPublicKey = $config['alipayrsaPublicKey'];
            $aop->format = "json";
            $aop->charset = "GBK";
            $aop->version="2.0";
            $aop->alipayrsaPublicKey = $alipayrsaPublicKey;
            $umxnt = $aop->rsaCheckUmxnt($request->all(), $alipayrsaPublicKey);

            if ($umxnt) {
                $data = $request->all();
                $orderinfo= Order::where('out_trade_no', $data['out_trade_no'])->first();
                //如果状态不相同修改数据库状态
                if ($orderinfo['status'] != $data['trade_status']) {
                    $statusformat=['WAIT_BUYER_PAY'=>3,'TRADE_CLOSED'=>4,'TRADE_SUCCESS'=>1,'TRADE_FINISHED'=>6];
                    $updatedata=[
                        'trade_no'=>$data['trade_no'],
                        'buyer_id'=>$data['buyer_id'],
                        'status'=>$data['trade_status'],
                        'total_amount'=>$data['total_amount'],
                        'pay_status'=>$statusformat[$data['trade_status']]
                    ];
                    \App\Models\Order::where('out_trade_no', $data['out_trade_no'])->update($updatedata);

                    //微信通知商户收营员
                    try {
                        //店铺通知微信
                        if ($data['trade_status'] == 'TRADE_SUCCESS') {
                            $store_id = $orderinfo->store_id;
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
                            $res=json_decode($data['fund_bill_list'],true);
                            $channelformat=['COUPON'=>'红包','ALIPAYACCOUNT'=>'支付宝余额','POINT'=>'集分宝','DISCOUNT'=>'折扣券','PCARD'=>'预付卡','FINANCEACCOUNT'=>'余额宝','MCARD'=>'商家储值卡','MDISCOUNT'=>'商户优惠券','MCOUPON'=>'商户红包','PCREDIT'=>'蚂蚁花呗'];
                            $moneyfrom='';
                            if(array_key_exists($res[0]['fundChannel'],$channelformat)){
                                $moneyfrom='('.$channelformat[$res[0]['fundChannel']].')';
                            }
                            $data = array(
                                "keyword1" => $orderinfo->total_amount.$moneyfrom,
                                "keyword2" => '支付宝(' . $data['buyer_logon_id'] . ')',
//                                "keyword3" => $channelformat[$res[0]['fundChannel']],
                                "keyword3" => '' . $orderinfo->updated_at . '',
                                "keyword4" => $data['trade_no'],
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
                                    continue;
                                }
                            }


                        }

                    } catch (\Exception $exception) {
                        Log::info('支付宝固定二维码');
                        Log::info($exception);
                        return json_encode([
                            'status' => 1,
                        ]);
                    }
                }
            }
        }
        return 'SUCCESS';
    }
}