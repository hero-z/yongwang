<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/2/21
 * Time: 15:33
 */

namespace App\Http\Controllers\PingAn;


use Alipayopen\Sdk\AopClient;
use App\Http\Controllers\Push\JpushController;
use App\Merchant;
use App\Models\MerchantOrders;
use App\Models\MerchantShops;
use App\Models\Order;
use App\Models\PageSets;
use App\Models\PinganConfig;
use App\Models\PinganStore;
use App\Models\PinganTradeQueries;
use App\Models\PushConfig;
use App\Models\PushPrintShopList;
use App\Models\WeixinPayConfig;
use App\Models\WeixinPayNotify;
use App\Models\WXNotify;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JPush\Client;
use Mockery\CountValidator\Exception;

class NotifyController extends BaseController
{
    public function best_notify_url(Request $request)
    {
        $response = $request->all();
        $check = $this->Check($request->all());
        if ($check) {
            //改变数据库的状态
            $data = $request->all();
            $Order = Order::where('out_trade_no', $data['out_trade_no'])->first();
            //通过接口查状态
            $aop = $this->AopClient();
            $aop->method = "fshows.liquidation.alipay.trade.query";
            $payStatus = [
                'trade_no' => $data['trade_no']
            ];
            $dataStatus = array('content' => json_encode($payStatus));
            try {
                $response = $aop->execute($dataStatus);
                $responseArray = json_decode($response, true);
            } catch (\Exception $exception) {
                Log::info($exception);
            }
            //如果状态不相同修改数据库状态
            $acptstatus = $responseArray['return_value']['trans_status'];
            if ($Order->status != $acptstatus) {
                Order::where('out_trade_no', $data['out_trade_no'])->update([
                    'status' => $acptstatus,
                    'pay_status' => $this->pay_status($acptstatus),
//                    'total_amount' => $responseArray['return_value']['total_fee'],
                ]);
//                微信通知商户收营员
                try {
                    //店铺通知微信
                    if ($responseArray['return_value']['trans_status'] == 'B') {
                        $store_id = $Order->store_id;
//安卓app语音播报
                        $jpush=new JpushController();
                        $jpush->push(''.$data['out_trade_no'].'-翼支付',$responseArray['return_value']['total_fee'],$data['out_trade_no']);
                        $this->printY($store_id, $Order, '平安翼支付');
                        //U打印
                        $this->printU($store_id, $Order, "平安翼支付");
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
//                        $user = $userService->get($responseArray['return_value']['open_id']);//买家open_id
                        $template = PageSets::where('id', 1)->first();
                        $notice = $app->notice;
                        $userIds = $WeixinPayNotifyStore->receiver;
                        $open_ids = explode(",", $userIds);
                        $templateId = $template->string1;
                        $url = $WeixinPayNotifyStore->linkTo;
                        $color = $WeixinPayNotifyStore->topColor;
                        $markstr='';
                        if($Order&&!empty($Order->remark)){
                            $markstr.='(备注:'.$Order->remark.')';
                        }
                        $andData = array(
                            "keyword1" => $Order->total_amount,
                            "keyword2" => '翼支付(' . $responseArray['return_value']['customer_id'] . ')'.$markstr,
                            "keyword3" => '' . $Order->updated_at . '',
                            "keyword4" => $responseArray['return_value']['trade_no'],
                            "remark" => '祝' . $WeixinPayNotifyStore->store_name . '生意红火',
                        );
                        Log::info($andData);
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
                                    'open_id' => $v,
                                ]);
                                try {
                                    $notice->uses($templateId)->withUrl($url)->andData($andData)->andReceiver($v)->send();
                                } catch (\Exception $exception) {
                                    Log::info($exception);
                                    continue;
                                }
                            }
                        }

                    }

                } catch (\Exception $exception) {
                    Log::info($exception);
                    return json_encode([
                        'status' => 1,
                    ]);
                }
            }
        }
        return 'success';
    }

    public function jd_notify_url(Request $request)
    {
        $response = $request->all();
        $check = $this->Check($request->all());
        Log::info('j_' . $check);
        if ($check) {
            //改变状态数据库的状态
            $data = $request->all();
            $Order = Order::where('out_trade_no', $data['out_trade_no'])->first();
            //通过接口查状态
            $aop = $this->AopClient();
            $aop->method = "fshows.liquidation.alipay.trade.query";
            $payStatus = [
                'trade_no' => $data['trade_no']
            ];
            $dataStatus = array('content' => json_encode($payStatus));
            try {
                $response = $aop->execute($dataStatus);
                $responseArray = json_decode($response, true);
            } catch (\Exception $exception) {
                Log::info($exception);
            }
            //如果状态不相同修改数据库状态
            $acptstatus = $responseArray['return_value']['status'];

            if ($Order->status != $acptstatus) {
                Order::where('out_trade_no', $data['out_trade_no'])->update([
                    'status' => $acptstatus,
                    'pay_status' => $this->pay_status($acptstatus),
//                    'total_amount' => $responseArray['return_value']['total_fee'],
                ]);
//                微信通知商户收营员
                try {
                    //店铺通知微信
                    if ($responseArray['return_value']['status'] == '2') {


                        $store_id = $Order->store_id;
                        //安卓app语音播报
                        $jpush=new JpushController();
                        $jpush->push(''.$data['out_trade_no'].'-京东',$responseArray['return_value']['total_fee'],$data['out_trade_no']);
                        $this->printY($store_id, $Order, '平安京东');
                        //U打印
                        $this->printU($store_id, $Order, "平安京东");

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
//                        $user = $userService->get($responseArray['return_value']['open_id']);//买家open_id
                        $template = PageSets::where('id', 1)->first();
                        $notice = $app->notice;
                        $userIds = $WeixinPayNotifyStore->receiver;
                        $open_ids = explode(",", $userIds);
                        $templateId = $template->string1;
                        $url = $WeixinPayNotifyStore->linkTo;
                        $color = $WeixinPayNotifyStore->topColor;
                        $andData = array(
                            "keyword1" => $Order->total_amount,
                            "keyword2" => '京东(' . $responseArray['return_value']['user'] . ')',
                            "keyword3" => '' . $Order->updated_at . '',
                            "keyword4" => $responseArray['return_value']['trade_no'],
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
                                    'open_id' => $v,
                                ]);
                                try {
                                    $notice->uses($templateId)->withUrl($url)->andData($andData)->andReceiver($v)->send();
                                } catch (\Exception $exception) {
                                    Log::info($exception);
                                    continue;
                                }
                            }
                        }

                    }

                } catch (\Exception $exception) {
                    Log::info($exception);
                    return json_encode([
                        'status' => 1,
                    ]);
                }
            }

        }
        return 'success';

    }

    //机具
    public function notify_url_m1(Request $request)
    {
        $check = $this->Check($request->all());
        if ($check) {
            //改变状态数据库的状态
            $data = $request->all();
            $PinganTradeQuery = Order::where('out_trade_no', $data['out_trade_no'])->first();
            //如果状态不相同修改数据库状态
            if ($PinganTradeQuery->status != $data['trade_status']) {
                Order::where('out_trade_no', $data['out_trade_no'])->update([
                    'status' => $data['trade_status'],
                    'total_amount' => $data['total_amount'],
                    "trade_no" => $data['trade_no']
                ]);
            }
        }
        return 'success';
    }
    //支付宝异步通知
    public function notify_url(Request $request)
    {
        $check = $this->Check($request->all());
        Log::info('A_' . $check);
        if ($check) {
            //改变状态数据库的状态
            $data = $request->all();
            $Order = Order::where('out_trade_no', $data['out_trade_no'])->first();
            //如果状态不相同修改数据库状态
            if ($Order->status != $data['trade_status']) {
                Order::where('out_trade_no', $data['out_trade_no'])->update([
                    'status' => $data['trade_status'],
//                    'total_amount' => $data['total_amount'],
                    'pay_status' => $this->pay_status($data['trade_status'])
                ]);
                //微信通知商户收营员
                try {
                    //店铺通知微信
                    if ($data['trade_status'] == 'TRADE_SUCCESS') {
                        $liquidator_commission_fee=$data['total_amount']-$data['net_income']-$data['pay_platform_rate']*$data['total_amount']-$data['bank_commission_rate']*$data['total_amount'];
//                        $liquidator_commission_fee=$data['total_amount']*$data['liquidator_commission_rate'];
                        $liquidator_commission_fee=round($liquidator_commission_fee, 2);
                        //计入分润
                        if($liquidator_commission_fee>0){
                            $cmd=New UserProfitController();
                            $res=$cmd->orderToprofit($Order->id,$liquidator_commission_fee,($data['pay_platform_rate']+$data['bank_commission_rate'])*100,($data['pay_platform_rate']+$data['bank_commission_rate']+$data['liquidator_commission_rate'])*100);
                            $res=json_decode($res,true);
                            if($res['code']!=1){
                                Log::info($data);
                                Log::info('分润+++++++++++++++++++');
                                Log::info($data['pay_platform_rate']." ".$data['bank_commission_rate']." ".$data['liquidator_commission_rate']);
                                Log::info($res['msg']);
                            }
                        }
                        $store_id = $Order->store_id;
                        //安卓app语音播报
                        $jpush=new JpushController();
                        $jpush->push(''.$data['out_trade_no'].'-支付宝',$data['total_amount'],$data['out_trade_no']);
                        $this->printY($store_id, $Order, '平安支付宝');
                        //U打印
                        $this->printU($store_id, $Order, "平安支付宝");
                        //微信提醒
                        $WeixinPayNotifyStore = WeixinPayNotify::where('store_id', $store_id)->first();
                        //实例化
                        $config = WeixinPayConfig::where('id', 1)->first();
                        if ($WeixinPayNotifyStore && $config) {
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
                            if($Order&&!empty($Order->remark)){
                                $markstr.='(备注:'.$Order->remark.')';
                            }
                            $data = array(
                                "keyword1" => $Order->total_amount,
                                "keyword2" => '支付宝(' . $data['buyer_logon_id'] . ')'.$markstr,
                                "keyword3" => '' . $Order->updated_at . '',
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

                } catch (\Exception $exception) {
                    Log::info($exception);
                    return json_encode([
                        'status' => 1,
                    ]);
                }
            }

        }

        return 'success';

    }
    //机具支付宝异步通知
    public function notify_url_m(Request $request)
    {
        $check = $this->Check($request->all());
        Log::info('A_' . $check);
        if ($check) {
            //改变状态数据库的状态
            $data = $request->all();
            $Order = Order::where('out_trade_no', $data['out_trade_no'])->first();
            //如果状态不相同修改数据库状态
            if ($Order->status != $data['trade_status']) {
                Order::where('out_trade_no', $data['out_trade_no'])->update([
                    'status' => $data['trade_status'],
//                    'total_amount' => $data['total_amount'],
                    'pay_status' => $this->pay_status($data['trade_status'])
                ]);
                //微信通知商户收营员
                try {
                    //店铺通知微信
                    if ($data['trade_status'] == 'TRADE_SUCCESS') {
                        $liquidator_commission_fee=$data['total_amount']-$data['net_income']-$data['pay_platform_rate']*$data['total_amount']-$data['bank_commission_rate']*$data['total_amount'];
//                        $liquidator_commission_fee=$data['total_amount']*$data['liquidator_commission_rate'];
                        $liquidator_commission_fee=round($liquidator_commission_fee, 2);
                        //计入分润
                        if($liquidator_commission_fee>0){
                            $cmd=New UserProfitController();
                            $res=$cmd->orderToprofit($Order->id,$liquidator_commission_fee,($data['pay_platform_rate']+$data['bank_commission_rate'])*100,($data['pay_platform_rate']+$data['bank_commission_rate']+$data['liquidator_commission_rate'])*100);
                            $res=json_decode($res,true);
                            if($res['code']!=1){
                                Log::info($data);
                                Log::info('分润+++++++++++++++++++');
                                Log::info($data['pay_platform_rate']." ".$data['bank_commission_rate']." ".$data['liquidator_commission_rate']);
                                Log::info($res['msg']);
                            }
                        }
                        $store_id = $Order->store_id;
                        //安卓app语音播报
                        //  $jpush=new JpushController();
                        //  $jpush->push(''.$data['out_trade_no'].'-支付宝',$data['total_amount'],$data['out_trade_no']);
                        $this->printY($store_id, $Order, '平安支付宝');
                        //U打印
                        $this->printU($store_id, $Order, "平安支付宝");
                        //微信提醒
                        $WeixinPayNotifyStore = WeixinPayNotify::where('store_id', $store_id)->first();
                        //实例化
                        $config = WeixinPayConfig::where('id', 1)->first();
                        if ($WeixinPayNotifyStore && $config) {
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
                            if($Order&&!empty($Order->remark)){
                                $markstr.='(备注:'.$Order->remark.')';
                            }
                            $data = array(
                                "keyword1" => $Order->total_amount,
                                "keyword2" => '支付宝(' . $data['buyer_logon_id'] . ')'.$markstr,
                                "keyword3" => '' . $Order->updated_at . '',
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

                } catch (\Exception $exception) {
                    Log::info($exception);
                    return json_encode([
                        'status' => 1,
                    ]);
                }
            }

        }

        return 'success';

    }

    //微信异步通知
    public function wx_notify_url(Request $request)
    {
        $check = $this->Check($request->all());
        if ($check) {
            //改变状态数据库的状态
            $data = $request->all();
            Log::info($data);
            $Order = Order::where('out_trade_no', $data['out_trade_no'])->first();
            //通过接口查状态
            $aop = $this->AopClient();
            $aop->method = "fshows.liquidation.alipay.trade.query";
            $payStatus = [
                'trade_no' => $data['transaction_id']
            ];
            $dataStatus = array('content' => json_encode($payStatus));
            try {
                $response = $aop->execute($dataStatus);
                $responseArray = json_decode($response, true);
            } catch (\Exception $exception) {
                Log::info($exception);
            }
            //如果状态不相同修改数据库状态
            if ($Order->status != $responseArray['return_value']['trade_state']) {
                Order::where('out_trade_no', $data['out_trade_no'])->update([
                    'trade_no' => $data['transaction_id'],
                    'status' => $responseArray['return_value']['trade_state'],
                    'pay_status' => $this->pay_wx_status($responseArray['return_value']['trade_state']),
//                    'total_amount' => $responseArray['return_value']['total_fee'],
                ]);
                //微信通知商户收营员
                try {
                    //店铺通知微信
                    if ($responseArray['return_value']['trade_state'] == 'SUCCESS') {
                        $liquidator_commission_fee=$data['total_fee']-$data['net_money']-$data['pay_platform_rate']*$data['total_fee']-$data['bank_commission_rate']*$data['total_fee'];
//                        $liquidator_commission_fee=$data['total_amount']*$data['liquidator_commission_rate'];
                        $liquidator_commission_fee=round($liquidator_commission_fee, 2);
                        //计入分润
                        if($liquidator_commission_fee>0){
                            $cmd=New UserProfitController();
                            $res=$cmd->orderToprofit($Order->id,$liquidator_commission_fee,($data['pay_platform_rate']+$data['bank_commission_rate'])*100,($data['pay_platform_rate']+$data['bank_commission_rate']+$data['liquidator_commission_rate'])*100);
                            $res=json_decode($res,true);
                            if($res['code']!=1){
                                Log::info($data);
                                Log::info('分润+++++++++++++++++++');
                                Log::info($data['pay_platform_rate']." ".$data['bank_commission_rate']." ".$data['liquidator_commission_rate']);
                                Log::info($res['msg']);
                            }
                        }
                        $store_id = $Order->store_id;
                        //安卓app语音播报
                        $jpush=new JpushController();
                        $jpush->push(''.$data['out_trade_no'].'-微信',$responseArray['return_value']['total_fee'],$data['out_trade_no']);
                        //易联云打印
                        $this->printY($store_id, $Order, '平安微信');
                        //U打印
                        $this->printU($store_id, $Order, "平安微信");
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
                        // $userService = $app->user;
                        //  $user = $userService->get($responseArray['return_value']['openid']);//买家open_id
                        $template = PageSets::where('id', 1)->first();
                        $notice = $app->notice;
                        $userIds = $WeixinPayNotifyStore->receiver;
                        $open_ids = explode(",", $userIds);
                        $templateId = $template->string1;
                        $url = $WeixinPayNotifyStore->linkTo;
                        $color = $WeixinPayNotifyStore->topColor;

                        $markstr='';
                        if($Order&&!empty($Order->remark)){
                            $markstr.='(备注:'.$Order->remark.')';
                        }
                        $andData = array(
                            "keyword1" => $Order->total_amount,
                            "keyword2" => '微信支付'/* . $user->nickname . ')'*/.$markstr,
                            "keyword3" => '' . $Order->updated_at . '',
                            "keyword4" => $responseArray['return_value']['trade_no'],
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
                                    'open_id' => $v,
                                ]);
                                try {
                                    $notice->uses($templateId)->withUrl($url)->andData($andData)->andReceiver($v)->send();
                                } catch (\Exception $exception) {
                                    Log::info($exception);
                                    continue;
                                }
                            }
                        }
                    }

                } catch (\Exception $exception) {
                    Log::info($exception);
                    return json_encode([
                        'status' => 1,
                    ]);
                }
            }

        }
        return 'success';

    }

    public function Check($request)
    {
        $config = PinganConfig::where('id', 1)->first();
        //支付异步通知
        $a = $this->AopClient();
        $aop = new AopClient();
        $aop->appId = $a->appId;
        $aop->rsaPrivateKey = $a->rsaPrivateKey;
        $aop->gatewayUrl = $a->gatewayUrl;
        $aop->signType = 'RSA';
        $aop->alipayrsaPublicKey = $config->pinganrsaPublicKey;
        $true = $aop->rsaCheckUmxnt($request, $config->pinganrsaPublicKey, 'RSA');
        return $true;
    }

    public function pay_status($status)
    {
        if ($status == "TRADE_SUCCESS" || $status == "2" || $status == "B") {
            $pay_status = 1;
        } elseif ($status == "TRADE_CLOSED" || $status == "9" || $status == "G") {
            $pay_status = 4;
        } elseif ($status == "WAIT_BUYER_PAY" || $status == "1") {
            $pay_status = 3;
        } else {
            $pay_status = 2;
        }
        return $pay_status;
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

    public function printY($store_id, $Order, $type)
    {
        try {
            $push = new \App\Http\Controllers\Push\AopClient();
            $PushConfig = PushConfig::where('id', 1)->first();
            $push_store = PushPrintShopList::where('store_id', $store_id)->where("type", "yilianyun")->get();
            $info = PushPrintShopList::where('store_id', $store_id)->where("type", "yilianyun")->first();
            if ($push_store) {
                $store = PinganStore::where('external_id', $store_id)->first();
                $store_name = $store->alias_name;
                $phone = $info->phone;
                $str = "<center>" . $store_name . "</center>
            <center>联系方式:" . $phone . "</center>
订单号:" . $Order->out_trade_no . "
付款金额:" . $Order->total_amount . "
付款用户:" . $type . "
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
            $store = PinganStore::where('external_id', $store_id)->first();
            if($store){
                $store_name = $store->alias_name;
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