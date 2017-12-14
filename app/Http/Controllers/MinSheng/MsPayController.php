<?php

/*
	微信、支付宝  跳转

*/
namespace App\Http\Controllers\MinSheng;

use App\Http\Controllers\Controller;

use App\Http\Controllers\MinSheng\MinSheng;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class MsPayController extends Controller
{ 

    //判断支付方式并拼接授权地址---多码合一
    /*
        当只传code_number时，表示进件和收款
        当有传store_id时，表示使用该store_id付款------code_number传主店的

    */
    public function payway(Request $request)
    {

        $code_number = $request->get('code_number');//获得空码编号
        $cashier_id=$request->get('cashier_id','0');

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
            echo '请用支付宝或者微信扫描二维码';
        }

        try {
            $info = DB::table('mscqr_lsitsinfos')->where('code_number', $code_number)->first();
        } catch (\Exception $exception) {
            echo '<h1>商户二维码不存在！</h1>';die;
        }


        //空码  给商户注册门店资料
        if ($info->code_type == 0) {

            return redirect(url('/api/minsheng/info?user_id=' . $info->user_id . '&code_number=' . $code_number));
        }

        //付款码
        if ($info->code_type == 1) {
            try {

                    $main_store_id=$info->store_id;//主店的store_id
                    $store_id=$request->get('store_id','0');//分店的store_id

                    $main_store = DB::table('ms_stores')->where('store_id', $main_store_id)->first();
                    if(empty($main_store))
                    {
                        echo '<h1>商户不存在！</h1>';die;

                    }

                    if($store_id)
                    {
                         $store = DB::table('ms_stores')->where('store_id', $store_id)->first();
                        if(empty($store))
                        {
                            echo '<h1>商户不存在！</h1>';die;
                        }

                        if ($store->status != 2)
                        {
                            echo '<h1>付款码状态关闭！请联系商家！</h1>';die;
                        }                       
                    }
                    else
                    {
                        if ($main_store->status != 2)
                        {
                            echo '<h1>付款码状态关闭！请联系商家！</h1>';die;
                        } 
                    }


                    switch($pay_type)
                    {
                        // 支付宝支付
                        case 'alipay':

                                $paytype = DB::table("ms_pay_way")->where("store_id", $main_store_id)->where('pay_way','ZFBZF')->first();
                                if(empty($paytype))
                                {
                                    echo '<h1>商户未开通支付宝付款！</h1>';die;

                                }

                                if($paytype->status!='2')
                                {
                                    echo '<h1>商户支付宝正在申请！</h1>';die;

                                }

                                $config = DB::table('alipay_isv_configs')->where('id', 1)->first();//支付宝配置信息app_id
                                $app_auth_url= Config::get('alipayopen.app_auth_url');
                                if($store_id)
                                {
                                    $code_url = $app_auth_url . '?app_id=' . $config->app_id . "&redirect_uri=" . $config->callback . '&scope=auth_base&state=MS_' . $store_id.'_'.$cashier_id;
                                }
                                else
                                {
                                    $code_url = $app_auth_url . '?app_id=' . $config->app_id . "&redirect_uri=" . $config->callback . '&scope=auth_base&state=MS_' . $main_store_id.'_'.$cashier_id;
                                }
                                // echo $code_url;die;
                                return redirect($code_url);
                                break;
                        // 微信支付
                        case 'weixin':
                                $paytype = DB::table("ms_pay_way")->where("store_id", $main_store_id)->where('pay_way','WXZF')->first();
                                if(empty($paytype))
                                {
                                    echo '<h1>商户未开通微信付款！</h1>';die;

                                }

                                if($paytype->status!='2')
                                {
                                    echo '<h1>商户正在申请微信收款！</h1>';die;

                                }

                                if($store_id)
                                {
                                    $code_url = url("admin/weixin/oauth?sub_info=MS_{$store_id}_{$cashier_id}");
                                }
                                else
                                {
                                    $code_url = url("admin/weixin/oauth?sub_info=MS_{$main_store_id}_{$cashier_id}");
                                }
                                return redirect($code_url);
                                break;
                    }

            } catch (\Exception $e) {
                echo '<h1>系统错误！</h1>';
                die;
            }

        }

        echo '当前二维码没有绑定商户，请不要使用！';
	}

}