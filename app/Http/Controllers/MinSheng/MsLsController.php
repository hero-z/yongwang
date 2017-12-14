<?php
/**
 * Created by PhpStorm.
 * User: hero
 * Date: 2017/6/30
 * Time: 10:45
 */

namespace App\Http\Controllers\MinSheng;


use App\Merchant;
use App\Models\MerchantShops;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class MsLsController
{
    public function ls(Request $request){
        $status=$request->get('status',1);
        $typein=[501,502];
        $where[]=['pay_status',$status];
        $storeids=[];
        $storenames=[];
        $m_id=auth()->guard('merchant')->user()->id;
        $merchant=Merchant::where('id',$m_id)->first();
        $cashier=[];
        if($merchant){
            //收银员
            $cashier=DB::table("merchants")->select("name","id")->get();
            foreach($cashier as $v){
                $cashier[$v->id]=$v->name;
            }
            //店铺名称
            $merchantstore=MerchantShops::where('merchant_id',$m_id)->where('store_type','ms')->select('store_id','store_name')->first();
            if($merchantstore){
                $storeids[]=$merchantstore->store_id;
                $storenames[$merchantstore->store_id]=$merchantstore->store_name;

                $merchantpstore=DB::table('ms_stores')->where('store_id',$merchantstore->store_id)->first();
                if($merchantpstore){
                    $children=DB::table('ms_stores')->where('pid',$merchantpstore->id)->get();
                    foreach ($children as $v){
                        $storeids[]=$v->store_id;
                        $storenames[$v->store_id]=$v->store_name;
                    }
                }
            }
            if($merchant->type==0){
                $list=Order::whereIn('store_id',$storeids)
                    ->whereIn('type',$typein)
                    ->where($where)
                    ->select("orders.remark","orders.type",'store_id',"orders.total_amount","orders.pay_status","orders.out_trade_no","orders.updated_at","orders.merchant_id")
                    ->orderBy("updated_at","desc")
                    ->paginate(9);
            }else{
                $list=Order::where('merchant_id',$m_id)->whereIn('type',$typein)->where($where)->paginate(9);
            }
        }
        return view('merchant.msls',compact('list','status','storenames','cashier'));
    }
}