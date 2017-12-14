<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/4/14
 * Time: 17:42
 */

namespace App\Http\Controllers\UnionPay;


use App\Http\Controllers\Controller;
use App\Models\UnionPayConfig;

class BaseController extends Controller
{
    public function AopClient()
    {
        $config = UnionPayConfig::where('id', 1)->first();
        //1.接入参数初始化
        $c = new AopClient();
       // $c->signType = "RSA2";//升级算法
        $c->AcquirerId=$config->acquirer_id;
        $c->gatewayUrl = 'https://openapi-paycompany-liquidation-test.wechatpark.com/gateway';
        $c->appId = $config->app_id;
        //软件生成的应用私钥字符串
        $c->rsaPrivateKey = $config->rsa_private_key;
        return $c;
    }

}