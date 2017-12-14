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
use Excel;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

use App\Http\Controllers\Controller;

class WeixinLsController extends Controller
{
    //商户微信流水账单
    public function weixinls(Request $request)
    {
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
                ->join('weixin_shop_lists', 'merchant_shops.store_id', '=', 'weixin_shop_lists.store_id')
                ->where('merchant_shops.merchant_id', auth()->guard('merchant')->user()->id)
                ->select("merchant_shops.store_id","weixin_shop_lists.pid","weixin_shop_lists.id")
                ->first();
        $b=DB::table("merchants")
            ->where("id",auth()->guard('merchant')->user()->id)
            ->first();
        //所有收银员
        $cashier=DB::table("merchants")->select("name","id")->get();
        foreach($cashier as $v){
            $cashier[$v->id]=$v->name;
        }
        $array=[201,202,203];
        if($a){
            if($a->pid==0&&$b->type==0){
                //分店store_id
                $data[]=$a->store_id;
                $c=DB::table("weixin_shop_lists")->where("pid",$a->id)->get();
                if($c){
                    foreach($c as $v){
                     $data[]=$v->store_id;
                    }
                }
                $list=DB::table("weixin_shop_lists")
                    ->join("orders","weixin_shop_lists.store_id","=","orders.store_id")
                    ->whereIn("orders.store_id",$data)
                    ->whereIn("orders.pay_status",$where)
                    ->whereIn("orders.type",$array)
                    ->orderBy("orders.updated_at","desc")
                    ->paginate(9);
               return view("merchant.weixinls",compact("list","cashier","status"));
       }
        if($a->pid!=0&&$b->type==0){
            $list=DB::table("weixin_shop_lists")
                ->join("orders","weixin_shop_lists.store_id","=","orders.store_id")
                ->where("orders.store_id",$a->store_id)
                ->whereIn("orders.pay_status",$where)
                ->whereIn("orders.type",$array)
                ->orderBy("orders.updated_at","desc")
                ->paginate(9);
            return view("merchant.weixinls",compact("list","cashier","status"));
        }
            if($b->type!=0){
                $list=DB::table("weixin_shop_lists")
                    ->join("orders","weixin_shop_lists.store_id","=","orders.store_id")
                    ->where("orders.merchant_id",auth()->guard('merchant')->user()->id)
                    ->whereIn("orders.pay_status",$where)
                    ->whereIn("orders.type",$array)
                    ->orderBy("orders.updated_at","desc")
                    ->paginate(9);
                return view("merchant.weixinls",compact("list","cashier","status"));
            }
        }else{
           $list=null;
            $cashier=null;
            return view("merchant.weixinls",compact("list","cashier","status"));
        }
    }
}