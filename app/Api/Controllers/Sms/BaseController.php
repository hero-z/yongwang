<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/4/6
 * Time: 15:07
 */

namespace App\Api\Controllers\Sms;

use App\Models\SmsConfig;
use Flc\Alidayu\Client;
use Flc\Alidayu\App;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{

    public function AopCient()
    {

        // 配置信息
        $con=SmsConfig::where('id',1)->first();
        $config = [
            'app_key' => $con->app_key,
            'app_secret' =>  $con->app_secret,
        ];

        return $client = new Client(new App($config));

    }

}