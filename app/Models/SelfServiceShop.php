<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelfServiceShop extends Model
{
    //
    protected $fillable = [
        'store_id', 'shop_id','contact_name','category_name','user_id', 'brand_name', 'brand_logo', "main_shop_name", "branch_shop_name"
        , "province_code", "city_code", "district_code", "address", "contact_number", "main_image", "audit_images1", "audit_images2", "audit_images3",
        "licence", "business_certificate",
        "auth_letter", "other_authorization"
    ];
}
