<?php

namespace App\Http\Controllers\AlipayOpen;

use App\Http\Controllers\Controller;
use App\Models\AlipayIsvConfig;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Config;

class AlipayOpenController extends Controller
{
    //初始化阿里开放平台参数
    public function AopClient()
    {
        $config=AlipayIsvConfig::where('id',1)->first();
        if($config){
            $config=$config->toArray();
        }
        //1.接入参数初始化
        $c = app('AopClient');
        $c->signType="RSA2";//升级算法
        $c->gatewayUrl = Config::get('alipayopen.gatewayUrl');
        $c->appId = $config['app_id'];
        //软件生成的应用私钥字符串
        $c->rsaPrivateKey = $config['rsaPrivateKey'];
        //软件生成的应用私钥文件路径
       // $c->rsaPrivateKeyFilePath = $config['rsaPrivateKeyFilePath'];
        //软件生成的应用公钥路径
      //  $c->rsaPublicKeyFilePath = $config['rsaPublicKeyFilePath'];
        //开发平台后台的支付宝rsa公钥(不是应用公钥)
      //  $c->alipayrsaPublicKey = $config['alipayrsaPublicKey'];
        $c->format = "json";
        $c->charset = "GBK";
        $c->version="2.0";
        return $c;
    }

    //初始化阿里开放平台参数
    public function AopClientNotify()
    {
        $config=AlipayIsvConfig::where('id',1)->first();
        if($config){
            $config=$config->toArray();
        }
        //1.接入参数初始化
        $c = app('AopClient');
        $c->gatewayUrl = Config::get('alipayopen.gatewayUrl');
        $c->appId = $config['app_id'];
        //软件生成的应用私钥字符串
        $c->rsaPrivateKey = $config['rsaPrivateKey'];
        //软件生成的应用私钥文件路径
        // $c->rsaPrivateKeyFilePath = $config['rsaPrivateKeyFilePath'];
        //软件生成的应用公钥路径
        //  $c->rsaPublicKeyFilePath = $config['rsaPublicKeyFilePath'];
        //开发平台后台的支付宝rsa公钥(不是应用公钥)
        $c->alipayrsaPublicKey = $config['alipayrsaPublicKey'];
        $c->format = "json";
        $c->charset = "GBK";
        $c->version="2.0";
        return $c;
    }

}
