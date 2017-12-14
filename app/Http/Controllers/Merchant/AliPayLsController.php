<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/2/28
 * Time: 16:41
 */
namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class AliPayLsController extends Controller
{
  public function alipayls(Request $request){
      $status=$request->get("status");
      $where=[1,2,3,4,5];
      if($status){
          if($status==1){
              $where=[1];
          }else{
              $where=[2,3,4,5];
          }
      }
      //所有收银员
      $cashier=DB::table("merchants")->select("name","id")->get();
      foreach($cashier as $v){
          $cashier[$v->id]=$v->name;
      }
      $array=[101,103,104];
      //判断登陆类型;
      $a=DB::table("merchant_shops")
          ->join('alipay_app_oauth_users', 'merchant_shops.store_id', '=', 'alipay_app_oauth_users.store_id')
          ->where('merchant_shops.merchant_id', auth()->guard('merchant')->user()->id)
          ->select("merchant_shops.store_id","alipay_app_oauth_users.pid","alipay_app_oauth_users.id")
          ->first();
      $b=DB::table("merchants")
          ->where("id",auth()->guard('merchant')->user()->id)
          ->first();
      if($a){
          if($a->pid==0&&$b->type==0){
              $data[]=$a->store_id;
              $c=DB::table("alipay_app_oauth_users")->where("pid",$a->id)->get();
              if($c){
                  foreach($c as $v){
                      $data[]=$v->store_id;
                  }
              }
              $list=DB::table("alipay_app_oauth_users")
                  ->join("orders","alipay_app_oauth_users.store_id","=","orders.store_id")
                  ->whereIn("orders.store_id",$data)
                  ->whereIn("orders.type",$array)
                  ->whereIn("orders.pay_status",$where)
                  ->select("orders.type","orders.total_amount","orders.merchant_id","orders.out_trade_no","orders.pay_status","orders.updated_at","orders.remark","alipay_app_oauth_users.auth_shop_name")
                  ->orderBy("orders.updated_at","desc")
                  ->paginate(9);
              return view("merchant.alipay",compact("list","cashier","status"));
          }
          if($a->pid!=0&&$b->type==0){
              $list=DB::table("alipay_app_oauth_users")
                  ->join("orders","alipay_app_oauth_users.store_id","=","orders.store_id")
                  ->where("orders.store_id",$a->store_id)
                  ->whereIn("orders.type",$array)
                  ->whereIn("orders.pay_status",$where)
                  ->select("orders.type","orders.total_amount","orders.merchant_id","orders.out_trade_no","orders.pay_status","orders.updated_at","orders.remark","alipay_app_oauth_users.auth_shop_name")
                  ->orderBy("orders.updated_at","desc")
                  ->paginate(9);
              return view("merchant.alipay",compact("list","cashier","status"));
          }
          if($b->type!=0){
              $list=DB::table("alipay_app_oauth_users")
                  ->join("orders","alipay_app_oauth_users.store_id","=","orders.store_id")
                  ->where("orders.merchant_id",auth()->guard('merchant')->user()->id)
                  ->whereIn("orders.pay_status",$where)
                  ->whereIn("orders.type",$array)
                  ->select("orders.type","orders.total_amount","orders.merchant_id","orders.out_trade_no","orders.pay_status","orders.updated_at","orders.remark","alipay_app_oauth_users.auth_shop_name")
                  ->orderBy("orders.updated_at","desc")
                  ->paginate(9);
              return view("merchant.alipay",compact("list","cashier","status"));
          }
      }else{
          $list=null;
          $cashier=null;
          return view("merchant.alipay",compact("list","cashier","status"));
      }


  }
    public function alipaysls(Request $request){
        //登录用户id;
        //支付宝口碑门店流水
        $status=$request->get("status");
        $where=[1,2,3,4,5];
        if($status){
            if($status==1){
                $where=[1];
            }else{
                $where=[2,3,4,5];
            }
        }
        //所有收银员
        $cashier=DB::table("merchants")->select("name","id")->get();
        foreach($cashier as $v){
            $cashier[$v->id]=$v->name;
        }
        $array1=[102,105,106];
        //判断登陆类型;
        $a=DB::table("merchant_shops")
            ->join('alipay_shop_lists', 'merchant_shops.store_id', '=', 'alipay_shop_lists.store_id')
            ->where('merchant_shops.merchant_id', auth()->guard('merchant')->user()->id)
            ->select("merchant_shops.store_id","alipay_shop_lists.pid","alipay_shop_lists.id")
            ->first();
        $b=DB::table("merchants")
            ->where("id",auth()->guard('merchant')->user()->id)
            ->first();
        if($a){
            if($a->pid==0&&$b->type==0){
                $data[]=$a->store_id;
                $c=DB::table("alipay_shop_lists")->where("pid",$a->id)->get();
                if($c){
                    foreach($c as $v){
                        $data[]=$v->store_id;
                    }
                }
                $info=DB::table("alipay_shop_lists")
                    ->join("orders","alipay_shop_lists.store_id","=","orders.store_id")
                    ->whereIn("orders.store_id",$data)
                    ->whereIn("orders.type",$array1)
                    ->whereIn("orders.pay_status",$where)
                    ->select("orders.type","orders.total_amount","orders.merchant_id","orders.out_trade_no","orders.pay_status","orders.updated_at","orders.remark","alipay_shop_lists.main_shop_name")
                    ->orderBy("orders.updated_at","desc")
                    ->paginate(9);
                return view("merchant.salipay",compact("info","cashier","status"));
            }
            if($a->pid!=0&&$b->type==0){
                $info=DB::table("alipay_shop_lists")
                    ->join("orders","alipay_shop_lists.store_id","=","orders.store_id")
                    ->where("orders.store_id",$a->store_id)
                    ->whereIn("orders.type",$array1)
                    ->whereIn("orders.pay_status",$where)
                    ->select("orders.type","orders.total_amount","orders.merchant_id","orders.out_trade_no","orders.pay_status","orders.updated_at","orders.remark","alipay_shop_lists.main_shop_name")
                    ->orderBy("orders.updated_at","desc")
                    ->paginate(9);
                return view("merchant.salipay",compact("info","cashier","status"));
            }
            if($b->type!=0){
                $info=DB::table("alipay_shop_lists")
                    ->join("orders","alipay_shop_lists.store_id","=","orders.store_id")
                    ->where("orders.merchant_id",auth()->guard('merchant')->user()->id)
                    ->whereIn("orders.pay_status",$where)
                    ->whereIn("orders.type",$array1)
                    ->select("orders.type","orders.total_amount","orders.merchant_id","orders.out_trade_no","orders.pay_status","orders.updated_at","orders.remark","alipay_shop_lists.main_shop_name")
                    ->orderBy("orders.updated_at","desc")
                    ->paginate(9);
                return view("merchant.salipay",compact("info","cashier","status"));
            }
        }else{
            $info=null;
            $cashier=null;
            return view("merchant.salipay",compact("info","cashier","status"));
        }
    }
}?>