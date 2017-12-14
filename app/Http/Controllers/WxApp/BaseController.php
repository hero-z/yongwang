<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/10
 * Time: 18:58
 */

namespace App\Http\Controllers\WxApp;


use App\Http\Controllers\Controller;
use App\Models\WechatMenuConfig;
use App\Models\WeixinPayConfig;
use EasyWeChat\Foundation\Application;

class BaseController extends Controller
{

    public function WxApp()
    {
        $config= WechatMenuConfig::where('id',1)->first();
        $options = [
            'app_id' => $config->app_id,
            'secret' => $config->secret,
            'token' => $config->token,
//            'token' => '18851186776',
        ];
        $app = new Application($options);

        return $app;
    }



}