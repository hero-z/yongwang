<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantPos extends Model
{
    //
    protected  $fillable=['merchant_id','poscode'];

    public function test()
    {
    	echo __FILE__;		
    }
}
