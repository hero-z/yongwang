<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/13
 * Time: 16:13
 */

namespace App\Http\Controllers\Merchant;


use App\Models\MerchantOrders;
use App\Models\MerchantShops;
use App\Models\Order;
use App\Models\PingancqrLsitsinfo;
use App\Models\PinganStore;
use App\Models\PinganTradeQueries;
use App\Models\ProvinceCity;
use Illuminate\Http\Request;

class PingAnController extends BaseController
{
    //平安我的收款码(公众号版);
    public function PingAnQr(Request $request)
    {
        $m_id = auth()->guard('merchant')->user()->id;
        //已经注册
        $PingAnSore = MerchantShops::where('merchant_id', $m_id)->where('store_type', 'pingan')->first();
        if ($PingAnSore) {
            $store_id = $PingAnSore->store_id;
            $PingancqrLsitsinfo = PingancqrLsitsinfo::where('store_id', $store_id)->first();
            if ($PingancqrLsitsinfo) {
                $code_url = url('Qrcode?code_number=' . $PingancqrLsitsinfo->code_number);
                $store_name = $PingancqrLsitsinfo->store_name;
                return view('merchant.pinganstore.myqr', compact('code_url', 'store_name','store_id'));
            } else {
                dd('信息不存在请联系服务商');
            }
            //如果有推广员就是加一个USer_id
        } else {
            //未注册
            //获取省份
            $provincelists=ProvinceCity::where('areaParentId',1)->select('areaCode','areaName')->get();
            $user_id='';
            return view('merchant.pinganstore.autostore',compact('user_id','provincelists'));
        }

    }

    //
    public function PingAnOrderList(Request $request)
    {

        $m_id = auth()->guard('merchant')->user()->id;
        $PingAnSore = MerchantShops::where('merchant_id', $m_id)->where('store_type', 'pingan')->first();
        if ($PingAnSore) {
            $store_id = $PingAnSore->store_id;
            $sum  = Order::where('store_id', $store_id)->
            whereIn('type',[301,302,303,304,305,306,307])->
            where('pay_status',1)->sum('total_amount');


            $order = Order::where('store_id', $store_id)->orderBy('created_at', 'desc')->paginate(6);


        } else {
            $sum=0;
            $order='';
        }
        return view('merchant.pinganstore.order', compact('sum', 'order'));

    }

}