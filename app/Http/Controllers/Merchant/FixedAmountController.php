<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/4/18
 * Time: 17:38
 */

namespace App\Http\Controllers\Merchant;


use Alipayopen\Sdk\Request\AlipayTradePrecreateRequest;
use App\Http\Controllers\PingAn\BaseController;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayShopLists;
use App\Models\MerchantShops;
use App\Models\PageSets;
use App\Models\PinganStore;
use App\Models\WeixinPayConfig;
use App\Models\WeixinPayNotify;
use App\Models\WeixinShopList;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;

class FixedAmountController extends BaseController
{
    //银联固定金额
    public function UnionPayFixed()
    {
        $m_id = auth()->guard('merchant')->user()->id;
        $m = MerchantShops::where('merchant_id', $m_id)->where('store_type', 'unionpay')->first();
        //有店铺 跳转
        if ($m) {
            $store_id=$m->store_id;
            $type='u';
            return view('merchant.fixedView',compact('type','store_id','m_id'));
        } else {
            //无店铺创建
            return redirect(route('UnionPayStoreCreate'));
        }
    }


    //选择支付方式
    public function choosePayWay(){
        $m_id = auth()->guard('merchant')->user()->id;
        $shopcolloct = MerchantShops::where('merchant_id', $m_id)->get();

        $store_id=[];
        foreach($shopcolloct as $v){
            switch (strtolower($v->store_type)){
                case 'unionpay':
                    $store_id['u']=$v->store_id;
                    break;
                case 'weixin':
                    $store_id['w']=$v->store_id;
                    break;
                case 'oalipay':
                    $store_id['o']=$v->store_id;
                    break;
                case 'salipay':
                    $store_id['s']=$v->store_id;
                    break;
                case 'pingan':
                    $store_id['p']=$v->store_id;
                    break;
                case 'pufa':
                    $store_id['f']=$v->store_id;
                    break;
            }
            
        }
        return view('merchant.choosepayway',compact('m_id','store_id'));
    }
    //固定金额
    public function allPayFixed(Request $request){
        $type=$request->type;
        $store_id=$request->store_id;
        $m_id=$request->m_id;
        return view('merchant.fixedView',compact('type','store_id','m_id'));
    }

    //固定金额视图
    public function FixedView(Request $request)
    {
        $store_id='';
        $type='u';
        return view('merchant.fixedView',compact('type','store_id'));
    }

