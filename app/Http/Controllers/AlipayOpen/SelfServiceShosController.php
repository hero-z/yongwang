<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2016/12/30
 * Time: 11:36
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Models\SelfServiceShop;
use Illuminate\Http\Request;

class SelfServiceShosController extends AlipayOpenController
{

    public function index()
    {
        return view('admin.alipayopen.selfshops.index');
    }

    public function selfserviceadd()
    {
        return view('admin.alipayopen.selfshops.selfserviceadd');
    }

    public function SelfShopPost(Request $request)
    {
        if($request->get('main_shop_name')||$request->get('contact_number')){
            $data = [
                "store_id" => $request->get('store_id'),
                "user_id" => $request->get('user_id'),
                "main_shop_name" => $request->get('main_shop_name'),
                "branch_shop_name" => $request->get('branch_shop_name'),
                "contact_number" => $request->get('contact_number'),
                "brand_name" => $request->get('brand_name'),
                "province_code" => $request->get('province_code'),
                "city_code" => $request->get('city_code'),
                "district_code" => $request->get('district_code'),
                "address" => $request->get('address'),
                "brand_logo" => $request->get('brand_logo'),
                "image" => $request->get('image'),
                "licence" => $request->get('licence'),
                "business_certificate" => $request->get('business_certificate'),
                "auth_letter" => $request->get('auth_letter'),
                "main_image" => $request->get('main_image'),
                "audit_images1" => $request->get('audit_images1'),
                "audit_images2" => $request->get('audit_images2'),
                "audit_images3" => $request->get('audit_images3'),
                "other_authorization" => $request->get('other_authorization'),
                "category_name" => $request->get('category_name'),
                "contact_name" => $request->get('contact_name'),
            ];
            $re=SelfServiceShop::create($data);
        }

     return view('admin.alipayopen.page.success');
    }

}