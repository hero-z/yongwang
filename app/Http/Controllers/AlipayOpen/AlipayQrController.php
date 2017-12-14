<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2016/12/21
 * Time: 11:23
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Merchant;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayShopLists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class AlipayQrController extends AlipayOpenController
{


    public function Skm(Request $request)
    {
        $u_id = $request->get('id');//这个是系统商户列表的id
            $store_name=AlipayShopLists::where('id',$u_id)->first()->main_shop_name;
         $merchant_id=$request->get('merchant_id');
          $merchant_name="";
          if ($merchant_id){
            $merchant_name=Merchant::where('id',$merchant_id)->first()->name;
          }
            $config = AlipayIsvConfig::where('id', 1)->first();
            if ($config) {
                $config = $config->toArray();
            }
            $config['app_auth_url'] = Config::get('alipayopen.app_auth_url');
            $code_url = $config['app_auth_url'] . '?app_id=' . $config['app_id'] . "&redirect_uri=" . $config['callback'] . '&scope=auth_base&state=SXD_' . $u_id.'_'.$merchant_id;
        return view('admin.alipayopen.skm', compact('code_url','store_name',"merchant_name"));


    }

    //仅生成收款
    public function OnlySkm(Request $request)
    {
        $store_id= $request->get('store_id');//授权的user_id
       //存收银员信息
        $merchant_id=$request->get('merchant_id');
        $merchant_name="";
        if ($merchant_id){
            $merchant_name=Merchant::where('id',$merchant_id)->first()->name;
        }
        $config = AlipayIsvConfig::where('id', 1)->first();
        if ($config) {
            $config = $config->toArray();
        }
        $usersInfo = AlipayAppOauthUsers::where('store_id', $store_id)->first();
        if ($usersInfo) {
            $auth_shop_name = $usersInfo->toArray()['auth_shop_name'];
        } else {
            $auth_shop_name = "无效商户二维码";
        }
        $config['app_auth_url'] = Config::get('alipayopen.app_auth_url');
        $code_url = $config['app_auth_url'] . '?app_id=' . $config['app_id'] . "&redirect_uri=" . $config['callback'] . '&scope=auth_base&state=OSK_' . $store_id.'_'.$merchant_id;
        return view('admin.alipayopen.onlyskm', compact('code_url', 'auth_shop_name','merchant_name'));

    }
}