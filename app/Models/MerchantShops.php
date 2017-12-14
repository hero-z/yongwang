<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantShops extends Model
{
    //
    protected  $fillable=['merchant_id','store_id','store_name','store_type','desc_pay','status'];
}
