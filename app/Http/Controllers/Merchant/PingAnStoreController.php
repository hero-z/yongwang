<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/1/17
 * Time: 23:08
 */

namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\PingAn\AopClient;
use App\Http\Controllers\PingAn\BaseController;
use App\Http\Controllers\PingAn\WitnessController;
use App\Models\MerchantShops;
use App\Models\PingancqrLsitsinfo;
use App\Models\PinganStore;
use App\Models\PinganStoreInfos;
use App\Models\PinganWitnessAccount;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PingAnStoreController extends BaseController

{
    //提交店铺信息
    public function autoStore()
    {
        $pay_type = "other";
        //判断是不是微信
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $pay_type = 'weixin';
        }
        //判断是不是支付宝
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $pay_type = 'alipay';
        }

        if ($pay_type == "other") {

            return '请用支付宝或者微信扫描二维码';

        }
        return view('merchant.pinganstore.autostore');
    }

    //自主提交到后台保存
    public function autoStorePost(Request $request)
    {
        $store = $request->except(['_token', 'user_id', 'code_number', 'sfz1', 'sfz2', 'orther1', 'province_code', 'city_code', 'district_code']);
        //检查系统店铺是否存在
        $s = PinganStore::where('alias_name', $store['alias_name'])->first();
        $m_id = auth()->guard('merchant')->user()->id;
        if ($s) {
            return json_encode([
                'success' => false,
                'error_message' => '店铺已经存在！请联系服务商'
            ]);
        }
        if ($request->input('user_id', 1)) {
            //检查用户是否存在
            $u = User::where('id', $request->input('user_id'))->first();
            if (!$u) {
                return json_encode([
                    'success' => false,
                    'error_message' => '推广员不存在'
                ]);
            }
        }
        //见证宝cust_id
        $witnessaccount=PinganWitnessAccount::where('merchant_id',$m_id)->first();
        if($witnessaccount&&$witnessaccount->cust_id){
            //商户已经有见证宝
            $store['jzb_cust_id']=$witnessaccount->cust_id;
        }else{
            //注册见证宝
            $witnesscmd=new WitnessController();
            $resarr=$witnesscmd->create($store['alias_name']);
            if($resarr['success']&&$resarr['return_value']){
                $store['jzb_cust_id']=$resarr['return_value']['cust_id'];
                if($witnessaccount){
                    $witnessaccount->update(['cust_id'=>$store['jzb_cust_id']]);
                }else{
                    PinganWitnessAccount::create([
                        'merchant_id'=>$m_id,
                        'cust_id'=>$store['jzb_cust_id'],
                        'nick_name'=>$store['alias_name'],
                    ]);
                }
            }else{
                return json_encode([
                    'success' => false,
                    'error_message' => '创建见证宝账号失败!'.(array_key_exists('error_message',$resarr)?$resarr['error_message']:'')
                ]);
            }
        }
        $store['contact_name'] = $request->id_card_name;
        $store['contact_phone'] = $request->service_phone;
        $store['contact_mobile'] = $request->service_phone;
        $store['contact_email'] = $request->service_phone . '@163.com';
        $aop = $this->AopClient();
        $aop->method = "fshows.liquidation.submerchant.create.with.auth";
        $data = array('content' => json_encode($store));
        try {
            $response = $aop->execute($data);
            $responseArray = json_decode($response, true);
        } catch (\Exception $exception) {
            return '系统超时！请刷新再试';
        }
        if ($responseArray['success']) {

            $updateData['external_id'] = $request->external_id;
            $updateData['sfzname'] = $request->id_card_name;
            $updateData['sfzno'] = $request->id_card_num;
            $updateData['address'] = $request->store_address;
            $updateData['sfz1'] = $request->sfz1;
            $updateData['sfz2'] = $request->sfz2;
            $updateData['sfz3'] = $request->id_card_hand_img_url;
            $updateData['main_image'] = $request->store_front_img_url;
            $updateData['licence'] = $request->business_license_img_url;
            $updateData['orther1'] = $request->orther1;
            $updateData['province_code'] = $request->province_code;
            $updateData['city_code'] = $request->city_code;
            $updateData['district_code'] = $request->district_code;
            $updateData['sub_status'] = $responseArray['return_value']['status'];
            $storeinfos = PinganStoreInfos::where('external_id', $store['external_id'])->first();
            if ($storeinfos) {
                if (!$storeinfos->sub_status) {
                    PinganStoreInfos::where('external_id', $store['external_id'])->update($updateData);
                } else {
                    return json_encode([
                        'success' => false,
                        'error_message' => '请勿重复提交！'
                    ]);
                }
            } else {
                PinganStoreInfos::create($updateData);
                PinganStoreInfos::where('external_id', $store['external_id'])->update($updateData);
            }
            $storeUpdateData = $request->only('external_id', 'name', 'alias_name', 'service_phone', 'category_id');
            $storeUpdateData['user_id'] = $request->get('user_id', 1);
            $storeUpdateData['user_name'] = User::where('id', $request->get('user_id', 1))->first()->name;;
            $storeUpdateData['sub_merchant_id'] = $responseArray['return_value']['sub_merchant_id'];
            $storeUpdateData['contact_name'] = $request->id_card_name;
            $storeUpdateData['contact_phone'] = $request->service_phone;
            $storeUpdateData['contact_mobile'] = $request->service_phone;
            $storeUpdateData['contact_email'] = $request->service_phone . '@163.com';
            $storeUpdateData['cust_id'] = $store['jzb_cust_id'];
            $storeinfo = PinganStore::where('external_id', $store['external_id'])->first();
            if ($storeinfo) {
                PinganStore::where('external_id', $store['external_id'])->update($storeUpdateData);
            } else {
                PinganStore::create($storeUpdateData);
                PinganStore::where('external_id', $store['external_id'])->update($storeUpdateData);
            }
            try {
                $code_number=time() . rand(1000, 9999);
                //添加商户收款码
                PingancqrLsitsinfo::create([
                    'store_id' => $request->external_id,
                    'code_number' => $code_number,
                    'store_name' => $request->alias_name,
                    'user_id' => $storeUpdateData['user_id'],
                    'user_name' =>$storeUpdateData['user_name'],
                    'from_info' => 'pingan',
                    'code_type'=>1

                ]);
            } catch (\Exception $exception) {
                return json_encode([
                    'success' => 0,
                    'error_message' => '修改二维码状态失败！'
                ]);
            }
            try {
                //关联商户id
                MerchantShops::create([
                    'merchant_id' => $m_id,
                    'store_id' => $request->external_id,
                    'store_name' => $request->alias_name,
                    'store_type' => 'pingan',
                    'desc_pay' => '平安通道',
                    'status' => 1
                ]);
            } catch (\Exception $exception) {
                return json_encode([
                    'success' => 0,
                    'error_message' => '关联商户id保存失败！'
                ]);
            }
        }


        return $response;
    }
    /*public function autoStorePost(Request $request)
    {
        $store = $request->except(['_token', 'user_id', 'code_number', 'sfz1', 'sfz2', 'orther1','province_code','city_code','district_code']);
        //检查系统店铺是否存在
        $s = PinganStore::where('alias_name', $store['alias_name'])->first();
        if ($s) {
            return json_encode([
                'success' => false,
                'error_message' => '店铺已经存在！请联系服务商'
            ]);
        }

        if ($request->input('user_id',1)) {
            //检查用户是否存在
            $u = User::where('id', $request->input('user_id'))->first();
            if (!$u) {
                return json_encode([
                    'success' => false,
                    'error_message' => '推广员不存在'
                ]);
            }
        }
        $store['contact_name']=$request->id_card_name;
        $store['contact_phone']=$request->service_phone;
        $store['contact_mobile']=$request->service_phone;
        $store['contact_email']=$request->service_phone.'@163.com';
        $ao = new BaseController();
        $aop = $ao->AopClient();
        $aop->method = "fshows.liquidation.submerchant.create.with.auth";
        $data = array('content' => json_encode($store));
        try {
            $response = $aop->execute($data);
            $responseArray = json_decode($response, true);
        } catch (\Exception $exception) {
            return '系统超时！请刷新再试';
        }
        if ($responseArray['success']) {

            $updateData['external_id']=$request->external_id;
            $updateData['sfzname']=$request->id_card_name;
            $updateData['sfzno']=$request->id_card_num;
            $updateData['address']=$request->store_address;
            $updateData['sfz1']=$request->sfz1;
            $updateData['sfz2']=$request->sfz2;
            $updateData['sfz3']=$request->id_card_hand_img_url;
            $updateData['main_image']=$request->store_front_img_url;
            $updateData['licence']=$request->business_license_img_url;
            $updateData['orther1']=$request->orther1;
            $updateData['province_code']=$request->province_code;
            $updateData['city_code']=$request->city_code;
            $updateData['district_code']=$request->district_code;
            $updateData['sub_status']=$responseArray['return_value']['status'];
            $storeinfos = PinganStoreInfos::where('external_id', $store['external_id'])->first();
            if ($storeinfos) {
                if(!$storeinfos->sub_status){
                    PinganStoreInfos::where('external_id', $store['external_id'])->update($updateData);
                }else{
                    return json_encode([
                        'success' => false,
                        'error_message' => '请勿重复提交！'
                    ]);
                }
            } else {
                PinganStoreInfos::create($updateData);
                PinganStoreInfos::where('external_id', $store['external_id'])->update($updateData);
            }
            $storeUpdateData=$request->only('external_id','name','alias_name','service_phone','category_id');
            $storeUpdateData['user_id'] = $request->get('user_id',1);
            $storeUpdateData['user_name'] = User::where('id', $request->get('user_id', 1))->first()->name;;
            $storeUpdateData['sub_merchant_id'] = $responseArray['return_value']['sub_merchant_id'];
            $storeUpdateData['contact_name']=$request->id_card_name;
            $storeUpdateData['contact_phone']=$request->service_phone;
            $storeUpdateData['contact_mobile']=$request->service_phone;
            $storeUpdateData['contact_email']=$request->service_phone.'@163.com';
            $storeinfo = PinganStore::where('external_id', $store['external_id'])->first();
            if ($storeinfo) {
                PinganStore::where('external_id', $store['external_id'])->update($storeUpdateData);
            } else {
                PinganStore::create($storeUpdateData);
                PinganStore::where('external_id', $store['external_id'])->update($storeUpdateData);
            }
            $code_number=time() . rand(1000, 9999);
            try {
                //添加商户收款码
                PingancqrLsitsinfo::create([
                    'store_id' => $request->external_id,
                    'code_number' => $code_number,
                    'store_name' => $request->alias_name,
                    'user_id' => $storeUpdateData['user_id'],
                    'user_name' =>$storeUpdateData['user_name'],
                    'from_info' => 'pingan',
                    'code_type'=>1

                ]);
            } catch (\Exception $exception) {
                return json_encode([
                    'success' => 0,
                    'error_message' => '修改二维码状态失败！'
                ]);
            }
            try {
                //关联商户id
                $m_id = auth()->guard('merchant')->user()->id;
                MerchantShops::create([
                    'merchant_id' => $m_id,
                    'store_id' => $request->external_id,
                    'store_name' => $request->alias_name,
                    'store_type' => 'pingan',
                    'desc_pay' => '平安通道',
                    'status' => 1
                ]);
            } catch (\Exception $exception) {
                return json_encode([
                    'success' => 0,
                    'error_message' => '关联商户id保存失败！'
                ]);
            }
        }
        return $response;
    }*/
    public function autom()
    {
        $pay_type = "other";
        //判断是不是微信
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $pay_type = 'weixin';
        }
        //判断是不是支付宝
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $pay_type = 'alipay';
        }

        if ($pay_type == "other") {

             return '请用支付宝或者微信扫描二维码';

        }
        //获取银行列表
        $banks=DB::table('zjjz_banks')->select('bank_code','bank_name','bank_no')->get();
        if($banks){
            $banks=$banks->toArray();
        }
        //获取省份
        $province=DB::table('pub_pay_node')->pluck('node_nodename','node_nodecode');
        if($province){
            $province=$province->toArray();
        }
        return view('merchant.pinganstore.autom',compact('banks','province'));
    }

    //提交绑定银行卡
    public function automPost(Request $request)
    {
        $external_id = $request->get('external_id');
//        $code_number = $request->get('code_number');
        $ao = new BaseController();
        $aop = $ao->AopClient();
        $aop->method = 'fshows.liquidation.submerchant.bank.bind';
        if ($request->get('is_public_account') == 1) {
            $content = [
                'is_public_account' => 1,
                'open_bank' => $request->get('open_bank')
            ];
        }
        $store = PinganStore::where('external_id', $external_id)->first();
        $content['sub_merchant_id'] = $store->sub_merchant_id;
        $content['bank_card_no'] = $request->get('bank_card_no');
        $content['card_holder'] = $request->get('card_holder');
        $data = array('content' => json_encode($content));
        $response = $aop->execute($data);
        $responseArray = json_decode($response, true);
        if ($responseArray['success']) {//绑卡成功
            try {
                PinganStore::where('external_id', $external_id)->update($content);//修改商户信息
            } catch (\Exception $exception) {
                return json_encode([
                    'success' => 0,
                    'error_message' => '修改商户信息保存失败！'
                ]);
            }
        }
        return $response;
    }

    //第三步上传资质文件

    public function autoFile(Request $request)
    {
        $pay_type = "other";
        //判断是不是微信
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $pay_type = 'weixin';
        }
        //判断是不是支付宝
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $pay_type = 'alipay';
        }

        if ($pay_type == "other") {

           return '请用支付宝或者微信扫描二维码';


        }
        try {
            $StoreInfos = PinganStoreInfos::where('external_id', $request->get('external_id'))->first();
            if ($StoreInfos) {
                $StoreInfos = $StoreInfos->toArray();
                return view('merchant.pinganstore.autoFileTwo', compact('StoreInfos'));
            } else {
                return view('merchant.pinganstore.autoFile');
            }
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    public function autoFilePost(Request $request)
    {
        $external_id = $request->get('external_id');
        $code_number = $request->get('code_number');
        $PinganStore = PinganStore::where('external_id', $external_id)->first();
        if ($PinganStore) {
            try {
                $pInfo = PinganStoreInfos::where('external_id', $external_id)->first();
                if ($pInfo) {
                    PinganStoreInfos::where('external_id', $external_id)->update($request->except(['_token', 'code_number']));
                } else {
                    PinganStoreInfos::create($request->except(['_token', 'code_number']));
                }
            } catch (\Exception $exception) {
                return json_encode([
                    'success' => 0,
                    'error_message' => '200信息保存失败！'
                ]);
            }
            try {
                //修改二维码为商户收款码
                PingancqrLsitsinfo::where('code_number', $code_number)->update([
                    'store_id' => $external_id,
                    'code_type' => 1,
                    'store_name' => $PinganStore->alias_name
                ]);
            } catch (\Exception $exception) {
                return json_encode([
                    'success' => 0,
                    'error_message' => '2001保存失败！'
                ]);
            }
        }
        return json_encode([
            'success' => 1,
        ]);

    }
}