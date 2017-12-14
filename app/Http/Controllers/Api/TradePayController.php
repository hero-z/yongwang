<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/5
 * Time: 17:39
 */

namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Support\Facades\Auth;
use Alipayopen\Sdk\Request\AlipayTradePayRequest;
use App\Http\Controllers\AlipayOpen\AlipayOpenController;
use App\Http\Controllers\PingAn\BaseController;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayShopLists;
use App\Models\MerchantOrders;
use App\Models\MerchantPayWay;
use App\Models\MerchantShops;
use App\Models\PinganConfig;
use App\Models\PinganStore;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TradePayController extends AlipayOpenController
{
    //判断授权码类型
    public function TradePay(Request $request)
    {
        $_token=$request->get('_token');
        if ($_token!=csrf_token()){
            return json_encode([
                'status' => 4003,
                'msg' => '没有权限调用接口'
            ]);
        }
        //$m_id = auth()->guard('merchant')->user()->id;
        $m_id = $request->get('merchant_id');//商户id
        $code = $request->get('code');
        $data = $request->except(['_token']);
        //支付宝28微信13QQ91京东18正常都是18位，支付宝早期有密支付是17位，银联新版19位62打头
        $str = substr($code, 0, 2);
        //判断通道 这里需要在表里设置 切换通道
        $MerchantPayWay = MerchantPayWay::where('merchant_id', $m_id)->first();
        $configs = AlipayIsvConfig::where('id', 1)->first();
        $store = MerchantShops::where('merchant_id', $m_id)->first();
        if (!$store) {
            $msg = '账号没有绑定任何店铺相关数据！请联系服务商开通店铺！';
            return json_encode([
                'status' => 4004,
                'msg' => $msg
            ]);
        }
        //判断通道
        if (!$MerchantPayWay || !$MerchantPayWay->alipay) {
            $msg = '没有设置收款通道！请设置收款通道';
            return json_encode([
                'status' => 4004,
                'msg' => $msg
            ]);
        }

        /****************支付宝 开始*******************/
        if ($str == "28") {
            //支付宝官方
            if ($MerchantPayWay->alipay == "oAlipay" || $MerchantPayWay->alipay == "sAlipay") {
                $aop = $this->AopClient();
                $aop->apiVersion = "2.0";
                $aop->method = 'alipay.trade.pay';
                $aop->notify_url = url('/notify_m');
                $requests = new AlipayTradePayRequest();
                $requests->setNotifyUrl(url('/notify_m'));
                if ($MerchantPayWay->alipay == "oAlipay") {
                    $type = "moalipay";
                    //仅当面付
                    $store = MerchantShops::where('merchant_id', $m_id)->where('store_type', 'oAlipay')->first();
                    $storeInfo = AlipayAppOauthUsers::where('store_id', $store->store_id)->first();
                    if (!$storeInfo) {
                        $msg = '商户信息不存在！请重新授权！';
                        return json_encode([
                            'status' => 4004,
                            'msg' => $msg
                        ]);
                    }
                    $desc = $request->get('desc', $storeInfo->auth_shop_name . "机具收款");
                    $out_trade_no = "mo" . date('YmdHis', time()) . rand(10000, 99999);
                    //提交到口碑
                    $requests->setBizContent("{" .
                        "    \"out_trade_no\":\"" . $out_trade_no . "\"," .
                        "    \"scene\":\"bar_code\"," .
                        "    \"auth_code\":\"" . $data['code'] . "\"," .
                        "    \"subject\":\"" . $desc . "\"," .
                        "    \"total_amount\":" . $data['price'] . "," .
                        "    \"timeout_express\":\"90m\"," .
                        "    \"body\":\"" . $desc . "\"," .
                        "      \"goods_detail\":[{" .
                        "        \"goods_id\":\"" . $store->store_id . "\"," .
                        "        \"goods_name\":\"" . $desc . "\"," .
                        "        \"quantity\":1," .
                        "        \"price\":" . $data['price'] . "," .
                        "        \"body\":\"" . $desc . "\"" .
                        "        }]," .
                        "    \"store_id\":\"" . $store->store_id . "\"," .
                        "    \"extend_params\":{" .
                        "      \"sys_service_provider_id\":\"" . $configs->pid . "\"" .
                        "}" .
                        "  }");
                }
                //门店付款
                if ($MerchantPayWay->alipay == "sAlipay") {
                    $type = "msalipay";
                    $store = MerchantShops::where('merchant_id', $m_id)->where('store_type', 'sAlipay')->first();
                    $storeInfo = AlipayShopLists::where('store_id', $store->store_id)->first();
                    if (!$storeInfo) {
                        $msg = '商户信息不存在！请联系服务商！';
                        return json_encode([
                            'status' => 4004,
                            'msg' => $msg
                        ]);
                    }
                    $desc = $request->get('desc', $storeInfo->main_shop_name . "机具收款");
                    $out_trade_no = "ms" . date('YmdHis', time()) . rand(10000, 99999);
                    //提交到口碑
                    $requests->setBizContent("{" .
                        "    \"out_trade_no\":\"" . $out_trade_no . "\"," .
                        "    \"scene\":\"bar_code\"," .
                        "    \"auth_code\":\"" . $data['code'] . "\"," .
                        "    \"subject\":\"" . $desc . "\"," .
                        "    \"total_amount\":" . $data['price'] . "," .
                        "    \"timeout_express\":\"90m\"," .
                        "    \"alipay_store_id\":\"" . $storeInfo->shop_id . "\"," .
                        "    \"body\":\"" . $desc . "\"," .
                        "      \"goods_detail\":[{" .
                        "        \"goods_id\":\"" . $store->store_id . "\"," .
                        "        \"goods_name\":\"" . $desc . "\"," .
                        "        \"quantity\":1," .
                        "        \"price\":" . $data['price'] . "," .
                        "        \"body\":\"" . $desc . "\"" .
                        "        }]," .
                        "    \"store_id\":\"" . $store->store_id . "\"," .
                        "    \"extend_params\":{" .
                        "      \"sys_service_provider_id\":\"" . $configs->pid . "\"" .
                        "}" .
                        "  }");
                }

                try {
                    $result = $aop->execute($requests, null, $storeInfo->app_auth_token);
                    $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
                    $resultCode = $result->$responseNode->code;
                    if (!empty($resultCode) && $resultCode == 10000) {//10003 正在输入密码
                        $price = $result->$responseNode->total_amount;
                        $pay_user = $result->$responseNode->buyer_logon_id;
                        MerchantOrders::create([
                            'out_trade_no' => $out_trade_no,
                            'trade_no' => $result->$responseNode->trade_no,
                            'store_id' => $store->store_id,
                            'total_amount' => $result->$responseNode->total_amount,
                            'status' => 'TRADE_SUCCESS',
                            'merchant_id' => $m_id,
                            'pay_ways' => 'alipay',
                            'type' => $type
                        ]);
                        //  return view('merchant.page.success', compact('price', 'pay_user'));
                        return json_encode([
                            'status' => 1,
                            'msg' => '订单支付成功'
                        ]);
                    } else {
                        MerchantOrders::create([
                            'out_trade_no' => $out_trade_no,
                            'trade_no' => '',
                            'store_id' => $store->store_id,
                            'total_amount' => $data['price'],
                            'status' => $result->$responseNode->code,
                            'merchant_id' => $m_id,
                            'pay_ways' => 'alipay',
                            'type' => $type
                        ]);
                        //正在支付
                        if (!empty($resultCode) && $resultCode == 10003) {
                            sleep(5);
                            $status = $this->TradePayQuery($out_trade_no);
                            if ($status == "TRADE_SUCCESS") {
                                return json_encode([
                                    'status' => 1,
                                    'msg' => '订单支付成功'
                                ]);
                            } else {
                                sleep(5);
                                $status = $this->TradePayQuery($out_trade_no);
                            }

                            return json_encode([
                                'status' => $status,
                                'msg' => '支付失败！请重新再试' . $status,
                            ]);

                        } else {
                            $msg = $result->$responseNode->sub_msg;//错误信息
                            return json_encode([
                                'status' => $result->$responseNode->sub_code,
                                'msg' => $msg,
                            ]);
                        }
                    }
                } catch (\Exception $exception) {
                    return json_encode([
                        'status' => 0,
                        'msg' => '系统异常请重新再试123！'
                    ]);
                }
            }

            //支付宝平安
            if ($MerchantPayWay->alipay == "pingan") {
                $out_trade_no = "mpa" . date('YmdHis', time()) . rand(10000, 99999);
                $store = MerchantShops::where('merchant_id', $m_id)->where('store_type', 'pingan')->first();
                $storeInfo = PinganStore::where('external_id', $store->store_id)->first();
                $ao = new BaseController();
                $aop = $ao->AopClient();
                $aop->method = "fshows.liquidation.submerchant.alipay.trade.pay";
                $pay = [
                    'out_trade_no' => $out_trade_no,
                    'notify_url' => url('/admin/pingan/notify_url_m'),
                    'scene' => 'bar_code',
                    'auth_code' => $data['code'],
                    'total_amount' => $data['price'],
                    'subject' => $request->get('desc') . '门店收款',
                    'body' => $request->get('desc') . '门店收款信息',
                    'sub_merchant' => [
                        'merchant_id' => $storeInfo->sub_merchant_id
                    ],
                ];
                $dataAop = array('content' => json_encode($pay));
                try {
                    $response = $aop->execute($dataAop);
                    $responseArray = json_decode($response, true);
                    //保存数据库
                    if ($responseArray['success']) {
                        $price = $responseArray['return_value']['totalAmount'];
                        $pay_user = $responseArray['return_value']['buyerLogonId'];
                        $insert = [
                            'trade_no' => $responseArray['return_value']['tradeNo'],
                            "out_trade_no" => $responseArray['return_value']['outTradeNo'],
                            'store_id' => $store->store_id,
                            'total_amount' => $price,
                            'status' => 'SUCCESS',
                            'merchant_id' => $m_id,
                            'pay_ways' => 'pingan',
                            'type' => 'mpalipay'
                        ];
                        MerchantOrders::create($insert);
                        return json_encode([
                            'status' => 1,
                            'msg' => '订单支付成功'
                        ]);
                    } else {
                        $insert1 = [
                            'trade_no' => "",
                            "out_trade_no" => $out_trade_no,
                            'store_id' => $store->store_id,
                            'total_amount' => $data['price'],
                            'status' => '',
                            'merchant_id' => $m_id,
                            'pay_ways' => 'pingan',
                            'type' => 'mpalipay'
                        ];
                        MerchantOrders::create($insert1);
                        //暂停10秒
                        sleep(5);
                        $status = $this->WxPOrderStatus($out_trade_no);
                        if ($status['return_value']['trade_status'] == "TRADE_SUCCESS") {
                            return json_encode([
                                'status' => 1,
                                'msg' => '订单支付成功'
                            ]);
                        } else {
                            sleep(5);
                            $status = $this->WxPOrderStatus($out_trade_no);
                        }
                        if ($status['return_value']['trade_status'] == "TRADE_SUCCESS") {
                            return json_encode([
                                'status' => 1,
                                'msg' => '订单支付成功'
                            ]);
                        }
                        return json_encode([
                            'status' => $status['return_value']['trade_status'],
                            'msg' => '支付失败!请重新支付'
                        ]);
                    }
                } catch (\Exception $exception) {
                    Log::info($exception);
                    return json_encode([
                        'status' => 0,
                        'msg' => '系统异常请重新再试456！'
                    ]);
                }
            }

        }
        /****************支付宝结束*******************/


        /****************微信 开始*******************/

        if ($str == "13") {
            //官方的微信
            if ($MerchantPayWay->weixin == "weixin") {
                $weixin = new \App\Http\Controllers\Weixin\BaseController();
                $options = $weixin->Options();
                $store = MerchantShops::where('merchant_id', $m_id)->where('store_type', 'weixin')->first();
                $options['payment']['sub_merchant_id'] = substr($store->store_id, 1);//微信子商户号
                $out_trade_no = 'mw' . date('Ymdhis', time()) . rand(10000, 99999);//订单号
                $total_fee = (int)($data['price'] * 100);//金额
                $app = new Application($options);
                $payment = $app->payment;
                $attributes = [
                    // 'trade_type' => 'MICROPAY', // JSAPI，NATIVE，APP...
                    'body' => $request->get('desc') . '商家收款',
                    'detail' => $request->get('desc') . '商家收款',
                    'out_trade_no' => $out_trade_no,
                    'total_fee' => $total_fee,
                    'spbill_create_ip' => $_SERVER['SERVER_ADDR'],
                    'auth_code' => $data['code']
                ];
                $order = new Order($attributes);
                $result = $payment->pay($order);
                if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
                    $insertW = [
                        'trade_no' => $result->transaction_id,
                        "out_trade_no" => $out_trade_no,
                        'store_id' => $store->store_id,
                        'total_amount' => $data['price'],
                        'status' => 'SUCCESS',
                        'merchant_id' => $m_id,
                        'pay_ways' => 'weixin',
                        'type' => 'mweixin'
                    ];
                    MerchantOrders::create($insertW);
                    return json_encode([
                        'status' => 1,
                        'msg' => '订单支付成功'
                    ]);
                } else {
                    //用户正在输入密码
                    if ($result->err_code == "USERPAYING") {
                        //暂停10秒
                        sleep(5);
                        $status = $this->WxOrderStatus($out_trade_no);
                        if ($status->trade_state == 'USERPAYING') {
                            sleep(5);//再过5秒
                            $status = $this->WxOrderStatus($out_trade_no);
                        }
                        //  dd($status);
                        if ($status->trade_state == 'SUCCESS') {
                            return json_encode([
                                'status' => 1,
                                'msg' => '订单支付成功'
                            ]);
                        }
                        return json_encode([
                            'status' => $status->trade_state,
                            'msg' => $status->trade_state_desc
                        ]);

                    } else {
                        $msg = $result->err_code_des;//错误信息
                        return json_encode([
                            'status' => 4004,
                            'msg' => $msg,
                        ]);
                    }
                }

            }
            //平安的微信
            if ($MerchantPayWay->weixin == "pingan") {
                $out_trade_no = "mpw" . date('YmdHis', time()) . rand(10000, 99999);
                $store = MerchantShops::where('merchant_id', $m_id)->where('store_type', 'pingan')->first();
                $storeInfo = PinganStore::where('external_id', $store->store_id)->first();
                $ao = new BaseController();
                $aop = $ao->AopClient();
                $aop->method = "fshows.liquidation.wx.trade.pay";
                $pay = [
                    'out_trade_no' => $out_trade_no,
                    'body' => $request->get('desc') . '门店收款信息',
                    'total_fee' => $data['price'],
                    'spbill_create_ip' => $_SERVER['SERVER_ADDR'],
                    'auth_code' => $data['code'],
                    'store_id' => $storeInfo->sub_merchant_id
                ];
                $dataWAop = array('content' => json_encode($pay));
                try {
                    $response = $aop->execute($dataWAop);
                    $responseArray = json_decode($response, true);
                    //保存数据库
                    if ($responseArray['success']) {
                        $price = $responseArray['return_value']['total_fee'];
                        $insertPW = [
                            'trade_no' => $responseArray['return_value']['transaction_id'],
                            "out_trade_no" => $responseArray['return_value']['out_trade_no'],
                            'store_id' => $store->store_id,
                            'total_amount' => $price,
                            'status' => 'SUCCESS',
                            'merchant_id' => $m_id,
                            'pay_ways' => 'pingan',
                            'type' => 'mpweixin'
                        ];
                        MerchantOrders::create($insertPW);
                        return json_encode([
                            'status' => 1,
                            'msg' => '订单支付成功'
                        ]);
                    } else {
                        $insertPW = [
                            'trade_no' => "",
                            "out_trade_no" => $out_trade_no,
                            'store_id' => $store->store_id,
                            'total_amount' => $data['price'],
                            'status' => '',
                            'merchant_id' => $m_id,
                            'pay_ways' => 'pingan',
                            'type' => 'mpweixin'
                        ];
                        MerchantOrders::create($insertPW);
                        //暂停10秒
                        sleep(5);
                        $status = $this->WxPOrderStatus($out_trade_no);
                        if ($status['return_value']['trade_state'] == 'USERPAYING') {
                            sleep(5);//再过5秒
                            $status = $this->WxPOrderStatus($out_trade_no);
                        }
                        //  dd($status);
                        if ($status['return_value']['trade_state'] == "SUCCESS") {
                            return json_encode([
                                'status' => 1,
                                'msg' => '订单支付成功'
                            ]);
                        }
                        return json_encode([
                            'status' => $status['return_value']['trade_state'],
                            'msg' => $status['return_value']['trade_state_desc']
                        ]);
                    }
                } catch (\Exception $exception) {
                    Log::info($exception);
                    return json_encode([
                        'status' => 0,
                        'msg' => '系统异常请重新再试567！'
                    ]);
                }

            }
        }
        /****************微信支付 结束*******************/

    }

    //查询订单
    public function TradePayQuery($out_trade_no)
    {
        $m = MerchantOrders::where('out_trade_no', $out_trade_no)->first();

        return $m->status;

    }

    //微信官方的订单查询
    public function WxOrderStatus($out_trade_no)
    {
        $m_id = auth()->guard('merchant')->user()->id;
        $weixin = new \App\Http\Controllers\Weixin\BaseController();
        $options = $weixin->Options();
        $store = MerchantShops::where('merchant_id', $m_id)->where('store_type', 'weixin')->first();
        $options['payment']['sub_merchant_id'] = $store->store_id;//微信子商户号
        $app = new Application($options);
        $payment = $app->payment;
        return $payment->query($out_trade_no);
    }

    //平安的查询接口
    public function WxPOrderStatus($out_trade_no)
    {
        $ao = new BaseController();
        $aop = $ao->AopClient();
        $aop->method = "fshows.liquidation.alipay.trade.query";
        $pay = [
            'out_trade_no' => $out_trade_no,
        ];
        $dataWAop = array('content' => json_encode($pay));
        $response = $aop->execute($dataWAop);
        return $responseArray = json_decode($response, true);
    }

    //收单
    public function AlipayTradePayCreate()
    {
        $m_id = auth()->guard('merchant')->user()->id;
        $store = MerchantShops::where('merchant_id', $m_id)->first();
        $MerchantPayWay = MerchantPayWay::where('merchant_id', $m_id)->first();
        if ($MerchantPayWay) {
            $store_name = $store->store_name;
        } else {
            $store_name = '你还没有绑定店铺无法';
        }
        $list = DB::table("merchant_orders")
            ->where("merchant_id", auth()->guard('merchant')->user()->id)
            ->orderBy('updated_at', 'desc')
            ->where("type", "moalipay")
            ->orwhere("type", "msalipay")
            ->orwhere("type", "mweixin")
            ->orwhere("type", "mpalipay")
            ->orwhere("type", "mpweixin")
            ->paginate(5);
        return view('merchant.Alipay.createTrade', compact('store_name', "list", 'MerchantPayWay'));
    }

    public function AlipayTradePayCreatePost($data)
    {


    }

}