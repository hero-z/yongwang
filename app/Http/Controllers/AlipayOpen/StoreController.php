<?php

namespace App\Http\Controllers\AlipayOpen;

use Alipayopen\Sdk\Request\AlipayOfflineMarketShopCategoryQueryRequest;
use Alipayopen\Sdk\Request\AlipayOfflineMarketShopCreateRequest;
use Alipayopen\Sdk\Request\AlipayOfflineMarketShopModifyRequest;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayShopCategory;
use App\Models\AlipayShopLists;
use App\Models\ProvinceCity;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class StoreController extends AlipayOpenController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shopname = $request->input("shopname");
        $where=[];
        if($shopname){
            $where[]=["alipay_shop_lists.main_shop_name", 'like', '%' . $shopname . '%'];

        } 
        $auth = Auth::user()->can('store');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $data = DB::table('alipay_shop_lists')->where("alipay_shop_lists.pid",0)->where($where)->select('alipay_shop_lists.*', 'users.name')->orderBy('created_at', 'desc')->where('user_id', Auth::user()->id)->where("alipay_shop_lists.is_delete",0)->join('users', 'users.id', '=', 'alipay_shop_lists.user_id')->get();
        // $data = AlipayShopLists::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        if (Auth::user()->hasRole('admin')) {
            $data = DB::table('alipay_shop_lists')->where("alipay_shop_lists.pid",0)->where($where)->select('alipay_shop_lists.*', 'users.name')->orderBy('created_at', 'desc')->where("alipay_shop_lists.is_delete",0)->join('users', 'users.id', '=', 'alipay_shop_lists.user_id')->get();
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
        return view('admin.alipayopen.store.index', compact('datapage', 'paginator',"shopname"));
    }

    //删除(软删除)
    public function storeChangeStatus(Request $request){
        $auth = Auth::user()->can('alipayShopChangeStatus');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $id=$request->get("id");
        $data['is_delete']=1;
//        $list=DB::table("alipay_app_oauth_users")->where("id",$id)->first();
//        dd($list);
        if(DB::table("alipay_shop_lists")->where("id",$id)->update($data)){
            return redirect("/admin/alipayopen/store");
        }
    }
    //加载还原页
    public function restoreIndex(){
        $auth = Auth::user()->can('alipayShopRestore');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $data = DB::table('alipay_shop_lists')->where('user_id', Auth::user()->id)->select('alipay_shop_lists.*', 'users.name')->orderBy('updated_at', 'desc')->where("alipay_shop_lists.is_delete",1)->join('users', 'users.id', '=', 'alipay_shop_lists.user_id')->paginate(8);
        if (Auth::user()->hasRole('admin')) {
            $data = DB::table('alipay_shop_lists')->select('alipay_shop_lists.*', 'users.name')->orderBy('updated_at', 'desc')->where("alipay_shop_lists.is_delete",1)->join('users', 'users.id', '=', 'alipay_shop_lists.user_id')->paginate(8);
        }
      //dd($data);
        return view("admin.alipayopen.store.storeRestore",["data"=>$data]);

    }
    //执行搜索
    public function storeSearch(Request $request){
        $shopname=$request->input("shopname");
        $auth = Auth::user()->can('store');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $data = DB::table('alipay_shop_lists')->where("alipay_shop_lists.pid",0)->select('alipay_shop_lists.*', 'users.name')->orderBy('updated_at', 'desc')->where('user_id', Auth::user()->id)->where("alipay_shop_lists.main_shop_name",'like','%'.$shopname.'%')->join('users', 'users.id', '=', 'alipay_shop_lists.user_id')->get();
        // $data = AlipayShopLists::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        if (Auth::user()->hasRole('admin')) {
            $data = DB::table('alipay_shop_lists')->where("alipay_shop_lists.pid",0)->select('alipay_shop_lists.*', 'users.name')->orderBy('updated_at', 'desc')->where("alipay_shop_lists.is_delete",0)->join('users', 'users.id', '=', 'alipay_shop_lists.user_id')->where("alipay_shop_lists.main_shop_name",'like','%'.$shopname.'%')->get();
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

        return view('admin.alipayopen.store.index', compact('datapage', 'paginator','shopname'));
    }
    //执行还原搜索
    public function storeRestoreSearch(Request $request){
        $shopname=$request->input("shopname");
        $data = DB::table('alipay_shop_lists')->select('alipay_shop_lists.*', 'users.name')->orderBy('updated_at', 'desc')->where("alipay_shop_lists.is_delete",1)->where("alipay_shop_lists.main_shop_name",'like','%'.$shopname.'%')->join('users', 'users.id', '=', 'alipay_shop_lists.user_id')->paginate(8);
        // dd($data);
        return view("admin.alipayopen.store.storeRestore",["data"=>$data,'shopname'=>$shopname]);
    }
    //还原选中
    public function storeRestore(Request $request){
        $s=$request->get("data");
        //dd($s);
        $data['is_delete']=0;
        foreach($s as $v){
            DB::table("alipay_shop_lists")->where("id",$v)->update($data);
        }
        return redirect("/admin/alipayopen/restoreIndex");
    }
    //单个还原
    public function storeRestoree(Request $request){
        $id=$request->id;
        $data['is_delete']=0;
        if(DB::table("alipay_shop_lists")->where("id",$id)->update($data)){
            return redirect("/admin/alipayopen/restoreIndex");
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $auth = Auth::user()->can('openShop');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        return view('admin.alipayopen.store.create');

    }


    //执行彻底删除
    public function delShop(Request $request){
        $auth = Auth::user()->can('delete');
        if (!$auth) {
            return json_encode(['success' => 0]);
        }else{
            $id = $request->get("id");
            try {
                if (DB::table("alipay_shop_lists")->where("id", $id)->delete()) {
                    return json_encode(['success' => 1]);
                }
            } catch (\Exception $exception) {
                return json_encode(['success' => 0]);
            }
        }
    }
    //加载添加已开店铺页
    public function addOldShop(){
        $auth = Auth::user()->can('addOldShop');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $list=DB::table("alipay_app_oauth_users")->where("promoter_id",Auth::user()->id)->where("is_delete",0)->get();
        if(Auth::user()->hasRole("admin")){
            $list=DB::table("alipay_app_oauth_users")->where("is_delete",0)->get();
        }
        return view("admin.alipayopen.store.addOldShop",compact("list"));
    }
    //执行已开店铺添加
    public function insertOldShop(Request $request){
        $data['user_id']=Auth::user()->id;
        $data['shop_id']=$request->get("shop_id");
        $data['store_id']="s".$request->get("store_id");
        $data['brand_name']="";
        $data['audit_status']="AUDIT_SUCCESS";
        $data["brand_logo"]="";
        $data['apply_id']=$request->get("shop_id");
        $data["branch_shop_name"]="";
        $data['address']="";
        $data['longitude']=6565;
        $data['latitude']="";
        $data['contact_number']="";
        $data['notify_mobile']="";
        $data['main_image']="";
        $data['audit_images']="";
        $data['business_time']="";
        $data['wifi']="";
        $data['parking']="";
        $data['value_added']="";
        $data['avg_price']="";
        $data['isv_uid']="";
        $data['licence']="";
        $data['licence_code']="";
        $data['licence_name']="";
        $data['business_certificate']="";
        $data['business_certificate_expires']="";
        $data['auth_letter']="";
        $data['is_operating_online']="";
        $data['online_url']="";
        $data['operate_notify_url']="";
        $data['implement_id']="";
        $data['no_smoking']="";
        $data['box']="";
        $data['created_at']=date("Y-m-d H:i:s");
        $data['request_id']="";
        $data['other_authorization']="";
        $data['licence_expires']="";
        $data['op_role']="";
        $data['biz_version']="";
        $data['category_id']=2015050700000038;
        $a=$request->get("auth_shop");
        $list=explode("*",$a);
        if(count($list)>=2){
            $data['app_auth_token']=$list[0];
            $data['main_shop_name']=$list[1];
            if(DB::table("alipay_shop_lists")->where("main_shop_name",$list[1])->first()){
                return back()->with("warnning","该店铺名已存在");
            }elseif(DB::table("alipay_shop_lists")->where("store_id",$data['store_id'])->first()){
                return back()->with("warnning","添加失败,store_id已存在");
            }elseif (DB::table("alipay_shop_lists")->insert($data)){
                return back()->with("warnning","添加成功");
            }
        }else{
            return back()->with("warnning","添加失败");
        }
    }

    /**创建店铺
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $config = AlipayIsvConfig::where('id', 1)->first();
        if ($config) {
            $config = $config->toArray();
        }
        $longitude_latitude = explode(',', $request->get("longitude_latitude"));//经纬度
        $longitude = $longitude_latitude[0];//经度
        $latitude = $longitude_latitude[1];//纬度
        $store_id = $request->get('store_id');
        $data = [
            "store_id" => "" . $store_id . "",
            "apply_id" => "",
            "shop_id" => "",
            "user_id" => Auth::user()->id,
            "category_id" => "" . $request->get('category_id') . "",
            "app_auth_token" => "" . $request->get('app_auth_token') . "",
            "brand_name" => "" . $request->get('brand_name') . "",
            "brand_logo" => "" . $request->get('brand_logo') . "",
            "main_shop_name" => "" . $request->get('main_shop_name') . "",
            "branch_shop_name" => "" . $request->get('branch_shop_name') . "",
            "province_code" => "" . $request->get('province_code') . "",
            "city_code" => "" . $request->get('city_code') . "",
            "district_code" => "" . $request->get('district_code') . "",
            "address" => "" . $request->get('address') . "",
            "longitude" => $longitude,
            "latitude" => "" . $latitude . "",
            "contact_number" => "" . $request->get('contact_number') . "",
            "notify_mobile" => "" . $request->get('notify_mobile') . "",
            "main_image" => "" . $request->get('main_image') . "",
            "audit_images" => "" . $request->get('audit_images1') . "," . $request->get('audit_images2') . "," . $request->get('audit_images3') . "",
            "business_time" => "" . $request->get('business_time') . "",
            "wifi" => "" . $request->get('wifi') . "",
            "parking" => "" . $request->get('parking') . "",
            "value_added" => "" . $request->get('value_added') . "",
            "avg_price" => "" . $request->get('avg_price') . "",
            "isv_uid" => "" . $config['pid'] . "",
            "licence" => "" . $request->get('licence') . "",
            "licence_code" => "" . $request->get('licence_code') . "",
            "licence_name" => "" . $request->get('licence_name') . "",
            "business_certificate" => "" . $request->get('business_certificate') . "",
            "business_certificate_expires" => "" . $request->get('business_certificate_expires') . "",
            "auth_letter" => "" . $request->get('auth_letter') . "",
            "is_operating_online" => "" . $request->get('is_operating_online') . "",
            "online_url" => "" . $request->get('online_url') . "",
            "operate_notify_url" => "" . url('/operate_notify_url') . "",
            "implement_id" => "" . $request->get('implement_id') . "",
            "no_smoking" => "" . $request->get('no_smoking') . "",
            "box" => "" . $request->get('box') . "",
            "request_id" => "" . $request->get('request_id') . "",
            "other_authorization" => "" . $request->get('other_authorization') . "",
            "licence_expires" => "" . $request->get('licence_expires') . "",
            "op_role" => "ISV",
            "biz_version" => "2.0",//这个参数很重要
        ];
        $shop = AlipayShopLists::where('store_id', $store_id)->first();
        if ($shop) {
            AlipayShopLists::where('store_id', $store_id)->update($data);
        } else {
            AlipayShopLists::create($data);
        }
        //提交到口碑
        $aop = $this->AopClient();
        $aop->apiVersion = "2.0";
        $aop->method = 'alipay.offline.market.shop.create';
        $requests = new AlipayOfflineMarketShopCreateRequest();
        $requests->setBizContent("{" .
            "\"store_id\":\"" . $request->get('store_id') . "\"," .
            "\"category_id\":\"" . $request->get('category_id') . "\"," .
            "\"brand_name\":\"" . $request->get('brand_name') . "\"," .
            "\"brand_logo\":\"" . $request->get('brand_logo') . "\"," .
            "\"main_shop_name\":\"" . $request->get('main_shop_name') . "\"," .
            "\"branch_shop_name\":\"" . $request->get('branch_shop_name') . "\"," .
            "\"province_code\":\"" . $request->get('province_code') . "\"," .
            "\"city_code\":\"" . $request->get('city_code') . "\"," .
            "\"district_code\":\"" . $request->get('district_code') . "\"," .
            "\"address\":\"" . $request->get('address') . "\"," .
            "\"longitude\":" . $longitude . "," .
            "\"latitude\":\"" . $latitude . "\"," .
            "\"contact_number\":\"" . $request->get('contact_number') . "\"," .
            "\"notify_mobile\":\"" . $request->get('notify_mobile') . "\"," .
            "\"main_image\":\"" . $request->get('main_image') . "\"," .
            "\"audit_images\":\"" . $request->get('audit_images1') . "," . $request->get('audit_images2') . "," . $request->get('audit_images3') . "\"," .
            "\"business_time\":\"" . $request->get('business_time') . "\"," .
            "\"wifi\":\"" . $request->get('wifi') . "\"," .
            "\"parking\":\"" . $request->get('parking') . "\"," .
            "\"value_added\":\"" . $request->get('value_added') . "\"," .
            "\"avg_price\":\"" . $request->get('avg_price') . "\"," .
            "\"isv_uid\":\"" . $config['pid'] . "\"," .
            "\"licence\":\"" . $request->get('licence') . "\"," .
            "\"licence_code\":\"" . $request->get('licence_code') . "\"," .
            "\"licence_name\":\"" . $request->get('licence_name') . "\"," .
            "\"business_certificate\":\"" . $request->get('business_certificate') . "\"," .
            "\"business_certificate_expires\":\"" . $request->get('business_certificate_expires') . "\"," .
            "\"auth_letter\":\"" . $request->get('auth_letter') . "\"," .
            "\"is_operating_online\":\"" . $request->get('is_operating_online') . "\"," .
            "\"online_url\":\"" . $request->get('online_url') . "\"," .
            "\"operate_notify_url\":\"" . url('/operate_notify_url') . "\"," .
            "\"implement_id\":\"" . $request->get('implement_id') . "\"," .
            "\"no_smoking\":\"" . $request->get('no_smoking') . "\"," .
            "\"box\":\"" . $request->get('box') . "\"," .
            "\"request_id\":\"" . $request->get('request_id') . "\"," .
            "\"other_authorization\":\"" . $request->get('other_authorization') . "\"," .
            "\"licence_expires\":\"" . $request->get('licence_expires') . "\"," .
            "\"op_role\":\"ISV\"," .
            "\"biz_version\":\"2.0\"" .
            "  }");
        $result = $aop->execute($requests, NULL, $request->get('app_auth_token'));
        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        if ($result->$responseNode->code == 10000) {
            //存储数据库
            $updata = [
                "apply_id" => $result->$responseNode->apply_id,
                "audit_status"=>""//重新提交以后去除状态
            ];
            AlipayShopLists::where('store_id', $store_id)->update($updata);
            $re = [
                'code' => $result->$responseNode->code,
                'sub_msg' => $result->$responseNode->msg,
            ];
        } else {
            $re = [
                'code' => $result->$responseNode->code,
                'sub_msg' => $result->$responseNode->sub_msg,
            ];
        }
        return json_encode($re);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //分类
        $category = AlipayShopCategory::all();
        if ($category) {
            $category = $category->toArray();
        }

        $shop = AlipayShopLists::where('id', $id)->first();
        if ($shop) {
            $shop = $shop->toArray();
        }
        $audit_images = explode(',', $shop['audit_images']);
        $shop['audit_images1'] = $audit_images[0];
        $shop['audit_images2'] = $audit_images[1];
        $shop['audit_images3'] = $audit_images[2];
        //地区 省
        $province = ProvinceCity::where('areaParentId', 1)->get();
        $city = ProvinceCity::where('areaCode', $shop['city_code'])->get();
        $district = ProvinceCity::where('areaCode', $shop['district_code'])->get();
        if ($province) {
            $province = $province->toArray();
        }
        if ($city) {
            $city = $city->toArray();
        }
        if ($district) {
            $district = $district->toArray();
        }
        $province_city_district = [
            'province' => $province,
            'city' => $city,
            'district' => $district
        ];
        return view('admin.alipayopen.store.edit', compact('shop', 'category', 'province_city_district'));
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    //修改口碑商户名称
    public function editshoplists(Request $request){
        $id=$request->get("id");
        $list=DB::table("alipay_shop_lists")->where("id",$id)->first();
        return view("admin.alipayopen.store.editshoplist",compact("list"));
    }
    public function updateshoplists(Request $request){
        $id=$request->get("id");
        $name=$request->get("name");
        $list=DB::table('alipay_shop_lists')->where("id",$id)->first();
        $info=DB::table("merchant_shops")->where("store_id",$list->store_id)->get()->toArray();
        if($info){
            return back()->with("warnning","店铺已绑定,请先解绑再修改");
        }else{
            $data['main_shop_name']=$name;
            if(DB::table("alipay_shop_lists")->where("id",$id)->update($data)){
                return back()->with("warnning","修改成功");
            }
            return back();
        }
        return back();
    }
}
