<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/2
 * Time: 11:19
 */

namespace App\Http\Controllers\AlipayOpen;


class AlipayOrderController extends AlipayOpenController
{

    public function create(){
       return view("admin.alipayopen.createorder");
    }

}