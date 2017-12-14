<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlipayTradeQuery extends Model
{
    //

    protected  $fillable=['out_trade_no','merchant_id','merchant_name','trade_no','store_id',"type",'status','total_amount'];
}
