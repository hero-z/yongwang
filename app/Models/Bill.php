<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    //
    protected $fillable=['admin_id', 'merchant_id','store_id','device_no','trade_no','trade_req_no','out_trade_no','type','total_amount','receipt_amount','pay_amount','invoice_amount','trade_status','pay_status','refund_flag','refund_amount','created_at','updated_at'];
}
