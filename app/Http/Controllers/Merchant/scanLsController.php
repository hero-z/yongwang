<?php
namespace App\Http\Controllers\Merchant;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Extensions\AuthenticatesLogout;
use Illuminate\Http\Request;
use App\Models\MerchantShops;
class scanLsController extends Controller{
   public function scanLs(Request $request){
       $status=$request->get("status");
       $where=[];
       if($status){
           if($status==1){
               $where[]=['orders.pay_status',1];
           }else{
               $where[]=['orders.pay_status','!=',1];
           }
       }


       $list = DB::table("orders")
           ->where("merchant_id",auth()->guard('merchant')->user()->id)
           ->whereIn('type',[103,105,202,305,306,307,402,603,604])
           ->where($where)
           ->orderBy('updated_at', 'desc')
           ->paginate(8);

       return view("merchant.scanLs",['list'=>$list,"status"=>$status]);
   }

}
?>