<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/2/26
 * Time: 17:38
 */

namespace App\Http\Controllers\Merchant;

use Alipayopen\Sdk\Request\AlipayOfflineMarketShopCreateRequest;
use App\Models\PingancqrLsits;
use App\Models\PingancqrLsitsinfo;
use App\Models\PinganStore;
use App\Models\PinganStoreInfos;
use App\Models\RoleUser;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class IndexController extends BaseController
{
    /**
     * 显示后台管理模板首页
     */
    public function index(Request $request)
    {
        //二维码
         $oalicode=$this->oalicode();
         $salicode=$this->salicode();
         $wxcode=$this->wxcode();
         $palicode=$this->pingancode(301,"alipay");
         $pweixin=$this->pingancode(302,"weixin");
         $pjd=$this->pingancode(303,"jd");
         $pbest=$this->pingancode(304,"bestpay");
         $unionpaycode=$this->unionpaycode("401");
         $pufacode=$this->pufacode("601");
        $wpufacode=$this->pufacode("602");
        //扫码枪
         $moali=$this->moali();
         $msali=$this->msali();
         $mweixin=$this->mweixin();
         $mpali=$this->mpingan(305,"mpalipay");
         $mpweixin=$this->mpingan(306,"mpweixin");
         $mpjd=$this->mpingan(307,"mpjd");
        $munionpay=$this->unionpaycode("402");
        $mapufa=$this->pufacode("603");
        $mwpufa=$this->pufacode("604");
        //现金
        $cash=$this->cash();
            $total =$oalicode+$salicode+$wxcode+$palicode+$pweixin+$pjd+$pbest+$unionpaycode+$pufacode+ $moali+$msali+$mweixin+$mpali+$mpweixin+$mpjd+$munionpay+$cash+$wpufacode+$mapufa+$mwpufa;
            $code =$oalicode+$salicode+$wxcode+$palicode+$pweixin+$pjd+$pbest+$unionpaycode+$pufacode+$wpufacode;
            $scan =$moali+$msali+$mweixin+$mpali+$mpweixin+$mpjd+$munionpay+$mapufa+$mwpufa;
            if($total!=0){
                $a =round(($oalicode+$salicode+$palicode+$moali+$msali+$mpali+$pufacode+$mapufa)/$total,5)*100;
                $b =round(($wxcode+$pweixin+$mweixin+$mpweixin+$wpufacode+$mwpufa)/$total,5)*100;
                $c =(100000000000-$a*1000000000-$b*1000000000)/1000000000;
            }else{
                $a=0;
                $b=0;
                $c=0;
            }

            return view('merchant.index', compact("total", "code", "scan", "a", "b", "c"));

    }
    //支付宝当面付二维码流水统计
    protected function oalicode(){
        $array = [101,104];
        //判断登陆类型;
        $a = DB::table("merchant_shops")
            ->join('alipay_app_oauth_users', 'merchant_shops.store_id', '=', 'alipay_app_oauth_users.store_id')
            ->where('merchant_shops.merchant_id', auth()->guard('merchant')->user()->id)
            ->select("merchant_shops.store_id", "alipay_app_oauth_users.pid", "alipay_app_oauth_users.id")
            ->first();
        $b = DB::table("merchants")
            ->where("id", auth()->guard('merchant')->user()->id)
            ->first();
        if ($a) {
            if ($a->pid == 0 && $b->type == 0) {
                $data[] = $a->store_id;
                $c = DB::table("alipay_app_oauth_users")->where("pid", $a->id)->get();
                if ($c) {
                    foreach ($c as $v) {
                        $data[] = $v->store_id;
                    }
                }
                $old=DB::table("alipay_trade_queries")
                    ->where("status","TRADE_SUCCESS")
                    ->whereIn("store_id", $data)
                    ->sum("total_amount");
                $new = DB::table("orders")
                    ->whereIn("store_id", $data)
                    ->whereIn("type",$array)
                    ->where("pay_status", "1")
                    ->sum("total_amount");
            }
            if ($a->pid != 0 && $b->type == 0) {
                $old=DB::table("alipay_trade_queries")
                    ->where("status","TRADE_SUCCESS")
                    ->where("store_id", $a->store_id)
                    ->sum("total_amount");
                $new = DB::table("orders")
                    ->where("store_id", $a->store_id)
                    ->whereIn("type",$array)
                    ->where("pay_status", "1")
                    ->sum("total_amount");
            }
            if ($b->type != 0) {
                $old=DB::table("alipay_trade_queries")
                    ->where("status","TRADE_SUCCESS")
                    ->where("type","oalipay")
                    ->where("merchant_id", auth()->guard('merchant')->user()->id)
                    ->sum("total_amount");
                $new = DB::table("orders")
                    ->where("merchant_id", auth()->guard('merchant')->user()->id)
                    ->whereIn("type",$array)
                    ->where("pay_status", "1")
                    ->sum("total_amount");
            }
        }else{
            $old=null;
            $new=null;
        }
        $oalicode=$old+$new;
         return $oalicode;

        }
    //支付宝口碑二维码
    protected function salicode(){
        //登录用户id;
        //支付宝口碑门店流水
        $array1=[102,106];
        //判断登陆类型;
        $a = DB::table("merchant_shops")
            ->join('alipay_shop_lists', 'merchant_shops.store_id', '=', 'alipay_shop_lists.store_id')
            ->where('merchant_shops.merchant_id', auth()->guard('merchant')->user()->id)
            ->select("merchant_shops.store_id", "alipay_shop_lists.pid", "alipay_shop_lists.id")
            ->first();
        $b = DB::table("merchants")
            ->where("id", auth()->guard('merchant')->user()->id)
            ->first();
        if ($a) {
            if ($a->pid == 0 && $b->type == 0) {
                $data[] = $a->store_id;
                $c = DB::table("alipay_shop_lists")->where("pid", $a->id)->get();
                if ($c) {
                    foreach ($c as $v) {
                        $data[] = $v->store_id;
                    }
                }
                $old=DB::table("alipay_trade_queries")
                    ->where("status","TRADE_SUCCESS")
                    ->whereIn("store_id", $data)
                    ->sum("total_amount");
                $new = DB::table("orders")
                    ->whereIn("store_id", $data)
                    ->whereIn("type",$array1)
                    ->where("pay_status", "1")
                    ->sum("total_amount");
            }
            if ($a->pid != 0 && $b->type == 0) {
                $old=DB::table("alipay_trade_queries")
                    ->where("status","TRADE_SUCCESS")
                    ->where("store_id", $a->store_id)
                    ->sum("total_amount");
                $new = DB::table("orders")
                    ->where("store_id", $a->store_id)
                    ->whereIn("type",$array1)
                    ->where("pay_status", "1")
                    ->sum("total_amount");
            }
            if ($b->type != 0) {
                $old=DB::table("alipay_trade_queries")
                    ->where("status","TRADE_SUCCESS")
                    ->where("type","salipay")
                    ->where("merchant_id", auth()->guard('merchant')->user()->id)
                    ->sum("total_amount");
                $new = DB::table("orders")
                    ->where("merchant_id", auth()->guard('merchant')->user()->id)
                    ->whereIn("type",$array1)
                    ->where("pay_status", "1")
                    ->sum("total_amount");
            }
        }else{
            $old=null;
            $new=null;
        }
        $salicode=$old+$new;
        return $salicode;
    }
   //微信二维码
    protected function wxcode(){

        //判断登陆类型;
        $a=DB::table("merchant_shops")
            ->join('weixin_shop_lists', 'merchant_shops.store_id', '=', 'weixin_shop_lists.store_id')
            ->where('merchant_shops.merchant_id', auth()->guard('merchant')->user()->id)
            ->select("merchant_shops.store_id","weixin_shop_lists.pid","weixin_shop_lists.id")
            ->first();
        $b=DB::table("merchants")
            ->where("id",auth()->guard('merchant')->user()->id)
            ->first();
        $array=[201,203];
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
                $old=DB::table("wx_pay_orders")
                    ->whereIn("store_id",$data)
                    ->where("status","SUCCESS")
                    ->sum("total_fee");
                $new=DB::table("orders")
                    ->whereIn("store_id",$data)
                    ->whereIn("type",$array)
                    ->where("pay_status","1")
                    ->sum("total_amount");
            }
            if($a->pid!=0&&$b->type==0){
                $old=DB::table("wx_pay_orders")
                    ->where("store_id",$a->store_id)
                    ->where("status","SUCCESS")
                    ->sum("total_fee");
                $new=DB::table("orders")
                    ->where("store_id",$a->store_id)
                    ->whereIn("type",$array)
                    ->where("pay_status","1")
                    ->sum("total_amount");
            }
            if($b->type!=0){
                $old=DB::table("wx_pay_orders")
                    ->where("merchant_id",auth()->guard('merchant')->user()->id)
                    ->where("status","SUCCESS")
                    ->sum("total_fee");
                $new=DB::table("orders")
                    ->where("merchant_id",auth()->guard('merchant')->user()->id)
                    ->whereIn("type",$array)
                    ->where("pay_status","1")
                    ->sum("total_amount");
            }
        }else{
            $old=null;
            $new=null;
        }
        $wxcode=$old+$new;
        return $wxcode;
    }
