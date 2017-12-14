<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinganStore extends Model
{
    //
    protected $fillable = ['external_id','pid','merchant_rate', 'name', 'alias_name', 'service_phone', 'contact_name'
        , 'contact_phone', 'contact_mobile', 'contact_email', 'category_id', 'memo', 'sub_merchant_id'
        ,'bank_card_no','card_holder','is_public_account','open_bank','status','user_id','user_name','sub_mch_id','sub_appid_status',
        'jsapi_path_status','subscribe_appid_status'
    ];
}
