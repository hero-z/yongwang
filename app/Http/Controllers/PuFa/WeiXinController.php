<?php
/**
 * 测试微信授权二维码并收款
 */

namespace App\Http\Controllers\PuFa;


use App\Models\PinganTradeQueries;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use App\Models\PufaStores;
use Illuminate\Http\Request;

class WeiXinController extends Controller
{
    /*
        微信的用户授权
    */
    function userwxauth()
    {

        $merchant_id = '7551000001';//商家在浦发的商户号    pufa_stores      merchant_id
        $auth_shop_name='cc测试收款';
        $code_url=url('admin/weixin/oauth?sub_info=PF_' . $merchant_id);

        return view('pufa.alipayopen.onlyskm', compact('code_url', 'auth_shop_name'));
/*
            // if ($pay_type == "weixin") {
                $merchant_id = '101520000465';//商户id
                $code_url = url('admin/weixin/oauth?sub_info=PF_' . $merchant_id);
                return redirect($code_url);

            // }

*/

    }


}