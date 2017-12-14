<?php
/**
 * Created by PhpStorm.
 * User: hero
 * Date: 2017/5/23
 * Time: 10:43
 */

namespace App\Http\Controllers\WeBank;


use App\Models\AlipayIsvConfig;
use App\Models\QrListInfo;
use App\Models\WeBankStore;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

class WebankQrController  extends BaseController
{
    public function webankQrCode(Request $request){
        /*$m_id = auth()->guard('merchant')->user()->id;
        //已经注册
        $webankinfo = MerchantShops::where('merchant_id', $m_id)->where('store_type', 'like','webank_%')->first();
        if ($webankinfo) {
            $store_id = $webankinfo->store_id;
            $qrinfo = QrListInfo::where('store_id', $store_id)->first();
            if ($qrinfo) {
                $code_url = url('admin/webank/webankQrCode?code_number=' . $qrinfo->code_number);
                $store_name = $webankinfo->store_name;
                return view('admin.webank.myqr', compact('code_url', 'store_name','store_id'));
            } else {
                $info='收款信息不存在';
            }
            return view('admin.bank.error',compact('info'));
            //如果有推广员就是加一个USer_id
        } else {
            //未注册
            //获取省份
            $provincelists=ProvinceCity::where('areaParentId',1)->select('areaCode','areaName')->get();
            $user_id='';
            return view('merchant.pinganstore.autostore',compact('user_id','provincelists'));
        }*/
        $code_number = $request->get('code_number');//获得空码编号
        $pay_type = "other";
        $code_from = $request->get('code_from');
//        dd($_SERVER['HTTP_USER_AGENT']);
        //判断是不是微信
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $pay_type = 'weixin';
        }
        //判断是不是支付宝
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $pay_type = 'alipay';
        }
        if ($pay_type == "other") {
            die('请用支付宝或者微信扫描二维码');
        }
        try {
            $info = DB::table('wb_qr_list_infos')->where('code_number', $code_number)->first();
            if($info){
                //空码
                if ($info->code_type == 0) {
                    return redirect(route('webankregister',['code_from'=>$code_from,'user_id'=>$info->user_id,'code_number'=>$code_number]));
                }
                //付款码
                if ($info->code_type == 1) {
                    $store = WeBankStore::where('store_id', $info->store_id)->first();
                    if ($store->pay_status == 0) {
                        die('付款码状态关闭！请联系客服！') ;
                    }
                    if ($store->is_delete){
                        die('该商户已经被删除！请恢复');
                    }
                    $merchant_id=$request->get('merchant_id');
                    //支付宝付款
                    if ($pay_type == "alipay") {
                        $store_id = $store->store_id;
                        $config = AlipayIsvConfig::where('id', 1)->first()->toArray();//支付宝配置信息app_id
                        $config['app_auth_url'] = Config::get('alipayopen.app_auth_url');
                        $code_url = $config['app_auth_url'] . '?app_id=' . $config['app_id'] . "&redirect_uri=" . $config['callback'] . '&scope=auth_base&state=WB_' . $store_id.'_'.$merchant_id;
                        return redirect($code_url);
                    }
                    //微信付款
                    if ($pay_type == "weixin") {
                        $store_id = $store->store_id;
                        $code_url = url('admin/weixin/oauth?sub_info=WB_' . $store_id.'_'.$merchant_id);
                        return redirect($code_url);
                    }
                }
            }else{
                die('二维码有误！请联系服务商重新换取新的二维码！') ;
            }

        } catch (\Exception $exception) {
            Log::info($exception);
        }

    }

}