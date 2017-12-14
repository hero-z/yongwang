<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/2/26
 * Time: 17:39
 */

namespace App\Http\Controllers\Merchant;

use App\Models\MerchantShops;
use DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PinganLsController extends Controller
{

    //商户微信流水账单
    public function pinganls(Request $request){
        //所有收银员
        $cashier=DB::table("merchants")->select("name","id")->get();
        foreach($cashier as $v){
            $cashier[$v->id]=$v->name;
        }
        $array=[301,302,303,304,305,306,307];
        $status=$request->get("status");
        $where=[1,2,3,4,5];
        if($status){
            if($status==1){
                $where=[1];
            }else{
                $where=[2,3,4,5];
            }
        }
        //判断登陆类型;
        $a=DB::table("merchant_shops")
            ->join('pingan_stores', 'merchant_shops.store_id', '=', 'pingan_stores.external_id')
            ->where('merchant_shops.merchant_id', auth()->guard('merchant')->user()->id)
            ->select("merchant_shops.store_id","pingan_stores.pid","pingan_stores.id")
            ->first();
        $b=DB::table("merchants")
            ->where("id",auth()->guard('merchant')->user()->id)
            ->first();
        if($a){
            if($a->pid==0&&$b->type==0){
                //分店store_id
                $data[]=$a->store_id;
                $c=DB::table("pingan_stores")->where("pid",$a->id)->get();
                if($c){
                    foreach($c as $v){
                        $data[]=$v->external_id;
                    }
                }
                $list=DB::table("pingan_stores")
                    ->join("orders","pingan_stores.external_id","=","orders.store_id")
                    ->whereIn("orders.store_id",$data)
                    ->whereIn("orders.pay_status",$where)
                    ->whereIn("orders.type",$array)
                    ->select("orders.remark","orders.type","orders.total_amount","orders.pay_status","orders.out_trade_no","orders.updated_at","pingan_stores.alias_name","orders.merchant_id")
                    ->orderBy("orders.updated_at","desc")
                    ->paginate(9);
                return view("merchant.pinganls",compact("list","cashier","status"));
            }
            if($a->pid!=0&&$b->type==0){
                $list=DB::table("pingan_stores")
                    ->join("orders","pingan_stores.external_id","=","orders.store_id")
                    ->where("orders.store_id",$a->store_id)
                    ->whereIn("orders.pay_status",$where)
                    ->whereIn("orders.type",$array)
                    ->select("orders.remark","orders.type","orders.total_amount","orders.pay_status","orders.out_trade_no","orders.updated_at","pingan_stores.alias_name","orders.merchant_id")
                    ->orderBy("orders.updated_at","desc")
                    ->paginate(9);
                return view("merchant.pinganls",compact("list","cashier","status"));
            }
            if($b->type!=0){
                $list=DB::table("pingan_stores")
                    ->join("orders","pingan_stores.external_id","=","orders.store_id")
                    ->where("orders.merchant_id",auth()->guard('merchant')->user()->id)
                    ->whereIn("orders.pay_status",$where)
                    ->whereIn("orders.type",$array)
                    ->select("orders.remark","orders.type","orders.total_amount","orders.pay_status","orders.out_trade_no","orders.updated_at","pingan_stores.alias_name","orders.merchant_id")
                    ->orderBy("pingan_trade_queries.updated_at","desc")
                    ->paginate(9);
                return view("merchant.pinganls",compact("list","cashier","status"));
            }
        }else{
            $list=null;
            $cashier=null;
            return view("merchant.pinganls",compact("list","cashier","status"));
        }
    }


}