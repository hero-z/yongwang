<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsConfig extends Model
{
    //
    protected $fillable=['app_key','app_secret','SignName','TemplateCode'];
}
