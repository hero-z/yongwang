<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/5/23
 * Time: 13:20
 */

namespace App\Api\Controllers\Merchant;


use Alipayopen\Sdk\Request\AlipayTradePrecreateRequest;
use App\Http\Controllers\Merchant\NewOrderManageController;
use App\Merchant;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayShopLists;
use App\Models\AppUpdate;
use App\Models\MerchantShops;
use App\Models\Order;
use App\Models\PingancqrLsitsinfo;
use App\Models\PinganStore;
use App\Models\PufacqrLsitsinfo;
use App\Models\PufaStores;
use App\Models\WeixinPayConfig;
use App\Models\WeixinShopList;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SelectController extends BaseController
{
    //查询门店信息
    public function selectStore(Request $request)
    {
        $user = $this->getMerchantInfo();
        $MerchantShops = MerchantShops::where('merchant_id', $user['id'])->select('store_id')->get();
        if ($MerchantShops->isEmpty()) {
            return json_encode([
                'status' => 0,
                'msg' => '收银员没有绑定店铺'
            ]);
        }
        $store_ids = array_flatten($MerchantShops->toArray());//
        $shopTable = [1 => 'alipay_app_oauth_users', 2 => 'alipay_shop_lists', 3 => 'weixin_shop_lists', 4 => 'pingan_stores', 5 => 'pufa_stores', 6 => 'union_pay_stores', 7 => 'ms_stores'];
        $res = [];
        foreach ($shopTable as $v) {
            $useridmark = 'user_id';
            $storeidmark = 'store_id';
            $storenamemark = 'store_name';
            switch ($v) {
                case 'alipay_app_oauth_users':
                    $useridmark = 'promoter_id';
                    $storenamemark = 'auth_shop_name';
                    break;
                case 'alipay_shop_lists':
                    $storenamemark = 'main_shop_name';
                    break;
                case 'pingan_stores':
                    $storeidmark = 'external_id';
                    $storenamemark = 'alias_name';
                    break;
                case 'pufa_stores':
                    $storenamemark = 'merchant_short_name';
                    break;
                case 'union_pay_stores':
                    $storenamemark = 'alias_name';
                    break;
                case 'ms_stores':
                    $storenamemark = 'store_short_name';
                    break;
            }
            $res[] = DB::table($v)
                ->whereIn($v . '.' . $storeidmark, $store_ids)
                ->select($v . '.' . $storeidmark . " as store_id", $v . "." . $storenamemark . " as store_name");
        }
        $a = new NewOrderManageController();
        $result = $a->checkEmpty($res);
        $list = empty($result) ? '' : $result->distinct()->get();
        return json_encode(['data' => $list]);

    }

    //查询 生成二维码
    public function selectPayQr(Request $request)
    {
        $user = $this->getMerchantInfo();
        $price = $request->get('price');
        $store_id = $request->get('store_id');
        if ($store_id == "") {
            return json_encode([
                'status' => 0,
                'msg' => '缺少参数,或者参数为空'
            ]);
        }
        $type = substr($store_id, 0, 1);
        if ($price) {
            //固定金额二维码
            return $this->getFixCodeUrl($type, $store_id, $user['id'], $price);

        } else {
            //活的二维码
            return $this->getCodeUrl($type, $store_id, $user['id']);

        }


    }

    //生成固定金额的二维码
    public function getFixCodeUrl($type, $store_id, $m_id, $total_amount)
    {
        $info = '-1未知错误,请联系服务商!';
        switch ($type) {
            case 'w':
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
                $payment = $app->payment;
                //创建订单
                try {
                    $shop = WeixinShopList::where('store_id', $store_id)->first();
                    if ($shop) {
                        $shop_name = $shop->name;
                        $out_trade_no = date('Ymdhis', time()) . '8888' . date('Ymdhis', time());//订单号
                        $attributes = [
                            'trade_type' => 'NATIVE', // JSAPI，NATIVE，APP...
                            'body' => $shop_name . '商家收款',
                            'detail' => $shop_name . '商家收款',
                            'out_trade_no' => $out_trade_no,
                            'total_fee' => $total_amount * 100,
                            'notify_url' => url('/merchant/wxcodeurlnotify'), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
//                            'openid'=> 'sadfsfasdf', // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
                            'sub_mch_id' => $shop->mch_id,
                        ];
                        $order = new \EasyWeChat\Payment\Order($attributes);
                        $result = $payment->prepare($order);
                        $responseArray = json_decode($result, true);
                        if ($responseArray['return_code'] == 'SUCCESS') {
                            $code_url = $responseArray['code_url'];
                            $orderinfo['out_trade_no'] = $out_trade_no;
                            $orderinfo['store_id'] = $store_id;
                            $orderinfo['merchant_id'] = $m_id;
                            $orderinfo['type'] = 203;
                            $orderinfo['total_amount'] = $total_amount;
                            Order::create($orderinfo);
                            return json_encode(['data' => ['code_url' => $code_url]]);
                        } else {
                            $info = $responseArray['return_code'] . ':' . $responseArray['return_msg'];
                        }
                    } else {
                        $info = '-201不存在该店铺信息,请联系服务商!';
                    }
                } catch (\Exception $e) {
                    Log::info($e);
                    $info = $e->getMessage();
                }
                break;
            case 'o':
                $config = AlipayIsvConfig::where('id', 1)->first();
                try {
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

                        $user = AlipayAppOauthUsers::where('store_id', $store_id)->first();
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
                                "\"subject\":\"" . $auth_shop_name . "固定二维码收款" . "\"," .
                                "\"extend_params\":{" .
                                "\"sys_service_provider_id\":\"" . $config['pid'] . "\"" .
                                /*  "\"hb_fq_num\":\"3\"," .
                                     "\"hb_fq_seller_percent\":\"100\"" .*/
                                "}," .
                                "\"timeout_express\":\"90m\"" .
                                "  }");


                            $result = $aop->execute($requests, NULL, $app_auth_token);
                            if ($result && $result->alipay_trade_precreate_response) {
                                $qr = $result->alipay_trade_precreate_response;
                                if ($qr->code == 10000) {
                                    $code_url = $qr->qr_code;
                                    $orderinfo['out_trade_no'] = $out_trade_no;
                                    $orderinfo['store_id'] = $store_id;
                                    $orderinfo['merchant_id'] = $m_id;
                                    $orderinfo['type'] = 104;
                                    $orderinfo['total_amount'] = $total_amount;
                                    \App\Models\Order::create($orderinfo);
                                    return json_encode(['data' => ['code_url' => $code_url]]);
                                } else {
                                    $info = $qr->msg;
                                }
                            } else {
                                $info = '生成预订单失败,请联系服务商';
                            }
                        } else {
                            $info = '店铺信息不存在,请联系服务商';
                        }
                    } else {
                        $info = '请联系服务商,检查ISV配置';
                    }
                } catch (\Exception $exception) {
                    Log::info($exception);
                    $info = $exception->getMessage();
                }
                break;
            case 's':
                $config = AlipayIsvConfig::where('id', 1)->first();
                try {
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

                        $user = AlipayShopLists::where('store_id', $store_id)->first();
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
                                "\"subject\":\"" . $auth_shop_name . "固定二维码收款" . "\"," .
                                "\"extend_params\":{" .
                                "\"sys_service_provider_id\":\"" . $config['pid'] . "\"" .
                                /*  "\"hb_fq_num\":\"3\"," .
                                     "\"hb_fq_seller_percent\":\"100\"" .*/
                                "}," .
                                "\"alipay_store_id\":" . $user['shop_id'] . "," .
                                "\"timeout_express\":\"90m\"" .
                                "  }");


                            $result = $aop->execute($requests, NULL, $app_auth_token);
                            if ($result && $result->alipay_trade_precreate_response) {
                                $qr = $result->alipay_trade_precreate_response;
                                if ($qr->code == 10000) {
                                    $code_url = $qr->qr_code;
                                    $orderinfo['out_trade_no'] = $out_trade_no;
                                    $orderinfo['store_id'] = $store_id;

                                    $orderinfo['merchant_id'] = $m_id;
                                    $orderinfo['type'] = 106;
                                    $orderinfo['total_amount'] = $total_amount;
                                    \App\Models\Order::create($orderinfo);
                                    return json_encode(['data' => ['code_url' => $code_url]]);
                                } else {
                                    $info = $qr->msg;
                                }
                            } else {
                                $info = '生成预订单失败,请联系服务商';
                            }
                        } else {
                            $info = '店铺信息不存在,请联系服务商';
                        }
                    } else {
                        $info = '请联系服务商,检查ISV配置';
                    }
                } catch (\Exception $exception) {
                    Log::info($exception);
                    $info = $exception->getMessage();
                }
                break;

            case  'p':

                if ($type == 'p') {
                    $store = PinganStore::where('external_id', $store_id)->first();
                    if ($store) {
                        $PingancqrLsitsinfo = PingancqrLsitsinfo::where('store_id', $store_id)->first();
                        if ($PingancqrLsitsinfo) {
                            $code_number = $PingancqrLsitsinfo->code_number;
                            if (!$code_number) {
                                return json_encode(['status' => 0, 'msg' => '二维码有误']);
                            };
                        } else {
                            return json_encode(['status' => 0, 'msg' => '收款码有误！请检查商户是否入驻成功']);
                        }
                        $code_url = url('/Qrcode?code_number=' . $code_number . '&merchant_id=' . $m_id);
                        return json_encode(['data' => ['code_url' => $code_url]]);
                    }
                }

                break;
            case 'f':
                if ($type == 'f') {
                    $store = PufaStores::where('store_id', $store_id)->first();
                    if ($store) {
                        $pufaqrinfo = PufacqrLsitsinfo::where('store_id', $store_id)->first();
                        if ($pufaqrinfo) {
                            $code_number = $pufaqrinfo->code_number;
                            if (!$code_number) {
                                return json_encode(['status' => 0, 'msg' => '二维码有误']);
                            };
                        } else {
                            return json_encode(['status' => 0, 'msg' => '收款码有误！请检查商户是否入驻成功']);

                        }
                        $code_url = url('api/pufa/payway?code_number=' . $code_number . '&cashier_id=' . $m_id);
                        return json_encode(['data' => ['code_url' => $code_url]]);
                    }

                    return json_encode(['status' => 0, 'msg' => '店铺不存在']);
                }
                break;
            case 'm':
                //https://isv.umxnt.com/api/minsheng/payway?code_number=20170524185142581940&store_id=m20170628204129899888
                if ($type == 'm') {
                    $store = DB::table('ms_stores')->where('store_id', $store_id)->first();
                    if ($store) {
                        $mscqr_lsitsinfos = DB::table('mscqr_lsitsinfos')->where('store_id', $store_id)->first();
                        if ($mscqr_lsitsinfos) {
                            $code_number = $mscqr_lsitsinfos->code_number;
                            if (!$code_number) {
                                return json_encode(['status' => 0, 'msg' => '二维码有误']);
                            };
                        } else {
                            return json_encode(['status' => 0, 'msg' => '收款码有误！请检查商户是否入驻成功']);

                        }
                        $code_url = url('api/minsheng/payway?code_number=' . $code_number . '&store_id=' . $store_id);
                        return json_encode(['data' => ['code_url' => $code_url]]);
                    }

                    return json_encode(['status' => 0, 'msg' => '店铺不存在']);
                }
                break;


        }
        return json_encode(['status' => 0, 'msg' => $info]);
    }

    //生成二维码

    public function getCodeUrl($type, $store_id, $m_id)
    {
        $config = AlipayIsvConfig::where('id', 1)->first()->toArray();
        if ($type == 'o') {
            $store = AlipayAppOauthUsers::where('store_id', $store_id)->first();
            if ($store) {
                $config['app_auth_url'] = Config::get('alipayopen.app_auth_url');
                $code_url = $config['app_auth_url'] . '?app_id=' . $config['app_id'] . "&redirect_uri=" . $config['callback'] . '&scope=auth_base&state=OSK_' . $store_id . '_' . $m_id;
                return json_encode(['data' => ['code_url' => $code_url]]);
            }
            return json_encode(['status' => 0, 'msg' => '店铺不存在']);
        }

        if ($type == 's') {
            $store = AlipayShopLists::where('store_id', $store_id)->first();
            if ($store) {
                $config['app_auth_url'] = Config::get('alipayopen.app_auth_url');
                $code_url = $config['app_auth_url'] . '?app_id=' . $config['app_id'] . "&redirect_uri=" . $config['callback'] . '&scope=auth_base&state=SXD_' . $store->id . '_' . $m_id;
                return json_encode(['data' => ['code_url' => $code_url]]);
            }
            return json_encode(['status' => 0, 'msg' => '店铺不存在']);
        }

        if ($type == 'w') {
            $store = WeixinShopList::where('store_id', $store_id)->first();
            if ($store) {
                $code_url = url('admin/weixin/oauth?sub_info=pay_' . $store_id . '_' . $m_id);
                return json_encode(['data' => ['code_url' => $code_url]]);
            }
            return json_encode(['status' => 0, 'msg' => '店铺不存在']);
        }

        if ($type == 'p') {
            $store = PinganStore::where('external_id', $store_id)->first();
            if ($store) {
                $PingancqrLsitsinfo = PingancqrLsitsinfo::where('store_id', $store_id)->first();
                if ($PingancqrLsitsinfo) {
                    $code_number = $PingancqrLsitsinfo->code_number;
                    if (!$code_number) {
                        return json_encode(['status' => 0, 'msg' => '二维码有误']);
                    };
                } else {
                    return json_encode(['status' => 0, 'msg' => '收款码有误！请检查商户是否入驻成功']);
                }
                $code_url = url('/Qrcode?code_number=' . $code_number . '&merchant_id=' . $m_id);
                return json_encode(['data' => ['code_url' => $code_url]]);
            }
        }
        if ($type == 'f') {
            $store = PufaStores::where('store_id', $store_id)->first();
            if ($store) {
                $pufaqrinfo = PufacqrLsitsinfo::where('store_id', $store_id)->first();
                if ($pufaqrinfo) {
                    $code_number = $pufaqrinfo->code_number;
                    if (!$code_number) {
                        return json_encode(['status' => 0, 'msg' => '二维码有误']);
                    };
                } else {
                    return json_encode(['status' => 0, 'msg' => '收款码有误！请检查商户是否入驻成功']);

                }
                $code_url = url('api/pufa/payway?code_number=' . $code_number . '&cashier_id=' . $m_id);
                return json_encode(['data' => ['code_url' => $code_url]]);
            }

            return json_encode(['status' => 0, 'msg' => '店铺不存在']);
        }
        //https://isv.umxnt.com/api/minsheng/payway?code_number=20170524185142581940&store_id=m20170628204129899888
        if ($type == 'm') {
            $store = DB::table('ms_stores')->where('store_id', $store_id)->first();
            if ($store) {
                $mscqr_lsitsinfos = DB::table('mscqr_lsitsinfos')->where('store_id', $store_id)->first();
                if ($mscqr_lsitsinfos) {
                    $code_number = $mscqr_lsitsinfos->code_number;
                    if (!$code_number) {
                        return json_encode(['status' => 0, 'msg' => '二维码有误']);
                    };
                } else {
                    return json_encode(['status' => 0, 'msg' => '收款码有误！请检查商户是否入驻成功']);

                }
                $code_url = url('api/minsheng/payway?code_number=' . $code_number . '&store_id=' . $store_id);
                return json_encode(['data' => ['code_url' => $code_url]]);
            }

            return json_encode(['status' => 0, 'msg' => '店铺不存在']);
        }

        return json_encode(['status' => 0, 'msg' => '店铺不存在']);
    }

    //更新app
    public function appUpdate(Request $request)
    {
        $update = $request->get('update');
        $type = $request->get('type');
        //pos
        if ($type) {
            try {
                $app = AppUpdate::where('id', 2)->first();
                return json_encode(['data' => ['version' => $app->version, 'UpdateUrl' => URL('' . $app->UpdateUrl . '')]]);
            } catch (\Exception $exception) {
                return json_encode(['status' => 0, 'msg' => $exception]);
            }
        }
        //app
        try {
            $app = AppUpdate::where('id', 1)->first();
            return json_encode(['data' => ['version' => $app->version, 'UpdateUrl' => URL('' . $app->UpdateUrl . '')]]);
        } catch (\Exception $exception) {
            return json_encode(['status' => 0, 'msg' => $exception]);
        }
    }

    //查询收银员信息
    public function getStoreMerchantInfo(Request $request)
    {
        try {
            $user = $this->getMerchantInfo();
            $store_id = $request->get('store_id');
            $users = DB::table('merchant_shops')
                ->join('merchants', 'merchants.id', '=', 'merchant_shops.merchant_id')
                ->where('merchant_shops.store_id', $store_id)
                ->select('merchants.id', 'merchants.pid', 'merchants.type', 'merchants.phone', 'merchants.name', 'merchant_shops.store_id')
                ->get();
            return json_encode(['status' => 1, 'data' => ['user' => $users]]);
        } catch (\Exception $exception) {
            return json_encode(['status' => 0, 'msg' => $exception->getMessage()]);

        }
    }


    //清除设备号
    public function outimei(Request $request)
    {
        $user = $this->getMerchantInfo();
        Merchant::where('phone', $user['phone'])->update(['imei' => '']);
        return json_encode(['status' => 1, 'data' => ['success' => true]]);

    }
}