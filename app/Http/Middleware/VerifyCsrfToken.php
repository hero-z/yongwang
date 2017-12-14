<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    /**
     *从CSRF验证中排除的URL
     *
     * @var array
     */
    protected $except = [
        'admin/weixin/*',
        '/operate_notify_url',
        '/notify',
        '/notify_m',
        'alipay_notify',
        'admin/pingan/notify_url',
        'admin/pingan/wx_notify_url',
        'admin/pingan/notify_url_m',
        'admin/pingan/wx_notify_url_m',
        '/admin/pingan/best_notify_url',
        '/admin/pingan/jd_notify_url',
        '/api/pufa/notify',
        '/api/pufa/wxnotify',
        '/api/minsheng/infonotify',
        'api/minsheng/paynotify',
        'api/minsheng/wxpaynotify',
        'admin/UnionPay/notify_url',
        'merchant/wxcodeurlnotify',
        '/merchant/alicodeurlnotify',
        'admin/webank/ali_callback',
        'admin/webank/wx_callback',
        'up/notice',

    ];
}
