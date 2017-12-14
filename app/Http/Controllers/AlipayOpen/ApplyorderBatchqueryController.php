<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2016/12/22
 * Time: 13:43
 */

namespace App\Http\Controllers\AlipayOpen;


use Illuminate\Support\Facades\Auth;

class ApplyorderBatchqueryController extends AlipayOpenController
{


    public function query()
    {
        $auth = Auth::user()->can('ApplyorderBatchquery');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        return view('admin.alipayopen.store.applyorderbatchquery');
    }
}