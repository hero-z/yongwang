<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlipayIsvConfig extends Model
{
    //
    protected  $fillable=['app_id','pid','operate_notify_url','rsaPrivateKey','rsaPrivateKeyFilePath','alipayrsaPublicKey','rsaPublicKeyFilePath','callback','notify'];
}
