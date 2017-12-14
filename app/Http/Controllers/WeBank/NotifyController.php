<?php
/**
 * Created by PhpStorm.
 * User: hero
 * Date: 2017/5/23
 * Time: 14:37
 */

namespace App\Http\Controllers\WeBank;

use App\Models\Order;
use App\Models\PageSets;
use App\Models\WeixinPayConfig;
use App\Models\WeixinPayNotify;
use App\Models\WXNotify;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;
use Symfony\Component\HttpFoundation\Request;

class NotifyController extends BaseController
{
    public function ali_callback(Request $request){
        try{
            if($this->checkSign($request,2)){
                $data=json_decode($request->data,true);
                $order_id=$data['orderId'];
                $order=Order::where('out_trade_no',$order_id)->first();
                if($order&&$order->status!=$data['tradeStatus']){
                    $paystatus=$data['tradeStatus']=='01'?1:3;
                    Order::where('out_trade_no',$order_id)->update([
                        'status'=>$data['tradeStatus'],
                        'total_amount'=>$data['totalAmount'],
                        'pay_status'=>$paystatus
                    ]);
                    if($data['tradeStatus']=='01'){
                        //微信推送
//                        Log::info('微信推送');
//                        Log::info($data);
//                        Log::info($order);
                        $store_id = $order->store_id;
                        $WeixinPayNotifyStore = WeixinPayNotify::where('store_id', $store_id)->first();
                        //实例化
                        $config = WeixinPayConfig::where('id', 1)->first();

                        if ($WeixinPayNotifyStore&&$config) {
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
                            $markstr='';
                            if($order&&!empty($order->remark)){
                                $markstr.='(备注:'.$order->remark.')';
                            }
                            $data = array(
                                "keyword1" => $order->total_amount,
                                "keyword2" => '支付宝(' . $data['buyerId'] . ')'.$markstr,
                                "keyword3" => '' . $order->updated_at . '',
                                "keyword4" => $data['outTradeNo'],
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
                                        'open_id' => $v,
                                    ]);
                                    try {
                                        $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($v)->send();
                                    } catch (\Exception $exception) {
                                        Log::info($exception);
                                        continue;
                                    }
                                }
                            }

                        }
                    }
                }
            }else{
                Log::info('校验失败');
            }
        }catch (Exception $e){
            Log::error($e);
        }
        return '200';
    }
    public function wx_callback(Request $request){
//        Log::info($request->all());
        try{
            if($this->checkSign($request,1)){
                $data=json_decode($request->data,true);
                $order_id=$data['orderId'];
                $order=Order::where('out_trade_no',$order_id)->first();
                if($order&&$order->status!=$data['tradeStatus']){
                    $paystatus=$data['tradeStatus']=='01'?1:3;
                    Order::where('out_trade_no',$order_id)->update([
                        'status'=>$data['tradeStatus'],
                        'total_amount'=>$data['totalAmount'],
                        'trade_no'=>$data['outTradeNo'],
                        'pay_status'=>$paystatus
                    ]);
                    if($data['tradeStatus']=='01'){
                        //微信推送
//                        Log::info('微信推送');
//                        Log::info($data);
//                        Log::info($order);
                        $store_id = $order->store_id;
                        $WeixinPayNotifyStore = WeixinPayNotify::where('store_id', $store_id)->first();
                        //实例化
                        $config = WeixinPayConfig::where('id', 1)->first();

                        if ($WeixinPayNotifyStore&&$config) {
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
                            $markstr='';
                            if($order&&!empty($order->remark)){
                                $markstr.='(备注:'.$order->remark.')';
                            }
                            $data = array(
                                "keyword1" => $order->total_amount,
                                "keyword2" => '微信(' . $data['buyerId'] . ')'.$markstr,
                                "keyword3" => '' . $order->updated_at . '',
                                "keyword4" => $data['outTradeNo'],
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
                                        'open_id' => $v,
                                    ]);
                                    try {
                                        $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($v)->send();
                                    } catch (\Exception $exception) {
                                        Log::info($exception);
                                        continue;
                                    }
                                }
                            }

                        }
                    }
                }
            }else{
                Log::info('校验失败');
            }
        }catch (Exception $e){
            Log::error($e);
        }
        return '200';
    }
}