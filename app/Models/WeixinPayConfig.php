<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeixinPayConfig extends Model
{
    //
    protected  $fillable=['app_id','key','secret','merchant_id','cert_path','key_path','notify_url'];
}
