<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/19
 * Time: 12:48
 */

namespace App\Api\Controllers\Merchant;


use App\Api\Transformers\MerchantOrderTransformer;
use App\Http\Controllers\AlipayOpen\AlipayOpenController;
use Alipayopen\Sdk\Request\AlipayTradePayRequest;
// use App\Http\Controllers\Api\BaseController;
use App\Models\MerchantOrders;
use App\Models\MerchantShops;
use App\Models\MerchantPayWay;
use App\Models\MerchantPos;
use JWTAuth;
use App\Models\PinganStore;
use App\Http\Controllers\PingAn\BaseController;
use App\Models\AlipayAppOauthUsers;
use Illuminate\Http\Request;
use App\Models\AlipayIsvConfig;
use Illuminate\Support\Facades\Log;
use App\Models\AlipayShopLists;
use App\Models\PinganConfig;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;

use Illuminate\Support\Facades\DB;


class MerchantOrderController extends BaseController
{

    /*
        生成签名

    */
    private function makesign($data)
    {
        ksort($data);
        $signstr=md5(http_build_query($data));
        return $signstr;
    }

    /*
        验证pos传过来的参数及签名
        通过签名返回false
        签名错误返回true
    */
    private function verifypostdata($postdata)
    {
        ksort($postdata);
        $truesign=$postdata['sign'];
        unset($postdata['sign']);
        $signstr=md5(http_build_query($postdata));
        // 提交数据验证签名成功
        if($truesign===$signstr)
        {
            return false;
        }
        // 验签失败
        return true;
    }

    /*
        返回json字符串

    */
    private function handlepostdata($poststr)
    {
        $poststr=array_keys($poststr);
        $poststr=array_filter($poststr);
        $poststr=array_shift($poststr);
        return $poststr;
    }

