<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlipayAppOauthUsers extends Model
{
    //
    protected $fillable=['user_id','pid','store_id','is_delete','promoter_id','auth_shop_name','auth_phone','auth_app_id','app_auth_token', 'app_refresh_token', 'expires_in' , 're_expires_in'];

}
