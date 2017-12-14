<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlipayWeixin extends Model
{
    //
    protected  $fillable=['alipay_user_id','alipay_auth_shop_name','promoter_id','alipay_app_auth_token','weixin_mch_id'];
}