//平安二维码
    protected function pingancode($type,$type1){
        $array=[$type];
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
                $old=DB::table("pingan_trade_queries")
                    ->whereIn("store_id",$data)
                    ->where("type",$type1)
                    ->where("status","like","%SUCCESS")
                    ->sum("total_amount");
                $new=DB::table("orders")
                    ->whereIn("store_id",$data)
                    ->whereIn("type",$array)
                    ->where("pay_status","1")
                    ->sum("total_amount");
            }
            if($a->pid!=0&&$b->type==0){
                $old=DB::table("pingan_trade_queries")
                    ->where("store_id",$a->store_id)
                    ->where("type",$type1)
                    ->where("status","like","%SUCCESS")
                    ->sum("total_amount");
                $new=DB::table("orders")
                    ->where("store_id",$a->store_id)
                    ->whereIn("type",$array)
                    ->where("pay_status","1")
                    ->sum("total_amount");
            }
            if($b->type!=0){
                $old=DB::table("pingan_trade_queries")
                    ->where("merchant_id",auth()->guard('merchant')->user()->id)
                    ->where("status","like","%SUCCESS")
                    ->where("type",$type1)
                    ->sum("total_amount");
                $new=DB::table("orders")
                    ->where("merchant_id",auth()->guard('merchant')->user()->id)
                    ->whereIn("type",$array)
                    ->where("pay_status","1")
                    ->sum("total_amount");
            }
        }else{
            $old=null;
            $new=null;
        }
        $pingancode=$old+$new;
        return $pingancode;
    }