    // pos机扫码支付--对应文档5.5
    public function index(Request $request)
    {
/*        $date=date('Y-m-d H:i:s');
        file_put_contents('./chencai_log.txt', "\n\n".$date."\n".var_export($request->all(),true)."\n",FILE_APPEND);
        echo 'log is writted'.date('Y-m-d H:i:s');
        die;*/


        // echo '<pre>';
        // 1 接收pos机发送的数据，验签不通过的直接不处理
/*        $postarr=[
                "sign"=>$request->get('sign'),
                "service"=>$request->get('service'),
                "deviceid"=>$request->get('deviceid'),//商户机器识别号，一个商户可以有多台设备
                "operator"=>$request->get('operator'),//操作员
                "amount"=>$request->get('amount'),//交易金额，单位：分
                "code"=>$request->get('code'),//第三方交易码
        ];
        $poststr=json_encode($postarr);
*/
        $poststr=$request->all();
        $poststr=$this->handlepostdata($poststr);

// echo $poststr;die;

        $postdata=$poststr ? json_decode($poststr,true) : '';
        if(empty($postdata))
        {
            $return = [
                'service'=>'',
                'deviceid'=>'',
                'operator'=>'',
                'result'=>9511,
                'message'=>'未收到参数',
                'order'=>'',
            ];
            $return['sign']=$this->makesign($return);
            return json_encode($return);
        }


        $data=[
            'code'=>$postdata['code'],
            'price'=>$postdata['amount']?sprintf('%0.2f',$postdata['amount']/100):0,
        ];

        // 2 验证签名
        if($this->verifypostdata($postdata))
        {
            $return = [
                'service'=>$postdata['service'],
                'deviceid'=>$postdata['deviceid'],
                'operator'=>$postdata['operator'],
                'result'=>9501,
                'message'=>'验证签名失败',
                'order'=>'',
            ];
            $return['sign']=$this->makesign($return);
            return json_encode($return);
            // exit('验证签名失败');
        }

        if(empty($data['price']))
        {
            $return = [
                'service'=>$postdata['service'],
                'deviceid'=>$postdata['deviceid'],
                'operator'=>$postdata['operator'],
                'result'=>9502,
                'message'=>'交易金额不能为空！',
                'order'=>'',
            ];
            $return['sign']=$this->makesign($return);
            return json_encode($return);
        }

        if(empty($data['code']))
        {
            $return = [
                'service'=>$postdata['service'],
                'deviceid'=>$postdata['deviceid'],
                'operator'=>$postdata['operator'],
                'result'=>9503,
                'message'=>'设备号不能为空！',
                'order'=>'',
            ];
            $return['sign']=$this->makesign($return);
            return json_encode($return);
            // exit('设备号不能为空！');
        }

        // 3 分析第三方交易码，确定使用服务商的支付通道
        //支付宝28微信13QQ91京东18正常都是18位，支付宝早期有密支付是17位，银联新版19位62打头
        $str = substr($postdata['code'], 0, 2);
        //判断通道 这里需要在表里设置 切换通道
        $configs = AlipayIsvConfig::where('id', 1)->first();
        //获取服务商id
        $MerchantPos = MerchantPos::where('poscode', $postdata['deviceid'])->first()->toArray();
        if(empty($MerchantPos)||empty($MerchantPos['poscode']))
        {
            $return = [
                'service'=>$postdata['service'],
                'deviceid'=>$postdata['deviceid'],
                'operator'=>$postdata['operator'],
                'result'=>9504,
                'message'=>'该门店没有pos机设备',
                'order'=>'',
            ];
            $return['sign']=$this->makesign($return);
            return json_encode($return);
            // exit('该门店没有pos机设备');
        }
        $m_id=$MerchantPos['merchant_id'];
        $store = MerchantShops::where('merchant_id', $m_id)->first()->toArray();
        // print_r($store);die;
        if (!$store) {
            $return = [
                'service'=>$postdata['service'],
                'deviceid'=>$postdata['deviceid'],
                'operator'=>$postdata['operator'],
                'result'=>9505,
                'message'=>'账号没有绑定任何店铺相关数据！请联系服务商开通店铺！',
                'order'=>'',
            ];
            $return['sign']=$this->makesign($return);
            return json_encode($return);
            // exit('账号没有绑定任何店铺相关数据！请联系服务商开通店铺！');
        }

        $MerchantPayWay = MerchantPayWay::where('merchant_id', $m_id)->first();
        //判断通道
        if (!$MerchantPayWay || !$MerchantPayWay->alipay) {
            $return = [
                'service'=>$postdata['service'],
                'deviceid'=>$postdata['deviceid'],
                'operator'=>$postdata['operator'],
                'result'=>9506,
                'message'=>'没有设置收款通道！请设置收款通道',
                'order'=>'',
            ];
            $return['sign']=$this->makesign($return);
            return json_encode($return);
            exit('没有设置收款通道！请设置收款通道');
        }
        // 开始支付动作，并返回状态=======================start

// print_r($data);
// echo '谨慎开始';die;




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
                    $type = "posmoalipay";
                    //仅当面付
                    $store = MerchantShops::where('merchant_id', $m_id)->where('store_type', 'oAlipay')->first();
                    $storeInfo = AlipayAppOauthUsers::where('store_id', $store->store_id)->first();
                    if (!$storeInfo) {

                        $return = [
                            'service'=>$postdata['service'],
                            'deviceid'=>$postdata['deviceid'],
                            'operator'=>$postdata['operator'],
                            'result'=>9507,
                            'message'=>'商户信息不存在！请重新授权！',
                            'order'=>'',
                        ];
                        $return['sign']=$this->makesign($return);
                        return json_encode($return);

                        $msg = '商户信息不存在！请重新授权！';
                        echo $msg;die;
                        return view('merchant.page.error', compact('price', 'pay_user', 'msg'));
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
                    $type = "posmsalipay";
                    $store = MerchantShops::where('merchant_id', $m_id)->where('store_type', 'sAlipay')->first();
                    $storeInfo = AlipayShopLists::where('store_id', $store->store_id)->first();
                    if (!$storeInfo) {

                        $return = [
                            'service'=>$postdata['service'],
                            'deviceid'=>$postdata['deviceid'],
                            'operator'=>$postdata['operator'],
                            'result'=>9507,
                            'message'=>'商户信息不存在！请联系服务商！',
                            'order'=>'',
                        ];
                        $return['sign']=$this->makesign($return);
                        return json_encode($return);

                        exit('商户信息不存在！请联系服务商！');
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

                        $return = [
                            'service'=>$postdata['service'],
                            'deviceid'=>$postdata['deviceid'],
                            'operator'=>$postdata['operator'],
                            'result'=>9508,
                            'message'=>'订单支付成功',
                            'order'=>'',
                        ];
                        $return['sign']=$this->makesign($return);
                        return json_encode($return);
                        
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

                                $return = [
                                    'service'=>$postdata['service'],
                                    'deviceid'=>$postdata['deviceid'],
                                    'operator'=>$postdata['operator'],
                                    'result'=>9508,
                                    'message'=>'订单支付成功',
                                    'order'=>$out_trade_no,
                                ];
                                $return['sign']=$this->makesign($return);
                                return json_encode($return);

                                return json_encode([
                                    'status' => 1,
                                    'msg' => '订单支付成功'
                                ]);
                            } else {
                                sleep(5);
                                $status = $this->TradePayQuery($out_trade_no);
                            }


                            $return = [
                                'service'=>$postdata['service'],
                                'deviceid'=>$postdata['deviceid'],
                                'operator'=>$postdata['operator'],
                                'result'=>9509,
                                'message'=>'支付失败！请重新再试',
                                'order'=>$out_trade_no,
                            ];
                            $return['sign']=$this->makesign($return);
                            return json_encode($return);

                            return json_encode([
                                'status' => $status,
                                'msg' => '支付失败！请重新再试'.$status,
                            ]);

                        } else {
                            $msg = $result->$responseNode->sub_msg;//错误信息

                            $return = [
                                'service'=>$postdata['service'],
                                'deviceid'=>$postdata['deviceid'],
                                'operator'=>$postdata['operator'],
                                'result'=>9509,
                                'message'=>$msg,
                                'order'=>$out_trade_no,
                            ];
                            $return['sign']=$this->makesign($return);
                            return json_encode($return);
                            return json_encode([
                                'status' => $result->$responseNode->sub_code,
                                'msg' => $msg,
                            ]);
                        }
                    }
                } catch (\Exception $exception) {

                    $return = [
                        'service'=>$postdata['service'],
                        'deviceid'=>$postdata['deviceid'],
                        'operator'=>$postdata['operator'],
                        'result'=>9510,
                        'message'=>'系统异常请重新再试',
                        'order'=>$out_trade_no,
                    ];
                    $return['sign']=$this->makesign($return);
                    return json_encode($return);
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
                // echo    987654321;die;
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
                            'type' => 'posmpalipay'
                        ];
                        MerchantOrders::create($insert);

                        $return = [
                            'service'=>$postdata['service'],
                            'deviceid'=>$postdata['deviceid'],
                            'operator'=>$postdata['operator'],
                            'result'=>9508,
                            'message'=>'订单支付成功',
                            'order'=>$out_trade_no,
                        ];
                        $return['sign']=$this->makesign($return);
                        return json_encode($return);

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
                            'type' => 'posmpalipay'
                        ];
                        MerchantOrders::create($insert1);
                        //暂停10秒
                        sleep(5);
                        $status = $this->WxPOrderStatus($out_trade_no);
                        if ($status['return_value']['trade_status'] == "TRADE_SUCCESS") {


                            $return = [
                                'service'=>$postdata['service'],
                                'deviceid'=>$postdata['deviceid'],
                                'operator'=>$postdata['operator'],
                                'result'=>9508,
                                'message'=>'订单支付成功',
                                'order'=>$out_trade_no,
                            ];
                            $return['sign']=$this->makesign($return);
                            return json_encode($return);
                            return json_encode([
                                'status' => 1,
                                'msg' => '订单支付成功'
                            ]);
                        } else {
                            sleep(5);
                            $status = $this->WxPOrderStatus($out_trade_no);
                        }
                        if ($status['return_value']['trade_status'] == "TRADE_SUCCESS") {

                            $return = [
                                'service'=>$postdata['service'],
                                'deviceid'=>$postdata['deviceid'],
                                'operator'=>$postdata['operator'],
                                'result'=>9508,
                                'message'=>'订单支付成功',
                                'order'=>$out_trade_no,
                            ];
                            $return['sign']=$this->makesign($return);
                            return json_encode($return);
                            return json_encode([
                                'status' => 1,
                                'msg' => '订单支付成功'
                            ]);
                        }

                        $return = [
                            'service'=>$postdata['service'],
                            'deviceid'=>$postdata['deviceid'],
                            'operator'=>$postdata['operator'],
                            'result'=>9509,
                            'message'=>'支付失败!请重新支付',
                            'order'=>$out_trade_no,
                        ];
                        $return['sign']=$this->makesign($return);
                        return json_encode($return);
                        return json_encode([
                            'status' => $status['return_value']['trade_status'],
                            'msg' => '支付失败!请重新支付'
                        ]);
                    }
                } catch (\Exception $exception) {
                    Log::info($exception);

                    $return = [
                        'service'=>$postdata['service'],
                        'deviceid'=>$postdata['deviceid'],
                        'operator'=>$postdata['operator'],
                        'result'=>9510,
                        'message'=>'系统异常请重新再试',
                        'order'=>$out_trade_no,
                    ];
                    $return['sign']=$this->makesign($return);
                    return json_encode($return);
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
                $options['payment']['sub_merchant_id'] = substr($store->store_id,1);//微信子商户号
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
                        'type' => 'posmweixin'
                    ];
                    MerchantOrders::create($insertW);


                    $return = [
                        'service'=>$postdata['service'],
                        'deviceid'=>$postdata['deviceid'],
                        'operator'=>$postdata['operator'],
                        'result'=>9508,
                        'message'=>'订单支付成功',
                        'order'=>$out_trade_no,
                    ];
                    $return['sign']=$this->makesign($return);
                    return json_encode($return);
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


                            $return = [
                                'service'=>$postdata['service'],
                                'deviceid'=>$postdata['deviceid'],
                                'operator'=>$postdata['operator'],
                                'result'=>9508,
                                'message'=>'订单支付成功',
                                'order'=>$out_trade_no,
                            ];
                            $return['sign']=$this->makesign($return);
                            return json_encode($return);

                            return json_encode([
                                'status' => 1,
                                'msg' => '订单支付成功'
                            ]);
                        }


                        $return = [
                            'service'=>$postdata['service'],
                            'deviceid'=>$postdata['deviceid'],
                            'operator'=>$postdata['operator'],
                            'result'=>9509,
                            'message'=>$status->trade_state_desc,
                            'order'=>$out_trade_no,
                        ];
                        $return['sign']=$this->makesign($return);
                        return json_encode($return);
                    
                        return json_encode([
                            'status' => $status->trade_state,
                            'msg' => $status->trade_state_desc
                        ]);

                    } else {
                        $msg = $result->err_code_des;//错误信息

                        $return = [
                            'service'=>$postdata['service'],
                            'deviceid'=>$postdata['deviceid'],
                            'operator'=>$postdata['operator'],
                            'result'=>9509,
                            'message'=>$msg,
                            'order'=>$out_trade_no,
                        ];
                        $return['sign']=$this->makesign($return);
                        return json_encode($return);


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
                            'type' => 'posmpweixin'
                        ];
                        MerchantOrders::create($insertPW);


                        $return = [
                            'service'=>$postdata['service'],
                            'deviceid'=>$postdata['deviceid'],
                            'operator'=>$postdata['operator'],
                            'result'=>9508,
                            'message'=>'订单支付成功',
                            'order'=>$out_trade_no,
                        ];
                        $return['sign']=$this->makesign($return);
                        return json_encode($return);

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
                            'type' => 'posmpweixin'
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

                            $return = [
                                'service'=>$postdata['service'],
                                'deviceid'=>$postdata['deviceid'],
                                'operator'=>$postdata['operator'],
                                'result'=>9508,
                                'message'=>'订单支付成功',
                                'order'=>$out_trade_no,
                            ];
                            $return['sign']=$this->makesign($return);
                            return json_encode($return);
                            return json_encode([
                                'status' => 1,
                                'msg' => '订单支付成功'
                            ]);
                        }

                        $return = [
                            'service'=>$postdata['service'],
                            'deviceid'=>$postdata['deviceid'],
                            'operator'=>$postdata['operator'],
                            'result'=>9509,
                            'message'=>$status['return_value']['trade_state_desc'],
                            'order'=>$out_trade_no,
                        ];
                        $return['sign']=$this->makesign($return);
                        return json_encode($return);

                        return json_encode([
                            'status' => $status['return_value']['trade_state'],
                            'msg' => $status['return_value']['trade_state_desc']
                        ]);
                    }
                } catch (\Exception $exception) {
                    Log::info($exception);

                    $return = [
                        'service'=>$postdata['service'],
                        'deviceid'=>$postdata['deviceid'],
                        'operator'=>$postdata['operator'],
                        'result'=>9510,
                        'message'=>'系统异常请重新再试',
                        'order'=>$out_trade_no,
                    ];
                    $return['sign']=$this->makesign($return);
                    return json_encode($return);

                    return json_encode([
                        'status' => 0,
                        'msg' => '系统异常请重新再试567！'
                    ]);
                }

            }
        }
        /****************微信支付 结束*******************/





        // 开始支付动作，并返回状态=======================end




    }





    /*
        查询一个订单（订单创建时间，支付方式，状态[失败/成功]，来源[pos机器/二维码/扫码枪....]）
    */
    public function queryOrder(Request $request)
    {

/*
        $poststr=$request->all();
        $poststr=$this->handlepostdata($poststr);

        $postdata=$poststr ? json_decode($poststr,true) : '';
        if(empty($postdata))
        {
            $return = [
                'service'=>'',
                'deviceid'=>'',
                'operator'=>'',
                'result'=>9511,
                'message'=>'未收到参数',
                'order'=>'',
            ];
            $return['sign']=$this->makesign($return);
            return json_encode($return);
        }
*/
        // 找出所有订单
    // select b.* from merchant_pos a left join merchant_orders b on a.merchant_id=b.merchant_id where a.poscode='123456' and b.pay_ways like 'pos%' and created_at>'2017-03-21 15:51:51';
        $data = MerchantPos::select()->leftJoin('merchant_orders as b','b.merchant_id','=','b.merchant_id')->where('merchant_pos.poscode','=',$postdata['code']=7593)->where('b.pay_ways','like','pos%')->orderBy('b.created_at', 'desc')->offset(0)->limit(10)->get()->toArray();
        echo '<pre>';
        print_r($data);die;
        //查出商户号
        // 查出订单信息

        // var_dump($data);die;
    }










    /*
        获取当天该pos收款成功的订单数及金额
        条件  当天0点到23点59分59秒
    */
    public function queryCurdayPOSorder(request $request)
    {
        $service=13;

        $poststr=$request->all();
        $poststr=$this->handlepostdata($poststr);

        $postdata=$poststr ? json_decode($poststr,true) : '';
        if(empty($postdata))
        {
            $return = [
                'service'=>$service,
                'deviceid'=>isset($postdata['deviceid'])?$postdata['deviceid']:'',
                'operator'=>isset($postdata['operator'])?$postdata['operator']:'',
                'result'=>'9511',
                'message'=>'未收到参数',
                'get_nu'=>'',
                'get_am'=>'',
            ];
            $return['sign']=$this->makesign($return);
            return json_encode($return);
        }
 
        // 2 验证签名
        if($this->verifypostdata($postdata))
        {
            $return = [
                'service'=>$service,
                'deviceid'=>isset($postdata['deviceid'])?$postdata['deviceid']:'',
                'operator'=>isset($postdata['operator'])?$postdata['operator']:'',
                'result'=>'9501',
                'message'=>'验证签名失败',
                'get_nu'=>'',
                'get_am'=>'',
            ];
            $return['sign']=$this->makesign($return);
            return json_encode($return);
            // exit('验证签名失败');
        }


        $start=date('Y-m-d ').'00:00:00';
        $end=date('Y-m-d ').'23:59:59';

        // 根据pos的id得到店员的id
        $merchantdata = MerchantPos::where('poscode','7593')->first()->toArray();
        if(empty($merchantdata))
        {
            $return = [
                'service'=>$service,
                'deviceid'=>isset($postdata['deviceid'])?$postdata['deviceid']:'',
                'operator'=>isset($postdata['operator'])?$postdata['operator']:'',
                'result'=>'9504',
                'message'=>'pos机不存在',
                'get_nu'=>'',
                'get_am'=>'',
            ];
            $return['sign']=$this->makesign($return);
            return json_encode($return); 
        }
        

        // 查出当前pos机的今日交易情况
        // select count(id) orders,sum(total_amount) money from merchant_orders where created_at between 1 and 2 and status like "SUCCESS%";
        // $results = DB::select('select * from users where id = :id', ['id' => 1]);
        $todaydata = DB::select('select count(id) orders,sum(total_amount) money from merchant_orders where created_at between :start and :end and (status = "SUCCESS" OR status="TRADE_SUCCESS" )'
            , ['start' => $start='2017-01-02 00:00:00','end'=>$end='2017-06-02 00:55:00']);
        $todaydata=array_shift($todaydata);


        $return = [
            'service'=>$service,
            'deviceid'=>$postdata['deviceid'],
            'operator'=>$postdata['operator'],
            'result'=>'9504',
            'message'=>'pos机不存在',
            'get_nu'=>(string)$todaydata->orders,
            'get_am'=>(string)$todaydata->money,
        ];
        $return['sign']=$this->makesign($return);
        return json_encode($return); 

    }





    /*

        退款

    */
    public function refund()
    {
        $service=10;
        echo __METHOD__;

    }

}