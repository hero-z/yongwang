<?php
/**
 * 生成浦发支付宝的商户下单页面--用户自行输入金额
 */

namespace App\Http\Controllers\PuFa;


// use App\Models\AlipayAppOauthUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PufaStores;

class PrepareDataController extends Controller
{
    // 浦发支付宝用户输入表单
    public function aliform(Request $request)
    {
        $store_id = $request->get('store_id');//服务商生成的商户id
        $cashier_id = $request->get('cashier_id');//商户的id，收款的商户
        $shopinfo = PufaStores::where('store_id', $store_id)->first();//商户资料
        if(!empty($shopinfo)) {
            $shopinfo = $shopinfo->toArray();
        }
        else
        {
            echo '<h1>商户不存在！</h1>';
            die;
        }
        
        return view('pufa.alipayopen.create_oqr_order', compact('shopinfo','cashier_id'));
    }
}