<?php
/**
 * Created by PhpStorm.
 * User: hero
 * Date: 2017/5/23
 * Time: 11:53
 */

namespace App\Http\Controllers\WeBank;


use App\Models\Order;
use App\Models\ProvinceCity;
use App\Models\WeBankStore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;
use Symfony\Component\HttpFoundation\Request;

class PayController extends  BaseController
{
    const WBPAY_CODE_PATH='/api/aap/server/wepay/publicpay';
    public function publicPay(Request $request){
        $store_id = $request->get('store_id');//商户号
        $m_id=$request->get('m_id');//收银员
        $shop = WeBankStore::where('store_id', $store_id)->first();
        $storeunion=DB::table('we_bank_storeunion')->where('store_id',$store_id)->where('product_type','003')->first();
        if(empty($storeunion->wb_merchant_id)){
            $wbstorecontroller=new StoreController();
            $data=[];
            $alipartid='ali'.date('YmdHis').rand(100,999);
            $data['store_id']=$alipartid;
            $data['id_type']=$shop->id_type;
            $data['id_no']=$shop->id_no;
            $data['merchant_name']=$shop->store_name;
            $data['alias_name']=$shop->alias_name;
            $data['licence_no']=$shop->licence_no;
            $data['contact_name']=$shop->contact_name;
            $data['contact_phone']=$shop->contact_phone;
            $data['merchant_type_code']=$shop->merchant_type_code;
            $data['ali_category_id']=$storeunion->category_id;
            $data['account_no']=$shop->account_no;
            $data['account_opbank_no']=$shop->account_opbank_no;
            $data['account_name']=$shop->account_name;
            $data['account_opbank']=$shop->account_opbank;
            $data['acct_type']=$shop->acct_type;
            $data['service_phone']=$shop->service_phone;
            $data['district']='0755';
            $data['payment_type']=($storeunion->payment_type=='23'||$storeunion->payment_type=='25')?1:2;
            $cityname=ProvinceCity::where('areaCode',$shop->city_code)->first();
            if($cityname){
                $district=DB::table('we_bank_district')->where('district',$cityname)->first();
                if($district)
                    $data['district']=$district->district_code;
            }
            $res=$wbstorecontroller->registerapi($data,2);
//            dd($res);
            if($res['code'] == 0&&$res['success']){
                DB::table('we_bank_storeunion')->where('store_id',$store_id)->where('product_type','003')->update(['wb_merchant_id'=>$res['wbMerchantId'],'partner_mch_id'=>$alipartid]);
            }
        }
        return view('admin.webank.weixin.alipay_view', compact('shop','m_id'));
    }
    public function doPay(Request $request){
        $type=$request->get("type");
        $remark=$request->get("remark");
        $total_amount = $request->get('total_amount');
        $m_id=$request->get('m_id');//收银员
        $store_id= $request->get('store_id');
        $wbstore=WeBankStore::where('store_id',$store_id)->first();
        $info='未知错误';
        $storeunion=DB::table('we_bank_storeunion')->where('store_id',$store_id)->where('product_type','003')->first();
        if($type=='2'){
            $user = $request->session()->get('user_data');//买家信息
            $user_id=$user[0]->user_id;
            $notify_url=url('admin/webank/ali_callback');
            $pay_type='801';
        }else{
            $wx_user_data = $request->session()->get('wx_user_data');
            $user_id=$wx_user_data[0]['id'];
            $notify_url=url('admin/webank/wx_callback');
            $pay_type='802';
        }
        try{
            if($storeunion&&$storeunion->wb_merchant_id){
                $webank=$this->WebankHelper($type);
                $app_id = $webank->appId;
                $version = $webank->version;
                $nonce = $webank->getNonce();
                $order_id=$webank->getOrderNum('b');
                $ip=$request->getClientIp();
                $data=[
                    'orderId'=>$order_id,
                    'wbMerchantId'=>$storeunion->wb_merchant_id,
                    'totalAmount'=>$total_amount,
                    'subject'=>$wbstore->alias_name.'二维码收款',
                    'operatorId'=>$m_id,
                    'storeId'=>$wbstore->store_id,
                    'spbillCreateIp'=>$ip,
                    'subAppid'=>$webank->wx_app_id,
//                'userId'=>'2088102172192852',
                    'userId'=>$user_id,
                    'notifyUrl'=>$notify_url
                ];
                $jsonData=json_encode($data,true);
                $params = array($app_id, $version, $nonce, $jsonData);
                $sign = $webank->getSign($params);
                if (!$sign) {
                    Log::error("Sign is empty!");
                    return array(
                        'code' => '-2',
                        'msg' => '签名计算失败！'
                    );
                }
                $url_params = sprintf(self::COMMON_SIGN_FORMAT, $app_id, $nonce, $version, $sign);
                $header = ['Content-Type: application/json'];
                $request = array(
                    'url' => $webank->headUrl.self::WBPAY_CODE_PATH . $url_params,
                    'method' => 'post',
                    'timeout' => self::$timeout,
                    'data' => $jsonData,
                    'header' => $header,
                );
                $result = $webank->sendRequest($request);
                if ($result['code'] == 0&&$result['success']) {
                    $ist=[
                        'out_trade_no'=>$order_id,
                        'trade_no'=>$result['channelNo'],
                        'store_id'=>$store_id,
                        'merchant_id'=>$m_id,
                        'type'=>$pay_type,
                        'total_amount'=>$total_amount,
                        'buyer_id'=>$user_id,
                        'pay_status'=>3,
                        'remark'=>$remark
                    ];
                    $res=Order::create($ist);
                    if($res){
                        return json_encode([
                            'success'=>1,
                            'channelNo'=>$result['channelNo']
                        ]);
                    }
                }else{
                    Log::info($request);
                    Log::error($result);
                    $info=$result['msg'];
                }

            }

            return json_encode([
                'success'=>0,
                'msg'=>$info
            ]);
        }catch (Exception $e){
            Log::error($e);
            $info=$e;
        }
        return json_encode([
            'success'=>0,
            'msg'=>$info
        ]);
    }
    public function wxdoPay(Request $request){
        $type=$request->get("type");
        $remark=$request->get("remark");
        $total_amount = $request->get('total_amount');
        $m_id=$request->get('m_id');//收银员
        $store_id= $request->get('store_id');
        $wbstore=WeBankStore::where('store_id',$store_id)->first();
        $info='未知错误';
        $storeunion=DB::table('we_bank_storeunion')->where('store_id',$store_id)->where('product_type','004')->first();
        if($type=='2'){
            $user = $request->session()->get('user_data');//买家信息
            $user_id=$user[0]->user_id;
            $notify_url=url('admin/webank/ali_callback');
            $pay_type='801';
        }else{
            $wx_user_data = $request->session()->get('wx_user_data');
            $user_id=$wx_user_data[0]['id'];
            $notify_url=url('admin/webank/wx_callback');
            $pay_type='802';
        }
        try{
            if($storeunion&&$storeunion->wb_merchant_id){
                $webank=$this->WebankHelper($type);
                $app_id = $webank->appId;
                $version = $webank->version;
                $nonce = $webank->getNonce();
                $order_id=$webank->getOrderNum('b');
                $ip=$request->getClientIp();
                $data=[
                    'orderId'=>$order_id,
                    'wbMerchantId'=>$storeunion->wb_merchant_id,
                    'totalAmount'=>$total_amount,
                    'subject'=>$wbstore->alias_name.'二维码收款',
                    'operatorId'=>$m_id,
                    'storeId'=>$wbstore->store_id,
                    'spbillCreateIp'=>$ip,
                    'subAppid'=>$webank->wx_app_id,
//                'userId'=>'2088102172192852',
                    'userId'=>$user_id,
                    'notifyUrl'=>$notify_url
                ];
                $jsonData=json_encode($data,true);
                $params = array($app_id, $version, $nonce, $jsonData);
                $sign = $webank->getSign($params);
                if (!$sign) {
                    Log::error("Sign is empty!");
                    return array(
                        'code' => '-2',
                        'msg' => '签名计算失败！'
                    );
                }
                $url_params = sprintf(self::COMMON_SIGN_FORMAT, $app_id, $nonce, $version, $sign);
                $header = ['Content-Type: application/json'];
                $request = array(
                    'url' => $webank->headUrl.self::WBPAY_CODE_PATH . $url_params,
                    'method' => 'post',
                    'timeout' => self::$timeout,
                    'data' => $jsonData,
                    'header' => $header,
                );
                $result = $webank->sendRequest($request);
                if ($result['code'] == 0&&$result['success']) {
                    $ist=[
                        'out_trade_no'=>$order_id,
//                    'trade_no'=>$result['channelNo'],
                        'store_id'=>$store_id,
                        'merchant_id'=>$m_id,
                        'type'=>$pay_type,
                        'total_amount'=>$total_amount,
                        'buyer_id'=>$user_id,
                        'pay_status'=>3,
                        'remark'=>$remark
                    ];
                    $res=Order::create($ist);
                    if($res){
                        return json_encode([
                            'success'=>1,
                            'payInfo'=>$result['payInfo']
                        ]);
                    }
                }else{
                    Log::info($request);
                    Log::error($result);
                    $info=$result['msg'];
                }
            }
            return json_encode([
                'success'=>0,
                'msg'=>$info
            ]);
        }catch (Exception $e){
            Log::error($e);
            $info=$e;

        }
        return json_encode([
            'success'=>0,
            'msg'=>$info
        ]);
    }
    public function alipaysuccess(Request $request){
        $price=$request->price;
        return view('admin.webank.alipay.pay_success',compact('price'));
    }
    public function alipayerror(Request $request){
        $code=$request->code;
        return view('admin.webank.alipay.pay_error',compact('code'));
    }
    public function weixinPay(Request $request){
        $store_id = $request->get('store_id');//商户号
        $m_id=$request->get('m_id');//收银员
        $shop = WeBankStore::where('store_id', $store_id)->first();
        $storeunion=DB::table('we_bank_storeunion')->where('store_id',$store_id)->where('product_type','004')->first();
        try{
            if(empty($storeunion->wb_merchant_id)){
                $wbstorecontroller=new StoreController();
                $data=[];
                $alipartid='wx'.date('YmdHis').rand(100,999);
                $data['store_id']=$alipartid;
                $data['id_type']=$shop->id_type;
                $data['id_no']=$shop->id_no;
                $data['merchant_name']=$shop->store_name;
                $data['alias_name']=$shop->alias_name;
                $data['licence_no']=$shop->licence_no;
                $data['contact_name']=$shop->contact_name;
                $data['contact_phone']=$shop->contact_phone;
                $data['merchant_type_code']=$shop->merchant_type_code;
                $data['wx_category_id']=$storeunion->category_id;
                $data['account_no']=$shop->account_no;
                $data['account_opbank_no']=$shop->account_opbank_no;
                $data['account_name']=$shop->account_name;
                $data['account_opbank']=$shop->account_opbank;
                $data['acct_type']=$shop->acct_type;
                $data['service_phone']=$shop->service_phone;
                $data['district']='0755';
                $data['payment_type']=($storeunion->payment_type=='23'||$storeunion->payment_type=='25')?1:2;
                $cityname=ProvinceCity::where('areaCode',$shop->city_code)->first();
                if($cityname){
                    $district=DB::table('we_bank_district')->where('district',$cityname)->first();
                    if($district)
                        $data['district']=$district->district_code;
                }
                $res=$wbstorecontroller->registerapi($data,1);
                if($res['code'] == 0&&$res['success']){
                    DB::table('we_bank_storeunion')->where('store_id',$store_id)->where('product_type','004')->update(['wb_merchant_id'=>$res['wbMerchantId'],'partner_mch_id'=>$alipartid]);
                }
            }
        }catch (Exception $e){
            Log::info($e);
        }

        return view('admin.webank.weixin.wxpay_view', compact('shop','m_id'));
    }
    public function wxpaysuccess(Request $request){
        $price=$request->price;
        return view('admin.webank.weixin.pay_success',compact('price'));
    }
    public function wxpayerror(Request $request){
        $code=$request->code;
        return view('admin.webank.weixin.pay_error',compact('code'));
    }
}