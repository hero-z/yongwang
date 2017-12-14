<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlipayShopLists extends Model
{
    //
    protected $fillable = ['store_id',"pid",'is_delete','audit_status','apply_id','shop_id','app_auth_token','category_id', 'brand_name', 'brand_logo', "main_shop_name", "branch_shop_name"
        ,"province_code","city_code","district_code","address","longitude","latitude","contact_number","notify_mobile","main_image","audit_images","business_time",
    "wifi","parking","value_added","avg_price","isv_uid","licence","licence_code","licence_name","business_certificate","business_certificate_expires",
        "auth_letter","is_operating_online","online_url","operate_notify_url","implement_id","no_smoking",
        "box","user_id","request_id","other_authorization","licence_expires","op_role","biz_version"
    ];
}
