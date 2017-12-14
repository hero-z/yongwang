<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithDraw extends Model
{
    //
    protected $fillable=['merchant_id','sub_merchant_id','tran_amount','lp_jzb_account_type',"withdraw_no"];
}
