<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlipayStoreInfo extends Model
{
    //
    protected  $fillable=['apply_id','store_id','shop_id','audit_status','notify_time',
    'is_show','request_id','biz_type','result_code'
    ];
}
