<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/29
 * Time: 12:14
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Http\Controllers\Controller;
use App\Models\PushConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class addOldBranchController extends Controller
{
   public function index(Request $request){
       $auth = Auth::user()->can('addOldBranch');
       if (!$auth) {
           echo '你没有权限操作！';
           die;
       }
       $pid=$request->get('pid');
       $type=$request->get("type");
       if($type=="ali"){
             $table="alipay_app_oauth_users";
             $shop="auth_shop_name as name";
             $user_id="promoter_id";
       }
       if($type=="sali"){
            $table="alipay_shop_lists";
            $shop="main_shop_name as name";
           $user_id="user_id";
       }
       if($type=="weixin"){
             $table="weixin_shop_lists";
             $shop="store_name as name";
             $user_id="user_id";
       }
       if($type=="pingan"){
           $table="pingan_stores";
           $shop="alias_name as name";
           $user_id="user_id";
       }
       if($type=="pufa"){
           $table="pufa_stores";
           $shop="merchant_short_name as name";
           $user_id="user_id";
       }
       if($type=="unionpay"){
           $table="union_pay_stores";
           $shop="alias_name as name";
           $user_id="user_id";
       }
       if($type=="webank"){
           $table="we_bank_stores";
           $shop="alias_name as name";
           $user_id="user_id";
       }
       if($type=="newland"){
           $table="merc_regists";
           $shop="store_name as name";
           $user_id="user_id";
       }
       try{
           $list=DB::table($table)
               ->where("is_delete",0)
               ->where($user_id,Auth::user()->id)
               ->where("pid",0)
               ->select($shop,"id")
               ->get();
           if(Auth::user()->hasRole("admin")){
               $list=DB::table($table)
                   ->where("is_delete",0)
                   ->where("pid",0)
                   ->select($shop,"id")
                   ->get();
           }
       }catch(\Exception $e){

       }
       return view("admin.alipayopen.alipaybranch.addOldBranch",compact("list","pid","type"));
   }
   public function add(Request $request){
       $data['pid']=$request->get('pid');
       $type=$request->get("type");
       $id=$request->get('id');
       if(!$id){
           return json_encode([
               "success"=>0,
               "sub_msg"=>"请选择店铺!"
           ]);
       }
       if($type=="ali"){
           $table="alipay_app_oauth_users";
       }
       if($type=="sali"){
           $table="alipay_shop_lists";
       }
       if($type=="weixin"){
           $table="weixin_shop_lists";
       }
       if($type=="pingan"){
           $table="pingan_stores";
       }
       if($type=="pufa"){
           $table="pufa_stores";
       }
       if($type=="unionpay"){
           $table="union_pay_stores";
       }
       if($type=="webank"){
           $table="we_bank_stores";
       }
       if($type=="newland"){
           $table="merc_regists";
       }
       try{

           $info=DB::table($table)->where("pid",$id)->count();
           if($info<1){
               $list=DB::table($table)
                   ->where('id',$id)
                   ->update($data);
               if($list){
                   return json_encode([
                       "success"=>1,
                   ]);
               }else{
                   return json_encode([
                       "success"=>0,
                       "sub_msg"=>"添加失败"
                   ]);
               }
           }else{
               return json_encode([
                   "success"=>0,
                   "sub_msg"=>"该店铺下已有分店,无法添加"
               ]);
           }

       }catch(\Exception $e){
           return json_encode([
               "success"=>0,
               "sub_msg"=>$e->getMessage()
           ]);
       }
   }
}