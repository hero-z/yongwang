<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/11
 * Time: 11:01
 */

namespace App\Http\Controllers\AlipayWeixin;


use App\Http\Controllers\Controller;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayShopLists;
use App\Models\MultiCode;
use App\Models\PinganConfig;
use App\Models\PinganStore;
use App\Models\WeixinShopList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AwQrController extends Controller
{

    public function qrCode(Request $request)
    {
        $id = $request->get('id');
        $store = MultiCode::Where('id', $id)->first();
        $store_name = $store->store_name;
        $code_url = url('/admin/alipayweixin/pay?id=' . $id);
        return view('admin.alipayweixin.qrCode', compact('store_name', 'code_url'));
    }

    public function pay(Request $request)
    {
        $id = $request->get('id');
        $store = MultiCode::Where('id', $id)->first();
        $pay_type = "other";
        $config = AlipayIsvConfig::where('id', 1)->first();
        if ($config) {
            $config = $config->toArray();
        }
        $config['app_auth_url'] = Config::get('alipayopen.app_auth_url');
        //判断是不是微信
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $pay_type = 'weixin';
        }
        //判断是不是支付宝
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $pay_type = 'alipay';
        }
       //判断是不是京东
       if(strpos($_SERVER['HTTP_USER_AGENT'], 'WalletClient') !==false){
           $pay_type="jd";
       }
        //判断是不是翼支付
        if(strpos($_SERVER['HTTP_USER_AGENT'],"Bestpay")!==false){
           $pay_type="bestpay";
        }
        if ($pay_type == "other") {
            // dd('请用支付宝或者微信扫描二维码');
        }

        //支付宝
        if ($pay_type == 'alipay') {
            //官方支付宝 当面付
            if ($store->alipay_ways == "oalipay") {
                $code_url = $config['app_auth_url'] . '?app_id=' . $config['app_id'] . "&redirect_uri=" . $config['callback'] . '&scope=auth_base&state=OSK_' . $store->store_id_a.'_';
                return redirect(url($code_url));
            }
            //官方支付宝 口碑
            if ($store->alipay_ways == "salipay") {
                $u_id = AlipayShopLists::where('store_id', $store->store_id_a)->first()->id;
                $code_url = $config['app_auth_url'] . '?app_id=' . $config['app_id'] . "&redirect_uri=" . $config['callback'] . '&scope=auth_base&state=SXD_' . $u_id;
                return redirect(url($code_url));
            }

            //平安支付宝
            if ($store->alipay_ways == "palipay") {
                $code_url = $config['app_auth_url'] . '?app_id=' . $config['app_id'] . "&redirect_uri=" . $config['callback'] . '&scope=auth_base&state=PA_' . $store->store_id_a;
                return redirect($code_url);
            }
            //浦发支付宝
            if($store->alipay_ways=="pfalipay"){
                $code_url = $config['app_auth_url'] . '?app_id=' . $config['app_id'] . "&redirect_uri=" . $config['callback'] . '&scope=auth_base&state=PF_' . $store->store_id_a;
                return redirect($code_url);
            }
        }
        //微信
        if ($pay_type == 'weixin') {
            //官方微信
            if ($store->weixin_ways == "weixin") {
                //兼容旧版本
                $code_url = url('admin/weixin/oauth?sub_info=pay_' . $store->store_id_w);
                return redirect($code_url);
            }

            //平安微信
            if ($store->weixin_ways == "pweixin") {
                $code_url = url('admin/weixin/oauth?sub_info=PPay_' .$store->store_id_w);
                return redirect($code_url);
            }
            //浦发微信
            if($store->weixin_ways=="pfweixin"){
                $code_url = url('admin/weixin/oauth?sub_info=PF_' .$store->store_id_w);
                return redirect($code_url);
            }
        }
        //京东
        if($pay_type=="jd"){
            //平安京东
            if($store->jd_ways=="pjd"){
                $external_id=$store->store_id_j;
                $code_url=url('admin/pingan/jdpay_view?external_id='.$external_id);
                return redirect($code_url);
            }
        }
        if($pay_type=="bestpay"){
            //平安翼支付
            if($store->bestpay_ways=="pbestpay"){
                $external_id = $store->store_id_b;
                $code_url=url('admin/pingan/pay_view?external_id='.$external_id);
                return redirect($code_url);
            }
        }
    }
    

}