<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/1/17
 * Time: 22:56
 */

namespace App\Http\Controllers\PingAn;


use App\Http\Controllers\Controller;
use App\Models\PinganConfig;

class BaseController extends Controller
{
    public function AopClient()
    {
        $config = PinganConfig::where('id', 1)->first();
        //1.接入参数初始化
        $c = new AopClient();
        $c->signType = "RSA2";//升级算法
        $c->gatewayUrl = 'https://openapi-liquidation.51fubei.com/gateway';
        $c->appId = $config->app_id;
        //软件生成的应用私钥字符串
        $c->rsaPrivateKey = $config->rsaPrivateKey;
        return $c;
    }


}