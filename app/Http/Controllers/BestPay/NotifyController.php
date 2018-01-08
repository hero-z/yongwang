<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/12/28
 * Time: 15:33
 */

namespace App\Http\Controllers\BestPay;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotifyController  extends BaseController
{
    public function mPayBack(Request $request)
    {
        Log::info($request->all());
    }
}