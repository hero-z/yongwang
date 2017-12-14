<?php
/**
 * 浦发支付宝授权二维码的生成，测试用！
 */

namespace App\Http\Controllers\PuFa;


use App\Models\AlipayIsvConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\PufaStores;
use App\Http\Controllers\Controller;

class AlipayQrController extends Controller
{

    //生成商户收款二维码
    public function OnlySkm(Request $request)
    {
        $merchant_id = $request->get('u_id');//商户号(支付宝是否授权皆可以)
        $config = AlipayIsvConfig::where('id', 1)->first()->toArray();//使用相同的支付宝配置
        $shopinfo = PufaStores::where('merchant_id', $merchant_id)->first();//商户资料
        if ($shopinfo) {
            $auth_shop_name = $shopinfo->toArray()['store_name'];
        } else {
            $auth_shop_name = "无效商户二维码";
        }
        $config['app_auth_url'] = Config::get('alipayopen.app_auth_url');
        $code_url = $config['app_auth_url'] . '?app_id=' . $config['app_id'] . "&redirect_uri=" . $config['callback'] . '&scope=auth_base&state=PF_' . $merchant_id;

        // echo $code_url;die;
        return view('pufa.alipayopen.onlyskm', compact('code_url', 'auth_shop_name'));

    }
}