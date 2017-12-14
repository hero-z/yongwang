<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2017/6/13
 * Time: 下午3:11
 */

namespace App\Api\Controllers\Admin;


class UsersController extends BaseController
{

    public function getUsers(){


        $this->getAdminInfo();
        dd('11111');
    }

}