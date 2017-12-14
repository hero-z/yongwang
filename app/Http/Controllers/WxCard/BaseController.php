<?php
/**
 * Created by PhpStorm.
 * User: hero
 * Date: 2017/4/12
 * Time: 18:00
 */
namespace App\Http\Controllers\WxCard;

use App\Http\Controllers\Controller;
use App\Models\WeixinPayConfig;
use EasyWeChat\Foundation\Application;

class BaseController extends Controller{
    public function WxCard(){
        $config = WeixinPayConfig::where('id', 1)->first();
        $options = [
            'app_id' => $config->app_id,
            'secret' => $config->secret,
            'token' => '18851186776',
        ];
        $app = new Application($options);

        return $app;
    }
    // curl post
    public function http_post( $url, $data ) {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HEADER, 1 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
        $resp = curl_exec( $ch );
        curl_close( $ch );
        return $resp;
    }
    //http_post获取access_token
    function getAccessToken( $appid, $secret ) {
        $url = "https://api.weixin.qq.com/cgi-bin/token";
        $data = "grant_type=client_credential" . "&appid=" . urlencode( $appid ) . "&secret=" . urlencode( $secret );
        $resp = http_post( $url, $data );

        //截取token
        $token=strstr( $resp, "\":\"" );
        $token=trim( $token, "\":\"" );
        $token=strstr( $token, "\",\"", true );
        return $token;
    }
}