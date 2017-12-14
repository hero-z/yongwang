<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/21
 * Time: 15:53
 */

namespace App\Api\Controllers\Merchant;


use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use Tymon\JWTAuth\Facades\JWTAuth;

class BaseController extends Controller
{
    use Helpers;//为了让所有继承这个的控制器都使用dingoApi

    public function getMerchantInfo()
    {
        JWTAuth::setToken(JWTAuth::getToken());
        $claim = JWTAuth::getPayload();
        if ($claim['sub']['type'] == "merchant") {
            return $claim['sub'];
        } else {
            throw new \Tymon\JWTAuth\Exceptions\JWTException('你不是商户账号没法访问商户信息');
        }
    }
}