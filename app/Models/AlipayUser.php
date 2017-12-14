<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlipayUser extends Model
{
    //
    protected $fillable=['user_id', 'auth_app_id','app_auth_token', 'app_refresh_token', 'expires_in' , 're_expires_in'];
}
