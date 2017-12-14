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

// 浦发状态映射
use App\Http\Controllers\PuFa\Map;

class PufaController extends Controller
{
    //商户浦发流水账单
    public function list(Request $request){
        $status=$request->get("status");
        $array=[601,602,603,604];
        $where=[1,2,3,4,5];
        if($status){
            if($status==1){
                $where=[1];
            }else{
                $where=[2,3,4,5];
            }
        }

        $merchant_id=auth()->guard('merchant')->user()->id;
        //判断登陆类型;
        // 店面信息
        $sql="select a.store_id,b.pid,b.id from merchant_shops a left join pufa_stores b on a.store_id=b.store_id where a.merchant_id={$merchant_id} limit 1";
        $a=DB::select($sql); 
        $a=array_shift($a);


        // 收银员信息
        $b=DB::table("merchants")->where("id",$merchant_id)->first();


        if($a){
            // 所有收银员
            $allmerchants=[];
            $merchants=DB::table("merchants")->select(['name','id'])->get();
            if($merchants)
            {
                foreach($merchants as $merchant)
                {
                    $allmerchants[$merchant->id]=$merchant->name;
                }
            }

            // 总店店长
            if($a->pid==0&&$b->type==0){
                //分店store_id
                $data[]=$a->store_id;
                $c=DB::table("pufa_stores")->where("pid",$a->id)->get();
                if($c){
                    foreach($c as $v){
                        $data[]=$v->store_id;
                    }
                }

                $list=DB::table("pufa_stores")
                    ->join("orders","pufa_stores.store_id","=","orders.store_id")
                    ->whereIn("orders.store_id",$data)
                    ->whereIn("orders.pay_status",$where)
                    ->whereIn("orders.type",$array)
                    ->select("orders.remark","orders.type","orders.total_amount","orders.pay_status","orders.out_trade_no","orders.updated_at","pufa_stores.merchant_short_name","orders.merchant_id")
                    ->orderBy("orders.updated_at","desc")
                    ->paginate(9);
                return view("merchant.pufals",compact("list",'allmerchants',"status"));
            }

            // 分店店长
            if($a->pid!=0&&$b->type==0){
                $list=DB::table("pufa_stores")
                    ->join("orders","pufa_stores.store_id","=","orders.store_id")
                    ->where("orders.store_id",$a->store_id)
                    ->whereIn("orders.pay_status",$where)
                    ->whereIn("orders.type",$array)
                    ->select("orders.remark","orders.type","orders.total_amount","orders.pay_status","orders.out_trade_no","orders.updated_at","pufa_stores.merchant_short_name","orders.merchant_id")
                    ->orderBy("orders.updated_at","desc")
                    ->paginate(9);
                return view("merchant.pufals",compact("list","allmerchants","status"));
            }
            // 其他收银员
            if($b->type!=0){
                $list=DB::table("pufa_stores")
                    ->join("orders","pufa_stores.store_id","=","orders.store_id")
                    ->where("orders.store_id",auth()->guard('merchant')->user()->id)
                    ->whereIn("orders.pay_status",$where)
                    ->whereIn("orders.type",$array)
                    ->select("orders.remark","orders.type","orders.total_amount","orders.pay_status","orders.out_trade_no","orders.updated_at","pufa_stores.merchant_short_name","orders.merchant_id")
                    ->orderBy("orders.updated_at","desc")
                    ->paginate(9);
                return view("merchant.pufals",compact("list","allmerchants","status"));
            }
        }else{
            $allmerchants=null;
            $list=null;
            return view("merchant.pufals",compact("list","status","allmerchants"));
        }
    }


}