    //生成qrcode
    public function getcodeurl(Request $request){
        $type=$request->type;
        $store_id=$request->store_id;
        $m_id=$request->m_id;
        $total_amount=$request->total_amount;
        $info='-1未知错误,请联系服务商!';
        switch($type){
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
                try{
                    $shop = WeixinShopList::where('store_id', $store_id)->first();
                    if($shop){
                        $shop_name=$shop->name;
                        $out_trade_no = date('Ymdhis', time()) . '8888' . date('Ymdhis', time());//订单号
                        $attributes = [
                            'trade_type' => 'NATIVE', // JSAPI，NATIVE，APP...
                            'body' => $shop_name . '商家收款',
                            'detail' => $shop_name . '商家收款',
                            'out_trade_no' => $out_trade_no,
                            'total_fee' => $total_amount*100,
                            'notify_url' => url('/merchant/wxcodeurlnotify'), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
//                            'openid'=> 'sadfsfasdf', // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
                            'sub_mch_id'=>$shop->mch_id,
                        ];
                        $order = new Order($attributes);
                        $result = $payment->prepare($order);
                        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
                            $prepayId = $result->prepay_id;
                            $code_url = $result->code_url;
                            $orderinfo['out_trade_no']=$out_trade_no;
                            $orderinfo['store_id']=$store_id;
                            $orderinfo['merchant_id']=$m_id;
                            $orderinfo['type']=203;
                            $orderinfo['total_amount']=$total_amount;
                            \App\Models\Order::create($orderinfo);
                            return json_encode(['status'=>1,'prepayId'=>$prepayId,'code_url'=>$code_url]);
                        }else{
                            $info=$result->return_code.':'.$result->return_msg;
                        }
                    }else{
                        $info='-201不存在该店铺信息,请联系服务商!';
                    }
                }catch(Exception $e){
                    $info=$e->getMessage();
                }
                break;
            case 'o':
                $config=AlipayIsvConfig::where('id',1)->first();
                try{
                    if($config){
                        $config=$config->toArray();
                        //1.接入参数初始化
                        $aop = app('AopClient');
                        $aop->signType="RSA2";//升级算法
                        $aop->gatewayUrl = Config::get('alipayopen.gatewayUrl');
                        $aop->appId = $config['app_id'];
                        //软件生成的应用私钥字符串
                        $aop->rsaPrivateKey = $config['rsaPrivateKey'];
                        $aop->format = "json";
                        $aop->charset = "GBK";
                        $aop->version="2.0";
                        $aop->method="alipay.trade.precreate";

                        $user=AlipayAppOauthUsers::where('store_id',$store_id)->first()->toArray();
                        if($user){
                            $app_auth_token=$user['app_auth_token'];
                            $auth_shop_name=$user['auth_shop_name'];
                            //2.调用接口
                            $requests = new AlipayTradePrecreateRequest();
                            $requests->setNotifyUrl(url('/merchant/alicodeurlnotify'));
                            $out_trade_no = date('Ymdhis', time()) .rand(10000,99999);//订单号

                            $requests->setBizContent("{" .
                                "    \"out_trade_no\":\"".$out_trade_no."\"," .
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


                            $result =$aop->execute($requests,NULL,$app_auth_token);
                            if($request&&$result->alipay_trade_precreate_response){
                                $qr=$result->alipay_trade_precreate_response;
                                if($qr->code==10000){
                                    $code_url = $qr->qr_code;
                                    $orderinfo['out_trade_no']=$out_trade_no;
                                    $orderinfo['store_id']=$store_id;
                                    $orderinfo['merchant_id']=$m_id;
                                    $orderinfo['type']=104;
                                    $orderinfo['total_amount']=$total_amount;
                                    \App\Models\Order::create($orderinfo);
                                    return json_encode(['status'=>1,'code_url'=>$code_url]);
                                }else{
                                    $info=$qr->msg;
                                }
                            }else{
                                $info='生成预订单失败,请联系服务商';
                            }
                        }else{
                            $info='店铺信息不存在,请联系服务商';
                        }
                    }else{
                        $info='请联系服务商,检查ISV配置';
                    }
                }catch (\Exception $exception){
                    $info= $exception->getMessage();
                }
                break;
            case 's':
                $config=AlipayIsvConfig::where('id',1)->first();
                try{
                    if($config){
                        $config=$config->toArray();
                        //1.接入参数初始化
                        $aop = app('AopClient');
                        $aop->signType="RSA2";//升级算法
                        $aop->gatewayUrl = Config::get('alipayopen.gatewayUrl');
                        $aop->appId = $config['app_id'];
                        //软件生成的应用私钥字符串
                        $aop->rsaPrivateKey = $config['rsaPrivateKey'];
                        $aop->format = "json";
                        $aop->charset = "GBK";
                        $aop->version="2.0";
                        $aop->method="alipay.trade.precreate";

                        $user=AlipayShopLists::where('store_id',$store_id)->first()->toArray();
                        if($user){
                            $app_auth_token=$user['app_auth_token'];
                            $auth_shop_name=$user['main_shop_name'];
                            //2.调用接口
                            $requests = new AlipayTradePrecreateRequest();
                            $requests->setNotifyUrl(url('/merchant/alicodeurlnotify'));
                            $out_trade_no = date('Ymdhis', time()) .rand(10000,99999);//订单号

                            $requests->setBizContent("{" .
                                "    \"out_trade_no\":\"".$out_trade_no."\"," .
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


                            $result =$aop->execute($requests,NULL,$app_auth_token);
                            if($request&&$result->alipay_trade_precreate_response){
                                $qr=$result->alipay_trade_precreate_response;
                                if($qr->code==10000){
                                    $code_url = $qr->qr_code;
                                    $orderinfo['out_trade_no']=$out_trade_no;
                                    $orderinfo['store_id']=$store_id;

                                    $orderinfo['merchant_id']=$m_id;
                                    $orderinfo['type']=106;
                                    $orderinfo['total_amount']=$total_amount;
                                    \App\Models\Order::create($orderinfo);
                                    return json_encode(['status'=>1,'code_url'=>$code_url]);
                                }else{
                                    $info=$qr->msg;
                                }
                            }else{
                                $info='生成预订单失败,请联系服务商';
                            }
                        }else{
                            $info='店铺信息不存在,请联系服务商';
                        }
                    }else{
                        $info='请联系服务商,检查ISV配置';
                    }
                }catch (\Exception $exception){
                    $info= $exception->getMessage();
                }
                break;
            case 'pj':
                $store_id = $request->get('store_id');
                $merchant_id = $request->get('m_id');//收银员
                $total_amount = $request->get('total_amount');
                $ctrl=new BaseController();
                $aop = $ctrl->AopClient();
                $aop->method = "fshows.liquidation.jdpay.uniorder";
                $store = PinganStore::where('external_id', $store_id)->first();
                $out_trade_no = 'pjg' . date('YmdHis', time()) . rand(10000, 99999);
                $pay = [
                    'sub_merchant_id' => $store->sub_merchant_id,
                    'body' => $store->alias_name . '门店收款信息',
                    'out_trade_no' => $out_trade_no,
                    'total_fee' => $total_amount,
                    'notify_url' => url('/admin/pingan/jd_notify_url'),
                    'order_type' => 1
                ];
                $data = array('content' => json_encode($pay));
                try {
                    $response = $aop->execute($data);
                    $responseArray = json_decode($response, true);
                    if ($responseArray['success']) {
                        $insert = [
                            'trade_no' => $responseArray['return_value']['trade_no'],
                            "out_trade_no" => $out_trade_no,
                            'store_id' => $store->external_id,
                            "type" => "303",
                            "merchant_id" =>(int)$merchant_id,
                            "total_amount" => $total_amount,
                            "buyer_id"=>"",
                            "status" => "",
                            "remark"=>'',
                            "created_at" => date('Y-m-d H:i:s', time()),
                            "updated_at" => date('Y-m-d H:i:s', time())
                        ];
//                dd($insert);
                        \App\Models\Order::create($insert);
                        return json_encode(['status'=>1,'code_url'=>$responseArray['return_value']['qr_code']]);
                    }else{
                        $info=$responseArray['error_code'].':'.$responseArray['error_message'];
                    }
                } catch(\Exception $e){
                    $info= $e->getMessage();
                }
                break;
        }
        return json_encode(['status'=>0,'msg'=>$info]);
    }
    
}