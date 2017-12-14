<?php 
namespace App\Common;

use Illuminate\Support\Facades\DB;

/*

	获取当前系统的所有店铺
*/
class AllStore
{
	/*
		收银员
	*/
	public static function get2()
	{

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

	/*
		返回所有店铺数组对象

		返回
		Array
	(
	    [0] => stdClass Object
	        (
	            [store_name] => 果果
	            [store_id] => o2088622551063974
	        )

	    [1] => stdClass Object
	        (
	            [store_name] => 农家菜
	            [store_id] => o2088622187364308
	        )


                                @foreach($oali as $v)
                                    @if($v->name)
                                        <option value="{{$v->store_id}}*{{$v->name}}">{{$v->name}}(支付宝当面付)</option>
                                    @endif
                                @endforeach
                                @foreach($sali as $v)
                                    @if($v->name)
                                        <option value="{{$v->store_id}}*{{$v->name}}">{{$v->name}}(支付宝口碑)</option>
                                    @endif
                                @endforeach
                                @foreach($weixin as $v)
                                    @if($v->name)
                                        <option value="{{$v->store_id}}*{{$v->name}}">{{$v->name}}(微信)</option>
                                    @endif
                                @endforeach
                                @foreach($pingan as $v)
                                    @if($v->name)
                                        <option value="{{$v->store_id}}*{{$v->name}}">{{$v->name}}(平安银行)</option>
                                    @endif
                                @endforeach
                                @foreach($pufa as $v)
                                    @if($v->name)
                                        <option value="{{$v->store_id}}*{{$v->name}}">{{$v->name}}(浦发银行)</option>
                                    @endif
                                @endforeach
                                @foreach($unionpay as $v)
                                    @if($v->name)
                                        <option value="{{$v->store_id}}*{{$v->name}}">{{$v->name}}(银联)</option>
                                    @endif
                                @endforeach


	*/
	public static function get()
	{

        //当面付店铺
       $temp=DB::table("alipay_app_oauth_users")
            ->select(DB::raw('auth_shop_name store_name,store_id'))
            ->get();
       $store_1=($temp->isEmpty())?[]:$temp->toArray();

        //微信 店铺
       $temp=DB::table("weixin_shop_lists")
            ->select(DB::raw('store_name,store_id'))
            ->get();
       $store_2=($temp->isEmpty())?[]:$temp->toArray();

        //浦发 店铺
       $temp=DB::table("pufa_stores")
            ->select(DB::raw('store_name,store_id'))
            ->get();
       $store_3=($temp->isEmpty())?[]:$temp->toArray();

        //口碑 店铺
       $temp=DB::table("alipay_shop_lists")
            ->select(DB::raw('main_shop_name store_name,store_id'))
            ->get();
       $store_4=($temp->isEmpty())?[]:$temp->toArray();

        //微众 店铺
       $temp=DB::table("we_bank_stores")
            ->select(DB::raw('alias_name store_name,store_id'))
            ->get();
       $store_5=($temp->isEmpty())?[]:$temp->toArray();

        //银联 店铺
       $temp=DB::table("union_store")
            ->select(DB::raw('store_name,store_id'))
            ->get();
       $store_6=($temp->isEmpty())?[]:$temp->toArray();

        //平安 店铺
       $temp=DB::table("pingan_stores")
            ->select(DB::raw('alias_name store_name,external_id store_id'))
            ->get();
       $store_7=($temp->isEmpty())?[]:$temp->toArray();


       return [
            'oali'=>$store_1,
            'weixin'=>$store_2,
            'pufa'=>$store_3,
            'sali'=>$store_4,
            'weizhong'=>$store_5,
            'unionpay'=>$store_6,
            'pingan'=>$store_7,
       ];
       // return $store;

	}


    public static function allStoreList()
    {
        $all=self::get();

        $data=[];

        foreach($all as $v)
        {
            $data=array_merge($data,$v);
        }
        return $data;

    }

}





 ?>