//银联二维码
   protected function unionpaycode($type){
       $array=[$type];
       //判断登陆类型;
       $a=DB::table("merchant_shops")
           ->join('union_pay_stores', 'merchant_shops.store_id', '=', 'union_pay_stores.store_id')
           ->where('merchant_shops.merchant_id', auth()->guard('merchant')->user()->id)
           ->select("merchant_shops.store_id","union_pay_stores.pid","union_pay_stores.id")
           ->first();
       $b=DB::table("merchants")
           ->where("id",auth()->guard('merchant')->user()->id)
           ->first();
       if($a){
           if($a->pid==0&&$b->type==0){
               //分店store_id
               $data[]=$a->store_id;
               $c=DB::table("union_pay_stores")->where("pid",$a->id)->get();
               if($c){
                   foreach($c as $v){
                       $data[]=$v->store_id;
                   }
               }
               $new=DB::table("orders")
                   ->whereIn("store_id",$data)
                   ->whereIn("type",$array)
                   ->where("pay_status","1")
                   ->sum("total_amount");
           }
           if($a->pid!=0&&$b->type==0){
               $new=DB::table("orders")
                   ->where("store_id",$a->store_id)
                   ->whereIn("type",$array)
                   ->where("pay_status","1")
                   ->sum("total_amount");
           }
           if($b->type!=0){
               $new=DB::table("orders")
                   ->where("merchant_id",auth()->guard('merchant')->user()->id)
                   ->whereIn("type",$array)
                   ->where("pay_status","1")
                   ->sum("total_amount");
           }
       }else{
           $new=null;
       }
       $unionpaycode=$new;
       return $unionpaycode;
   }
  //浦发二维码
    protected function pufacode($type){
        $array=[$type];
        //判断登陆类型;
        $a=DB::table("merchant_shops")
            ->join('pufa_stores', 'merchant_shops.store_id', '=', 'pufa_stores.store_id')
            ->where('merchant_shops.merchant_id', auth()->guard('merchant')->user()->id)
            ->select("merchant_shops.store_id","pufa_stores.pid","pufa_stores.id")
            ->first();
        $b=DB::table("merchants")
            ->where("id",auth()->guard('merchant')->user()->id)
            ->first();
        if($a){
            if($a->pid==0&&$b->type==0){
                //分店store_id
                $data[]=$a->store_id;
                $c=DB::table("pufa_stores")->where("pid",$a->id)->get();
                if($c){
                    foreach($c as $v){
                        $data[]=$v->store_id;
                    }
                }
                $new=DB::table("orders")
                    ->whereIn("store_id",$data)
                    ->whereIn("type",$array)
                    ->where("pay_status","1")
                    ->sum("total_amount");
            }
            if($a->pid!=0&&$b->type==0){
                $new=DB::table("orders")
                    ->where("store_id",$a->store_id)
                    ->whereIn("type",$array)
                    ->where("pay_status","1")
                    ->sum("total_amount");
            }
            if($b->type!=0){
                $new=DB::table("orders")
                    ->where("merchant_id",auth()->guard('merchant')->user()->id)
                    ->whereIn("type",$array)
                    ->where("pay_status","1")
                    ->sum("total_amount");
            }
        }else{
            $new=null;
        }
        $pufacode=$new;
        return $pufacode;
    }

    //扫码枪流水
    //支付宝当面付扫码枪
    protected function moali(){
        $array = [103];
        //判断登陆类型;
        $a = DB::table("merchant_shops")
            ->join('alipay_app_oauth_users', 'merchant_shops.store_id', '=', 'alipay_app_oauth_users.store_id')
            ->where('merchant_shops.merchant_id', auth()->guard('merchant')->user()->id)
            ->select("merchant_shops.store_id", "alipay_app_oauth_users.pid", "alipay_app_oauth_users.id")
            ->first();
        $b = DB::table("merchants")
            ->where("id", auth()->guard('merchant')->user()->id)
            ->first();
        if ($a) {
            if ($a->pid == 0 && $b->type == 0) {
                $data[] = $a->store_id;
                $c = DB::table("alipay_app_oauth_users")->where("pid", $a->id)->get();
                if ($c) {
                    foreach ($c as $v) {
                        $data[] = $v->store_id;
                    }
                }
                $old=DB::table("merchant_orders")
                    ->where("status","TRADE_SUCCESS")
                    ->whereIn("store_id", $data)
                    ->sum("total_amount");
                $new = DB::table("orders")
                    ->whereIn("store_id", $data)
                    ->whereIn("type",$array)
                    ->where("pay_status", "1")
                    ->sum("total_amount");
            }
            if ($a->pid != 0 && $b->type == 0) {
                $old=DB::table("merchant_orders")
                    ->where("status","TRADE_SUCCESS")
                    ->where("store_id", $a->store_id)
                    ->sum("total_amount");
                $new = DB::table("orders")
                    ->where("store_id", $a->store_id)
                    ->whereIn("type",$array)
                    ->where("pay_status", "1")
                    ->sum("total_amount");
            }
            if ($b->type != 0) {
                $old=DB::table("merchant_orders")
                    ->where("status","TRADE_SUCCESS")
                    ->where("type","moalipay")
                    ->where("merchant_id", auth()->guard('merchant')->user()->id)
                    ->sum("total_amount");
                $new = DB::table("orders")
                    ->where("merchant_id", auth()->guard('merchant')->user()->id)
                    ->whereIn("type",$array)
                    ->where("pay_status", "1")
                    ->sum("total_amount");
            }
        }else{
            $old=null;
            $new=null;
        }
        $oalicode=$old+$new;
        return $oalicode;
    }
   //支付宝口碑扫码枪
    protected function msali(){
        //登录用户id;
        //支付宝口碑门店流水
        $array1=[105];
        $old=DB::table("merchant_orders")
            ->where("status","TRADE_SUCCESS")
            ->where("type","msalipay")
            ->where("merchant_id", auth()->guard('merchant')->user()->id)
            ->sum("total_amount");
        $new = DB::table("orders")
            ->where("merchant_id", auth()->guard('merchant')->user()->id)
            ->whereIn("type",$array1)
            ->where("pay_status", "1")
            ->sum("total_amount");
        $salicode=$old+$new;
        return $salicode;
    }
    //微信扫码枪
    protected function mweixin(){

        //判断登陆类型;
        $a=DB::table("merchant_shops")
            ->join('weixin_shop_lists', 'merchant_shops.store_id', '=', 'weixin_shop_lists.store_id')
            ->where('merchant_shops.merchant_id', auth()->guard('merchant')->user()->id)
            ->select("merchant_shops.store_id","weixin_shop_lists.pid","weixin_shop_lists.id")
            ->first();
        $b=DB::table("merchants")
            ->where("id",auth()->guard('merchant')->user()->id)
            ->first();
        $array=[202];
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
                $old=DB::table("merchant_orders")
                    ->whereIn("store_id",$data)
                    ->where("status","SUCCESS")
                    ->sum("total_amount");
                $new=DB::table("orders")
                    ->whereIn("store_id",$data)
                    ->whereIn("type",$array)
                    ->where("pay_status","1")
                    ->sum("total_amount");
            }
            if($a->pid!=0&&$b->type==0){
                $old=DB::table("merchant_orders")
                    ->where("store_id",$a->store_id)
                    ->where("status","SUCCESS")
                    ->sum("total_amount");
                $new=DB::table("orders")
                    ->where("store_id",$a->store_id)
                    ->whereIn("type",$array)
                    ->where("pay_status","1")
                    ->sum("total_amount");
            }
            if($b->type!=0){
                $old=DB::table("merchant_orders")
                    ->where("merchant_id",auth()->guard('merchant')->user()->id)
                    ->where("type","mweixin")
                    ->where("status","SUCCESS")
                    ->sum("total_amount");
                $new=DB::table("orders")
                    ->where("merchant_id",auth()->guard('merchant')->user()->id)
                    ->whereIn("type",$array)
                    ->where("pay_status","1")
                    ->sum("total_amount");
            }
        }else{
            $old=null;
            $new=null;
        }
        $wxcode=$old+$new;
        return $wxcode;
    }
   //平安扫码枪
    protected function mpingan($type,$type1){
        $array=[$type];
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
                $old=DB::table("merchant_orders")
                    ->whereIn("store_id",$data)
                    ->where("type",$type1)
                    ->where("status","like","%SUCCESS")
                    ->sum("total_amount");
                $new=DB::table("orders")
                    ->whereIn("store_id",$data)
                    ->whereIn("type",$array)
                    ->where("pay_status","1")
                    ->sum("total_amount");
            }
            if($a->pid!=0&&$b->type==0){
                $old=DB::table("merchant_orders")
                    ->where("store_id",$a->store_id)
                    ->where("type",$type1)
                    ->where("status","like","%SUCCESS")
                    ->sum("total_amount");
                $new=DB::table("orders")
                    ->where("store_id",$a->store_id)
                    ->whereIn("type",$array)
                    ->where("pay_status","1")
                    ->sum("total_amount");
            }
            if($b->type!=0){
                $old=DB::table("merchant_orders")
                    ->where("merchant_id",auth()->guard('merchant')->user()->id)
                    ->where("type",$type1)
                    ->where("status","like","%SUCCESS")
                    ->sum("total_amount");
                $new=DB::table("orders")
                    ->where("merchant_id",auth()->guard('merchant')->user()->id)
                    ->whereIn("type",$array)
                    ->where("pay_status","1")
                    ->sum("total_amount");
            }
        }else{
            $old=null;
            $new=null;
        }
        $pingancode=$old+$new;
        return $pingancode;
    }
    protected function cash(){
        $cash=DB::table("orders")
            ->where("merchant_id",auth()->guard('merchant')->user()->id)
            ->where("type","701")
            ->sum("total_amount");
        return $cash;
    }
    public function editMerchant(){
        $phone=auth()->guard("merchant")->user()->phone;
        return view("merchant.editMerchant",compact("phone"));
    }
    public function updateMerchant(Request $request){
        $id=auth()->guard("merchant")->user()->id;
        $data['password'] = $request->get("password");
        $data['password_confirmation']=$request->get("password_confirmation");
       if($data['password']||$data['password_confirmation']){
           $dataIn = [
               'password' => bcrypt($data['password']),
           ];


           $rules = [
               'password' => 'required|min:6|confirmed',
           ];
           $messages = [
               'required' => '密码不能为空',
               'between' => '密码必须是6~20位之间',
               'confirmed' => '新密码和确认密码不匹配',
           ];
           $cn = [
               'password' => '密码',
               'password_confirmation' => '确认密码'
           ];
           $validator = Validator::make($data, $rules, $messages, $cn);
           if ($validator->fails()) {
               return back()->with("warnning","两次密码输入不一致");  //返回一次性错误
           }
       }
        $dataIn['phone']=$request->get("phone");
        $list=DB::table("merchants")->where("phone",$dataIn['phone'])->first();
        if(preg_match("/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|17[0-9]|18[0|1|2|3|5|6|7|8|9])\\d{8}$/",$dataIn['phone'])){
            if($list){
                if($list->id==$id){
                    if(DB::table("merchants")->where("id",$id)->update($dataIn)){
                        return back()->with("warnning","修改成功");
                    }
                }else{
                    return back()->with("warnning","该手机号已被占用");
                }
            }else{
                if(DB::table("merchants")->where("id",$id)->update($dataIn)){
                    return back()->with("warnning","修改成功");
                }
            }
        }else{
            return back()->with("warnning","请按格式输入正确的手机号");
        }


    }
}