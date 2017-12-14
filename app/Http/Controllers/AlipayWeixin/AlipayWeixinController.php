<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/1/15
 * Time: 16:04
 */

namespace App\Http\Controllers\AlipayWeixin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayWeixin;
use App\Models\WeixinShopList;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class AlipayWeixinController extends Controller
{
    public function AlipayWexinLists(Request $request)
    {
        //
        $data =DB:: table("multi_codes")->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        if (Auth::user()->hasRole('admin')) {
            $data = DB::table("multi_codes")->orderBy('created_at', 'desc')->get();
        }
        if ($data->isEmpty()) {
            $paginator = "";
            $datapage = "";
        } else {
            $data = $data->toArray();
            //非数据库模型自定义分页
            $perPage = 9;//每页数量
            if ($request->has('page')) {
                $current_page = $request->input('page');
                $current_page = $current_page <= 0 ? 1 : $current_page;
            } else {
                $current_page = 1;
            }
            $item = array_slice($data, ($current_page - 1) * $perPage, $perPage); //注释1
            $total = count($data);
            $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
            $datapage = $paginator->toArray()['data'];
        }
        //dd($datapage);
        return view('admin.alipayweixin.list', compact('datapage', 'paginator'));
    }

    public function addAliPayWeixinStore(Request $request)
    {
        $auth = Auth::user()->can('addAlipayWeixin');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
      //  $store = AlipayAppOauthUsers::all();
        //支付宝加平安商户
        //支付宝当面付
        //支付宝加平安商户
        //支付宝当面付
        $oali=DB::table("alipay_app_oauth_users")
            ->where("promoter_id",Auth::user()->id)
            ->select("store_id","auth_shop_name","promoter_id")
            ->get();
        $sali=DB::table("alipay_shop_lists")
            ->where("user_id",Auth::user()->id)
            ->select("store_id","main_shop_name","user_id")
            ->get();
        $pingan=DB::table("pingan_stores")
            ->where("user_id",Auth::user()->id)
            ->select("external_id","alias_name","user_id")
            ->get();
        $pufa=DB::table('pufa_stores')
            ->where("user_id",Auth::user()->id)
            ->select("store_id","merchant_short_name","user_id")
            ->get();
        //微信加平安商户
        $weixin=DB::table("weixin_shop_lists")
            ->where("user_id",Auth::user()->id)
            ->select("store_id","store_name","user_id")
            ->get();
        if(Auth::user()->hasRole("admin")){
            $oali=DB::table("alipay_app_oauth_users")->select("store_id","auth_shop_name","promoter_id")->get();
            $sali=DB::table("alipay_shop_lists")->select("store_id","main_shop_name","user_id")->get();
            $pingan=DB::table("pingan_stores")->select("external_id","alias_name","user_id")->get();
            //微信加平安商户
            $pufa=DB::table('pufa_stores')
                ->select("store_id","merchant_short_name","user_id")
                ->get();
            $weixin=DB::table("weixin_shop_lists")->select("store_id","store_name","user_id")->get();
        }

        return view('admin.alipayweixin.add', compact('oali',"sali","pingan","weixin","pufa"));
    }
    //二码合一插入数据库
     public function addTwo(Request $request){
         $ali=$request->get("ali");
         $weixin=$request->get("weixin");
         $jd=$request->get("jd");
         $bestpay=$request->get('bestpay');


         if($ali){
             $a=explode("*",$ali);
             $store_id_k=$a[0];
             $store_id_v=$a[1];
             $data[$store_id_k]=$store_id_v;
             $data['store_name']=$a[2];
             $pay_ways_k=$a[3];
             $pay_ways_v=$a[4];
             $data['user_id']=$a[5];
             $data[$pay_ways_k]=$pay_ways_v;

         }else{
             $a[1]=1;
         }
         if($weixin){
             $b=explode("*",$weixin);
             $store_id_k=$b[0];
             $store_id_v=$b[1];
             $data[$store_id_k]=$store_id_v;
             $data['store_name']=$b[2];
             $pay_ways_k=$b[3];
             $pay_ways_v=$b[4];
             $data['user_id']=$b[5];
             $data[$pay_ways_k]=$pay_ways_v;
         }else{
             $b[1]=2;
         }
         if($jd){
             $c=explode("*",$jd);
             $store_id_k=$c[0];
             $store_id_v=$c[1];
             $data[$store_id_k]=$store_id_v;
             $data['store_name']=$c[2];
             $pay_ways_k=$c[3];
             $pay_ways_v=$c[4];
             $data['user_id']=$c[5];
             $data[$pay_ways_k]=$pay_ways_v;
         }else{
             $c[1]=3;
         }
         if($bestpay){
             $d=explode("*",$bestpay);
             $store_id_k=$d[0];
             $store_id_v=$d[1];
             $data[$store_id_k]=$store_id_v;
             $data['store_name']=$d[2];
             $pay_ways_k=$d[3];
             $pay_ways_v=$d[4];
             $data['user_id']=$d[5];
             $data[$pay_ways_k]=$pay_ways_v;
         }else{
             $d[1]=4;
         }
         $data['created_at']=date("Y-m-d H:i:s");
        if(count($data)<7){
           return redirect("/admin/alipayweixin/addAliPayWeixinStore")->with("warnning", "您好!请选择至少两种类型进行合成");
       }else{
           if(DB::table("multi_codes")->where("store_name",$data['store_name'])->first()){
               return redirect("/admin/alipayweixin/addAliPayWeixinStore")->with("warnning", "您好!请不要重复合成");
           }else{
               try{
                   if(DB::table('multi_codes')->insert($data)){
                       return redirect("/admin/alipayweixin/addAliPayWeixinStore")->with("warnning", "您好!合成成功");
                   }
               }catch(Exception $e){
                   return redirect("/admin/alipayweixin/addAliPayWeixinStore")->with("warnning", "您好!合成失败");
               }
           }

       }

     }
    //加载二码合一修改页
    public function editAddTwo(Request $request){
        $auth = Auth::user()->can('alipayweixinEdit');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $id=$request->id;
        //dd($id);
        $list=DB::table("multi_codes")->where("id",$id)->first();
        //支付宝加平安商户
        //支付宝当面付
        $oali=DB::table("alipay_app_oauth_users")->where("auth_shop_name",$list->store_name)->select("store_id","auth_shop_name")->get();
        $sali=DB::table("alipay_shop_lists")->where("main_shop_name",$list->store_name)->select("store_id","main_shop_name")->get();
        $pingan=DB::table("pingan_stores")->where("alias_name",$list->store_name)->select("external_id","alias_name")->get();
        //微信加平安商户
        $weixin=DB::table("weixin_shop_lists")->where("store_name",$list->store_name)->select("store_id","store_name")->get();
        $pufa=DB::table("pufa_stores")->where("merchant_short_name",$list->store_name)->select("store_id","merchant_short_name")->get();
        return view("admin.alipayweixin.addTwo",['list'=>$list,"oali"=>$oali,"sali"=>$sali,"pingan"=>$pingan,"weixin"=>$weixin,"id"=>$id,"pufa"=>$pufa]);
    }
    //执行修改
    public function updateAddTwo(Request $request){

        $id=$request->get('id');
        $ali=$request->get("ali");
        $weixin=$request->get("weixin");
        $jd=$request->get("jd");
        $bestpay=$request->get('bestpay');
        if($ali){
            $a=explode("*",$ali);
            $store_id_k=$a[0];
            $store_id_v=$a[1];
            $data[$store_id_k]=$store_id_v;
            $pay_ways_k=$a[2];
            $pay_ways_v=$a[3];
            $data[$pay_ways_k]=$pay_ways_v;

        }
        if($weixin){
            $b=explode("*",$weixin);
            $store_id_k=$b[0];
            $store_id_v=$b[1];
            $data[$store_id_k]=$store_id_v;
            $pay_ways_k=$b[2];
            $pay_ways_v=$b[3];
            $data[$pay_ways_k]=$pay_ways_v;
        }
        if($jd){
            $c=explode("*",$jd);
            $store_id_k=$c[0];
            $store_id_v=$c[1];
            $data[$store_id_k]=$store_id_v;
            $pay_ways_k=$c[2];
            $pay_ways_v=$c[3];
            $data[$pay_ways_k]=$pay_ways_v;
        }
        if($bestpay){
            $d=explode("*",$bestpay);
            $store_id_k=$d[0];
            $store_id_v=$d[1];
            $data[$store_id_k]=$store_id_v;
            $pay_ways_k=$d[2];
            $pay_ways_v=$d[3];
            $data[$pay_ways_k]=$pay_ways_v;
        }
        $data['updated_at']=date("Y-m-d H:i:s");

                try{
                    if(DB::table('multi_codes')->where("id",$id)->update($data)){
                        return redirect("/admin/alipayweixin/AlipayWexinLists");
                    }
                }catch(Exception $e){
                    return redirect("/admin/alipayweixin/editAddTwo?id=".$id)->with("warnning", "您好!修改失败");
                }
    }
    public function xuanzhong(Request $request)
    {
        $data=[];
           $value=$request->get("value");
           if($value){
               $name=explode("*",$value)[2];
              if($name){
                  //支付宝当面付
                  $oali=DB::table('alipay_app_oauth_users')->where("auth_shop_name",$name)->first();
                  if($oali){
                      $data[0]['value']="store_id_a*".$oali->store_id."*".$oali->auth_shop_name."*alipay_ways*oalipay*".$oali->promoter_id;
                      $data[0]['name']=$oali->auth_shop_name."(支付宝当面付)";
                  }else{
                      $data[0]['value']="";
                      $data[0]['name']="";
                  }
                  //支付宝口碑
                  $sali=DB::table("alipay_shop_lists")->where("main_shop_name",$name)->first();
                  if($sali){
                      $data[1]['value']="store_id_a*".$sali->store_id."*".$sali->main_shop_name."*alipay_ways*salipay*".$sali->user_id;
                      $data[1]['name']=$sali->main_shop_name."(支付宝口碑)";
                  }else{
                      $data[1]['value']="";
                      $data[1]['name']="";
                  }
                  //微信
                  $weixin=DB::table("weixin_shop_lists")->where("store_name",$name)->first();
                  if($weixin){
                      $data[2]['value']="store_id_w*".$weixin->store_id."*".$weixin->store_name."*weixin_ways*weixin*".$weixin->user_id;
                      $data[2]['name']=$weixin->store_name."(官方微信)";
                  }else{
                      $data[2]['value']="";
                      $data[2]['name']="";
                  }
                 //平安
                  $pingan=DB::table('pingan_stores')->where('alias_name',$name)->first();
                  if($pingan){
                      //平安支付宝
                      $data[3]['value']="store_id_a*".$pingan->external_id."*".$pingan->alias_name."*alipay_ways*palipay*".$pingan->user_id;
                      $data[3]['name']=$pingan->alias_name."(平安支付宝)";
                     //平安微信
                      $data[4]['value']="store_id_w*".$pingan->external_id."*".$pingan->alias_name."*weixin_ways*pweixin*".$pingan->user_id;
                      $data[4]['name']=$pingan->alias_name."(平安微信)";
                      //平安京东
                      $data[5]['value']="store_id_j*".$pingan->external_id."*".$pingan->alias_name."*jd_ways*pjd*".$pingan->user_id;
                      $data[5]['name']=$pingan->alias_name."(平安京东)";
                      //平安翼支付
                      $data[6]['value']="store_id_b*".$pingan->external_id."*".$pingan->alias_name."*bestpay_ways*pbestpay*".$pingan->user_id;
                      $data[6]['name']=$pingan->alias_name."(平安翼支付)";
                  }else{
                      $data[3]['value']="";
                      $data[3]['name']="";
                      $data[4]['value']="";
                      $data[4]['name']="";
                      $data[5]['value']="";
                      $data[5]['name']="";
                      $data[6]['value']="";
                      $data[6]['name']="";
                  }
                  //浦发
                  $pufa=DB::table("pufa_stores")->where("merchant_short_name",$name)->first();
                  if($pufa){
                      //浦发支付宝
                      $data[7]['value']="store_id_a*".$pufa->store_id."*".$pufa->merchant_short_name."*alipay_ways*pfalipay*".$pufa->user_id;
                      $data[7]['name']=$pufa->merchant_short_name."(浦发支付宝)";
                      //浦发微信
                      $data[8]['value']="store_id_w*".$pufa->store_id."*".$pufa->merchant_short_name."*weixin_ways*pfweixin*".$pufa->user_id;
                      $data[8]['name']=$pufa->merchant_short_name."(浦发微信)";
                  }else{
                      $data[7]['value']="";
                      $data[7]['name']="";
                      $data[8]['value']="";
                      $data[8]['name']="";
                  }
              }
           }
            return json_encode($data);

    }

   public  function deleteAddtwo(Request $request){

   }
    
    public function addAliPayWeixinStorePost(Request $request)
    {
        $id = $request->get('id');
        $alistore = AlipayAppOauthUsers::where('id', $id)->first();
        $wxstore = WeixinShopList::where('store_name', $alistore->auth_shop_name)->first();
        if ($wxstore) {
            $umxnt = AlipayWeixin::where('alipay_user_id', $alistore->user_id)->first();
            if ($umxnt) {
                return back()->with('errors', '此店铺已经生成过二码合一');  //返回一次性错误
            }
            $data = [
                'alipay_user_id' => $alistore->user_id,
                'alipay_auth_shop_name' => $alistore->auth_shop_name,
                'promoter_id' => $alistore->promoter_id,
                'alipay_app_auth_token' => $alistore->app_auth_token,
                'weixin_mch_id' => $wxstore->mch_id
            ];
            AlipayWeixin::create($data);
            return redirect(route('AlipayWexinLists'));
        } else {
            return back()->with('errors', '此店铺没有添加微信商户信息');  //返回一次性错误
        }

    }

    public function delAlipayWexin(Request $request)
    {
        $auth = Auth::user()->can('alipayWeixinDelete');
        if (!$auth) {
            $data = [
                'status' => 1,
            ];
            return json_encode($data);
        }
        $id = $request->get('id');
        if ($id) {
         DB::table("multi_codes")->where("id",$id)->delete();
        }
        $data = [
            'status' => 1,
        ];
        return json_encode($data);
    }

    public function qr(Request $request)
    {
        $id = $request->get('id');
        AlipayWeixin::where('id', $id)->first();
        return view('admin/alipaeweixin/');
    }
}