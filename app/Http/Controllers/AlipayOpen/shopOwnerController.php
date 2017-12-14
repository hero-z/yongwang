<?php
namespace App\Http\Controllers\AlipayOpen;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class shopOwnerController extends AlipayOpenController{
    public function changeShopOwner(Request $request){
        $auth = Auth::user()->can('changeShopOwner');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $users=DB::table("users")->select("id","name")->get();

        //dd($pingan);
        return view("admin.alipayopen.users.changeShopOwner",['users'=>$users]);

    }
//    public function changeOwnerTwo(Request $request){
//        $id=$request->hidden;
//        $name=$request->shopname;
//        //当面付店铺
//        //dd($id);
//        $users=DB::table("users")->select("id","name")->get();
//        $ali=DB::table("alipay_app_oauth_users")->where("promoter_id",$id)->where("auth_shop_name",'like','%'.$name.'%')->select("auth_shop_name","store_id");
//        //口碑
//        $sali=DB::table("alipay_shop_lists")->where("user_id",$id)->where("main_shop_name",'like','%'.$name.'%')->select("main_shop_name","store_id");
//        //微信
//        $weixin=DB::table("weixin_shop_lists")->where("user_id",$id)->where("store_name",'like','%'.$name.'%')->select("store_name","store_id");
//        //平安银行
//        $pingan=DB::table("pingan_stores")
//            ->where("user_id",$id)
//            ->where("name",'like','%'.$name.'%')
//            ->select("name","external_id")
//            ->union($ali)
//            ->union($sali)
//            ->union($weixin)
//            ->get();
//        return view("admin.alipayopen.users.changeOwnerTwo",['pingan'=>$pingan,"users"=>$users,"id"=>$id]);
//    }
    public function changeTo(Request $request){
        $id=$request->id;
        //当面付店铺
        $ali=DB::table("alipay_app_oauth_users")->where("promoter_id",$id)->where("is_delete",0)->select("auth_shop_name","store_id");
        //口碑
        $sali=DB::table("alipay_shop_lists")->where("user_id",$id)->where("is_delete",0)->select("main_shop_name","store_id");
        //微信
        $weixin=DB::table("weixin_shop_lists")->where("user_id",$id)->select("store_name","store_id");
        //民生
        $ms=DB::table('ms_stores')
            ->where("user_id",$id)
            ->select('store_short_name as store_name','store_id');
        //微众
        $wb=DB::table('we_bank_stores')
            ->where("user_id",$id)
            ->select('alias_name as store_name','store_id');
        //浦发
        $pf=DB::table('pufa_stores')
            ->where("user_id",$id)
            ->select('merchant_short_name as store_name','store_id');
        //银联
        $un=DB::table('union_pay_stores')
            ->where("user_id",$id)
            ->select('alias_name as store_name','store_id');
        //平安银行
        $pingan=DB::table("pingan_stores")
            ->where("user_id",$id)
            ->where("is_delete",0)
            ->select("alias_name","external_id")
            ->union($ali)
            ->union($sali)
            ->union($weixin)
            ->union($ms)
            ->union($wb)
            ->union($pf)
            ->union($un)
            ->get()
            ->toArray();
        return json_encode($pingan);
    }
    public function changeOwner(Request $request){
        $auth = Auth::user()->can('changeShopOwner');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $store_id=$request->get("su");
        $id=$request->to;
        $sid=$request->from;
        if($store_id==""){
            return back()->with("warnning","请选择要转移的店铺");
        }elseif($id==$sid){
            return back()->with("warnning","请选择不同的员工进行转移");
        }elseif($id&&$sid){
            foreach($store_id as $k=>$v){
                $s=substr($store_id[$k],0,1);
                if($s=="o"){
                    $data["promoter_id"]=$id;
                    DB::table("alipay_app_oauth_users")->where("store_id",$store_id[$k])->update($data);
                }
                if($s=="s"){
                    $data["user_id"]=$id;
                    DB::table("alipay_shop_lists")->where("store_id",$store_id[$k])->update($data);
                }
                if($s=="w"){
                    $data["user_id"]=$id;
                    DB::table("weixin_shop_lists")->where("store_id",$store_id[$k])->update($data);
                }
                if($s=="p"){
                    $data["user_id"]=$id;
                    DB::table("pingan_stores")->where("external_id",$store_id[$k])->update($data);
                }
                if($s=="u"){
                    $data["user_id"]=$id;
                    DB::table("union_pay_stores")->where("store_id",$store_id[$k])->update($data);
                }
                if($s=="b"){
                    $data["user_id"]=$id;
                    DB::table("we_bank_stores")->where("store_id",$store_id[$k])->update($data);
                }
                if($s=="f"){
                    $data["user_id"]=$id;
                    DB::table("pufa_stores")->where("store_id",$store_id[$k])->update($data);
                }
                if($s=="m"){
                    $data["user_id"]=$id;
                    DB::table("ms_stores")->where("store_id",$store_id[$k])->update($data);
                }
            }
            return back()->with("warnning","转移成功");
        }else{
            return back()->with("warnning","请选择两个员工");
        }
    }
}
?>