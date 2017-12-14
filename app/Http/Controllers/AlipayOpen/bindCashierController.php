<?php
namespace App\Http\Controllers\AlipayOpen;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\CountValidator\Exception;

class bindCashierController extends Controller{
    public function bindCashierIndex(Request $request){
        $store_name=$request->get('store_name');
        $store_id=$request->get('store_id');
        //当面付收银员
        $a=DB::table("alipay_app_oauth_users")
            ->join("merchant_shops","alipay_app_oauth_users.store_id","=","merchant_shops.store_id")
            ->join("merchants","merchant_shops.merchant_id","=","merchants.id")
            ->where("alipay_app_oauth_users.auth_shop_name",$store_name)
            ->select("merchants.name","merchants.id");
        //微信收银员
        $b=DB::table("weixin_shop_lists")
            ->join("merchant_shops","weixin_shop_lists.store_id","=","merchant_shops.store_id")
            ->join("merchants","merchant_shops.merchant_id","=","merchants.id")
            ->where("weixin_shop_lists.store_name",$store_name)
            ->select("merchants.name","merchants.id");
        $c=DB::table("pufa_stores")
            ->join("merchant_shops","pufa_stores.store_id","=","merchant_shops.store_id")
            ->join("merchants","merchant_shops.merchant_id","=","merchants.id")
            ->where("pufa_stores.store_name",$store_name)
            ->select("merchants.name","merchants.id");
        //口碑收银员
        $d=DB::table("alipay_shop_lists")
            ->join("merchant_shops","alipay_shop_lists.store_id","=","merchant_shops.store_id")
            ->join("merchants","merchant_shops.merchant_id","=","merchants.id")
            ->where("alipay_shop_lists.main_shop_name",$store_name)
            ->select("merchants.name","merchants.id");
        //微众
        $e=DB::table("we_bank_stores")
            ->join("merchant_shops","we_bank_stores.store_id","=","merchant_shops.store_id")
            ->join("merchants","merchant_shops.merchant_id","=","merchants.id")
            ->where("we_bank_stores.alias_name",$store_name)
            ->select("merchants.name","merchants.id");
            // 银联钱包
        $f=DB::table("union_store")
            ->join("merchant_shops","union_store.store_id","=","merchant_shops.store_id")
            ->join("merchants","merchant_shops.merchant_id","=","merchants.id")
            ->where("union_store.store_name",$store_name)
            ->select("merchants.name","merchants.id");
        //平安收银员
        $cashier=DB::table("pingan_stores")
            ->join("merchant_shops","pingan_stores.external_id","=","merchant_shops.store_id")
            ->join("merchants","merchant_shops.merchant_id","=","merchants.id")
            ->where("pingan_stores.alias_name",$store_name)
            ->select("merchants.name","merchants.id")
            ->union($a)
            ->union($b)
            ->union($c)
            ->union($d)
            ->union($e)
            ->union($f)
            ->get();
        $first=substr($store_id,0,1);
        return view("admin.alipayopen.Cashier.bindCashier",compact("store_name","store_id","cashier","first"));
    }
    public function bindCashier(Request $request){
        $data=$request->except("_token");
        $data['created_at']=date("Y-m-d H:i:s");
        $list=DB::table("merchant_shops")
            ->where("merchant_id",$request->get("merchant_id"))
            ->where("store_type",$request->get("store_type"))
            ->first();
        try{
            if($list){
                return json_encode(
                    [
                        "success"=>0,
                        "sub_msg"=>"请勿重复绑定"
                    ]
                );
           }else{
                if(DB::table("merchant_shops")->insert($data)){
                    return json_encode(
                        [
                            "success"=>1
                        ]
                    );
                }else{
                    return json_encode(
                        [
                            "success"=>0,
                            "sub_msg"=>"绑定失败"
                        ]
                    );
                }

            }
        }catch(Exception $e){

        }
    }
}
?>