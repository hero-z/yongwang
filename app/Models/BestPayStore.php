<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BestPayStore extends Model
{
    //
    protected $fillable=['admin_id', 'merchant_id','store_id','store_name','alias_name','contact_name','contact_phone', 'merchantId', 'data_key' , 'pay_key','created_at','updated_at'];
}
