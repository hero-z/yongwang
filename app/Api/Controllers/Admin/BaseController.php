<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/21
 * Time: 15:53
 */

namespace App\Api\Controllers\Admin;


use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use Tymon\JWTAuth\Facades\JWTAuth;

class BaseController extends Controller
{
    use Helpers;//为了让所有继承这个的控制器都使用dingoApi

    public function getAdminInfo()
    {
        JWTAuth::setToken(JWTAuth::getToken());
        $claim = JWTAuth::getPayload();
        if ($claim['sub']['type'] == "user") {
            return $claim['sub'];
        } else {
            throw new \Tymon\JWTAuth\Exceptions\JWTException('你不是代理商账号没法访问代理商信息');
        }
    }
}