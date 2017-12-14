<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnionPayStore extends Model
{
    //
    protected $fillable = [
        'store_id',
        'pid',
        'merchant_name',
        'alias_name',
        'province_code',
        'city_code',
        'district_code',
        'is_t0',
        'address',
        'telephone',
        'email',
        'manager',
        'manager_phone',
        'manager_id_card',
        'manager_id_card_img',
        'store_img',
        'legal_man',
        'service_telephone',
        'business_licence_img',
        "local_image",
        'sub_merchant_id',
        'user_id',
        'status'];
}
