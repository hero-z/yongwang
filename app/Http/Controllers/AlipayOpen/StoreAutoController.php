<?php

namespace App\Http\Controllers\AlipayOpen;

use Alipayopen\Sdk\Request\AlipayOfflineMarketShopCreateRequest;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayShopLists;
use Illuminate\Http\Request;

class StoreAutoController extends AlipayOpenController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.alipayopen.store.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
            "user_id" => "" . $request->get('promoter_id') . "",
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
