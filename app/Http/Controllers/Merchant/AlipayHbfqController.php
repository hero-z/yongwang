<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2017/8/2
 * Time: 下午2:02
 */

namespace App\Http\Controllers\Merchant;


use Alipayopen\Sdk\Request\AlipayTradePrecreateRequest;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayShopLists;
use App\Models\MerchantShops;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AlipayHbfqController extends BaseController
{

    public function alipayhbfq()
    {
        $user['id'] = auth()->guard('merchant')->user()->id;
        $store_o = MerchantShops::where('store_type', 'oalipay')->where('merchant_id', $user['id'])->first();
        $store_s = MerchantShops::where('store_type', 'salipay')->where('merchant_id', $user['id'])->first();

        return view("merchant.Alipay.alipayhbfq",compact("store_o","store_s","cashier","status"));

    }



    public function AlipayHbfqPost(Request $request)
    {
        $type=$request->get('type');
        $total_amount=$request->get('total_amount');
        $hb_fq_num=$request->get('hb_fq_num');
        $hb_fq_seller_percent=$request->get('hb_fq_seller_percent');

        if ($type=="支付宝口碑"){
            $shop_id =1;//这个代表有 不是代表门店id
        }else{
            $shop_id=0;
        }
        if ($hb_fq_num=="3期"){
            $hb_fq_num =3;//
        }
        if ($hb_fq_num=="6期"){
            $hb_fq_num =6;//
        }
        if ($hb_fq_num=="12期"){
            $hb_fq_num =12;//
        }

        if ($hb_fq_seller_percent=="商户"){
            $hb_fq_seller_percent =100;//
        }
        if ($hb_fq_seller_percent=="顾客"){
            $hb_fq_seller_percent =0;//
        }
        try {
            $user['id'] = auth()->guard('merchant')->user()->id;
           // $shop_id = $shop_id;
          //  $total_amount =$total_amount;//金额
          //  $hb_fq_num = $hb_fq_num;//花呗分期数
           // $hb_fq_seller_percent = $hb_fq_seller_percent;//商家承担手续费传入100，用户承担手续费传入0
            $config = AlipayIsvConfig::where('id', 1)->first();

            if (!$total_amount){
                return json_encode([
                    'status' => 0,
                    'msg' =>'金额未输入',
                ]);
            }

            if ($config) {
                $config = $config->toArray();
                //1.接入参数初始化
                $aop = app('AopClient');
                $aop->signType = "RSA2";//升级算法
                $aop->gatewayUrl = Config::get('alipayopen.gatewayUrl');
                $aop->appId = $config['app_id'];
                //软件生成的应用私钥字符串
                $aop->rsaPrivateKey = $config['rsaPrivateKey'];
                $aop->format = "json";
                $aop->charset = "GBK";
                $aop->version = "2.0";
                $aop->method = "alipay.trade.precreate";
                //口碑分期
                if ($shop_id) {
                    $store = MerchantShops::where('store_type', 'salipay')->where('merchant_id', $user['id'])->first();
                    if (!$store){
                        return json_encode([
                            'status' => 0,
                            'msg' =>'未绑定通道',
                        ]);
                    }
                    $user = AlipayShopLists::where('store_id', $store->store_id)->first();
                    if ($user) {
                        $user = $user->toArray();
                        $app_auth_token = $user['app_auth_token'];
                        $auth_shop_name = $user['main_shop_name'];
                        //2.调用接口
                        $requests = new AlipayTradePrecreateRequest();
                        $requests->setNotifyUrl(url('/merchant/alicodeurlnotify'));
                        $out_trade_no = date('Ymdhis', time()) . rand(10000, 99999);//订单号
                        $requests->setBizContent("{" .
                            "    \"out_trade_no\":\"" . $out_trade_no . "\"," .
//                                "    \"seller_id\":\"".$user['user_id']."\"," .
                            "\"total_amount\":" . $total_amount . "," .
                            "\"subject\":\"" . $auth_shop_name . "-花呗分期-" . $hb_fq_num . "期" . "\"," .
                            "\"extend_params\":{" .
                            "\"sys_service_provider_id\":\"" . $config['pid'] . "\"," .
                            "\"hb_fq_num\":\"" . $hb_fq_num . "\"," .
                            "\"hb_fq_seller_percent\":\"" . $hb_fq_seller_percent . "\"" .
                            "}," .
                            "\"alipay_store_id\":" . $user->shop_id . "," .
                            "\"timeout_express\":\"90m\"" .
                            "  }");


                        $result = $aop->execute($requests, NULL, $app_auth_token);
                        if ($request && $result->alipay_trade_precreate_response) {
                            $qr = $result->alipay_trade_precreate_response;
                            if ($qr->code == 10000) {
                                $code_url = $qr->qr_code;
                                $orderinfo['out_trade_no'] = $out_trade_no;
                                $orderinfo['store_id'] = $store->store_id;
                                $orderinfo['merchant_id'] = $user['id'];
                                $orderinfo['type'] = 106;
                                $orderinfo['total_amount'] = $total_amount;
                                $orderinfo['hb_fq_num'] = (int)$hb_fq_num;
                                $orderinfo['hb_fq_seller_percent'] = (int)$hb_fq_seller_percent;
                                $orderinfo['hb_fq_sxf'] = $this->hb_fq_sxf($hb_fq_num, $total_amount, $hb_fq_seller_percent);
                                \App\Models\Order::create($orderinfo);
                                return json_encode([
                                    'status' => 1,
                                    'data' => ['code_url' => $code_url],
                                ]);
                            } else {
                                return json_encode([
                                    'status' => 0,
                                    'msg' => $qr->sub_msg
                                ]);

                            }
                        } else {

                            $info = '生成预订单失败,请联系服务商';
                            return json_encode([
                                'status' => 0,
                                'msg' => $info
                            ]);
                        }
                    } else {
                        $info = '你还没有开通支付宝官方店铺,请联系服务商';
                        return json_encode([
                            'status' => 0,
                            'msg' => $info
                        ]);
                    }

                    //当面付分期
                } else {
                    $store = MerchantShops::where('store_type', 'oalipay')->where('merchant_id', $user['id'])->first();
                    if (!$store){
                        return json_encode([
                            'status' => 0,
                            'msg' =>'未绑定通道',
                        ]);
                    }
                    $user = AlipayAppOauthUsers::where('store_id', $store->store_id)->first();
                    if ($user) {
                        $user = $user->toArray();
                        $app_auth_token = $user['app_auth_token'];
                        $auth_shop_name = $user['auth_shop_name'];
                        //2.调用接口
                        $requests = new AlipayTradePrecreateRequest();
                        $requests->setNotifyUrl(url('/merchant/alicodeurlnotify'));
                        $out_trade_no = date('Ymdhis', time()) . rand(10000, 99999);//订单号

                        $requests->setBizContent("{" .
                            "    \"out_trade_no\":\"" . $out_trade_no . "\"," .
//                                "    \"seller_id\":\"".$user['user_id']."\"," .
                            "\"total_amount\":" . $total_amount . "," .
                            "\"subject\":\"" . $auth_shop_name . "-花呗分期-" . $hb_fq_num . "期" . "\"," .
                            "\"extend_params\":{" .
                            "\"sys_service_provider_id\":\"" . $config['pid'] . "\"," .
                            "\"hb_fq_num\":\"" . $hb_fq_num . "\"," .
                            "\"hb_fq_seller_percent\":\"" . $hb_fq_seller_percent . "\"" .
                            "}," .
                            "\"timeout_express\":\"90m\"" .
                            "  }");


                        $result = $aop->execute($requests, NULL, $app_auth_token);
                        if ($request && $result->alipay_trade_precreate_response) {
                            $qr = $result->alipay_trade_precreate_response;
                            if ($qr->code == 10000) {
                                $code_url = $qr->qr_code;
                                $orderinfo['out_trade_no'] = $out_trade_no;
                                $orderinfo['store_id'] = $store->store_id;
                                $orderinfo['merchant_id'] = $user['id'];
                                $orderinfo['type'] = 104;
                                $orderinfo['total_amount'] = $total_amount;
                                $orderinfo['hb_fq_num'] = (int)$hb_fq_num;
                                $orderinfo['hb_fq_seller_percent'] = (int)$hb_fq_seller_percent;
                                $orderinfo['hb_fq_sxf'] = $this->hb_fq_sxf($hb_fq_num, $total_amount, $hb_fq_seller_percent);
                                \App\Models\Order::create($orderinfo);
                                return json_encode([
                                    'status' => 1,
                                    'data' => ['code_url' => $code_url],
                                ]);
                            } else {
                                return json_encode([
                                    'status' => 0,
                                    'msg' => $qr->sub_msg
                                ]);

                            }
                        } else {

                            $info = '生成预订单失败,请联系服务商';
                            return json_encode([
                                'status' => 0,
                                'msg' => $info
                            ]);
                        }
                    } else {
                        $info = '店铺信息不存在,请联系服务商';
                        return json_encode([
                            'status' => 0,
                            'msg' => $info
                        ]);
                    }
                }

            } else {
                $info = '请联系服务商,检查ISV配置';
                return json_encode([
                    'status' => 0,
                    'msg' => $info
                ]);
            }
        } catch (\Exception $exception) {
            $info = $exception->getMessage();
            return json_encode([
                'status' => 0,
                'line'=>$exception->getLine(),
                'msg' => $info
            ]);
        }

    }

    //花呗分期手续费
    public function hb_fq_sxf($hb_fq_num, $total_amount, $hb_fq_seller_percent)
    {
        //商家承担手续费
        if ((int)$hb_fq_seller_percent == 100) {

            //1.80%
            if ((int)$hb_fq_num == 3) {
                $hb_fq_sxf=$total_amount*0.018;
            }
            //4.5%
            if ((int)$hb_fq_num == 6) {
                $hb_fq_sxf=$total_amount*0.045;

            }
            // 7.5%
            if ((int)$hb_fq_num == 12) {
                $hb_fq_sxf=$total_amount*0.075;

            }

        }
        //用户承担手续费
        if ((int)$hb_fq_seller_percent == 0) {

            //2.30%
            if ((int)$hb_fq_num == 3) {
                $hb_fq_sxf=$total_amount*0.023;

            }
            //4.5%
            if ((int)$hb_fq_num == 6) {
                $hb_fq_sxf=$total_amount*0.045;

            }
            // 7.5%
            if ((int)$hb_fq_num == 12) {
                $hb_fq_sxf=$total_amount*0.075;

            }
        }


        return $hb_fq_sxf;
    }

}