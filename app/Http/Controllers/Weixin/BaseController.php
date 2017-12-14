<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/1/11
 * Time: 14:57
 */

namespace App\Http\Controllers\Weixin;


use App\Http\Controllers\Controller;
use App\Models\WeixinPayConfig;

class BaseController extends Controller
{



    public function Options()
    {
        $config = WeixinPayConfig::where('id', 1)->first();
        $options = [
            'app_id' => $config->app_id,
            'payment' => [
                'merchant_id' => $config->merchant_id,
                'key' => $config->key,
                'cert_path' => $config->cert_path, // XXX: 绝对路径！！！！
                'key_path' => $config->key_path,      // XXX: 绝对路径！！！！
                'notify_url' => $config->notify_url,       // 你也可以在下单时单独设置来想覆盖它
            ],
        ];

        return $options;
    }

}