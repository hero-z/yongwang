<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2016/10/23
 * Time: 17:52
 */

//阿里isv必须配置

return [
    'app_oauth_url' => env('APP_OAUTH_URL', 'https://openauth.alipay.com/oauth2/appToAppAuth.htm'),//商户授权
    'app_auth_url'=>env('APP_AUTH_URL', 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm'),//用户授权
    'gatewayUrl'=>env('GATEWAYURL','https://openapi.alipay.com/gateway.do'),
    'aop_sdk_work_dir'=>env('AOP_SDK_WORK_DIR','/tmp/'),
    'aop_sdk_dev_mode'=>env('AOP_SDK_DEV_MODE',true),

];