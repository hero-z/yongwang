<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'out_trade_no',
        'trade_no',
        'store_id',
        'merchant_id',
        'type',
        'total_amount',
        'status',
        'pay_status',
        'cost_rate',
        'service_rate',
        'buyer_id',
        "remark",
        "hb_fq_num",
        "hb_fq_seller_percent",
        "hb_fq_sxf"
    ];
}
