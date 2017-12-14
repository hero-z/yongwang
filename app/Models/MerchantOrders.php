<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantOrders extends Model
{
    protected $fillable = ['out_trade_no','trade_no','store_id','total_amount','status','merchant_id','merchant_name','pay_ways','type'];
}
