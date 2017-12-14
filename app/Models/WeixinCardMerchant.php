<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeixinCardMerchant extends Model
{
    //
    protected  $fillable=['merchant_id','app_id','brand_name','logo_url_local','logo_url','logo_url_mediaid','license','idcard','protocol','status','primary_category_id','secondary_category_id','end_time','begin_time','updated_at'];

}
