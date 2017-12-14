<?php

namespace App\Http\Controllers\Weixin;

use App\Models\PinganConfig;
use App\Models\WeBankConfig;
use App\Models\WeixinPayConfig;
use Illuminate\Http\Request;
use EasyWeChat\Foundation\Application;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\PinganStore;


class OauthController extends BaseController
{
    //授权
    public function oauth(Request $request)
    {
        $sub_info = $request->get('sub_info');
        $arr = explode('_', $sub_info);
        if ($arr[0] == 'pay' || $arr[0] == 'PPay' || $arr[0] == 'PF' || $arr[0] == 'MS'|| $arr[0] == 'WB') {
            $options = $this->Options();//基础配置(微信支付的配置)
            //平安
            if ($arr[0] == 'PPay') {
                //查看商户是否有公众号配置
                $m_app_id=PinganStore::where('external_id',$arr[1])->first()->wx_app_id;
                 if ($m_app_id){
                     $options['app_id'] = $m_app_id;
                 }else{
                     $options['app_id'] = PinganConfig::where('id', 1)->first()->wx_app_id;
                 }
            }
            //浦发
            if ($arr[0] == 'PF') {
                $pufaconfig=DB::table('pufa_configs')->where('id','=','1')->first();

                $options['app_id'] = $pufaconfig->wx_app_id;
            }
            //民生
            if ($arr[0] == 'MS') {
                $msconfig=DB::table('ms_configs')->where('id','=','1')->first();

                $options['app_id'] = $msconfig->wx_app_id;
            }
            //微众
            if ($arr[0] == 'WB') {
                $wbconfig=WeBankConfig::where('id',1)->first();
                $options['app_id'] = $wbconfig->wx_app_id;
            }
            $config = [
                'app_id' => $options['app_id'],
                'scope' => 'snsapi_base',
                'oauth' => [
                    'scopes' => ['snsapi_base'],
                    'response_type' => 'code',
                    'callback' => url('admin/weixin/oauth_callback?sub_info=' . $sub_info),
                ],

            ];
            $app = new Application($config);
            /*  $response = $app->oauth->scopes(['snsapi_base'])
                  ->setRequest($request)
                  ->redirect();*/

            $response = $app->oauth->redirect();
//回调后获取user时也要设置$request对象
//$user = $app->oauth->setRequest($request)->user();
        }

        return $response;
    }

    public function oauth_callback(Request $request)
    {
        $sub_info = $request->get('sub_info');
        $arr = explode('_', $sub_info);
        $code = $request->input('code');
        if ($arr[0] == 'PPay') {
            //平安
            //查看商户是否有公众号配置
            $m=PinganStore::where('external_id',$arr[1])->first();
            $m_app_id=$m->wx_app_id;
            if ($m_app_id){
                $m_secert=$m->wx_secret;
                $config = [
                    'app_id' => $m_app_id,
                    "secret" => $m_secert,
                    "code" => $code,
                    "grant_type" => "authorization_code",
                ];
            }else{
                $wxConfig = PinganConfig::where('id', 1)->first();
                $config = [
                    'app_id' => $wxConfig->wx_app_id,
                    "secret" => $wxConfig->wx_secret,
                    "code" => $code,
                    "grant_type" => "authorization_code",
                ];
            }

        }
        elseif($arr[0] == 'PF')
        {
            $pufaconfig=DB::table('pufa_configs')->where('id','=','1')->first();
            //浦发
            $config = [
                'app_id' => $pufaconfig->wx_app_id,
                "secret" => $pufaconfig->wx_secret,
                "code" => $code,
                "grant_type" => "authorization_code",
            ];

        }
        elseif($arr[0] == 'MS')
        {
            $msconfig=DB::table('ms_configs')->where('id','=','1')->first();
            //浦发
            $config = [
                'app_id' => $msconfig->wx_app_id,
                "secret" => $msconfig->wx_secret,
                "code" => $code,
                "grant_type" => "authorization_code",
            ];

        }elseif($arr[0] == 'WB')
        {
            $wbconfig=WeBankConfig::where('id','=','1')->first();
            //微众
            $config = [
                'app_id' => $wbconfig->wx_app_id,
                "secret" => $wbconfig->wx_secret,
                "code" => $code,
                "grant_type" => "authorization_code",
            ];

        }else {
            $wxConfig = WeixinPayConfig::where('id', 1)->first();
            $config = [
                'app_id' => $wxConfig->app_id,
                "secret" => $wxConfig->secret,
                "code" => $code,
                "grant_type" => "authorization_code",
            ];
        }

        $app = new Application($config);
        $oauth = $app->oauth;
        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();
        $userarray = $user->toArray();
        $request->session()->forget('wx_user_data');
        $request->session()->push('wx_user_data', $userarray);

        if (count($arr) == 3) {
            $m_id = (int)$arr[2];//收银员id
        } else {
            $m_id = 0;
        }

        if ($arr[0] == 'pay') {
            //兼容旧版本
            $f = substr($arr[1], 0, 1);
            if ($f != 'w') {
                $arr[1] = 'w' . $arr[1];
            }
            //结束
            header('location:' . url("admin/weixin/orderview?store_id=" . $arr[1]. '&m_id=' . $m_id)); // 跳转到 user/profile*/
        }
        if ($arr[0] == 'PPay') {
            header('location:' . url("admin/pingan/weixin/orderview?external_id=" . $arr[1] . '&m_id=' . $m_id)); // 跳转到 user/profile*/
        }
        if ($arr[0] == 'PF') {
            header('location:' . url("api/pufa/wxform?store_id={$arr[1]}&cashier_id={$m_id}")); // 服务商处的商户id
        }
        if ($arr[0] == 'MS') {
            header('location:' . url("api/minsheng/wxform?store_id={$arr[1]}&cashier_id={$m_id}")); // 
        }
        if ($arr[0] == 'WB') {
            header('location:' . url("admin/webank/weixin/weixinPay?store_id={$arr[1]}&m_id={$m_id}")); //
        }
        //跳转到订单付款页面
        /* $_SESSION['wechat_user'] = $user->toArray();
         $targetUrl = empty($_SESSION['target_url']) ? '/' : $_SESSION['target_url'];
         header('location:' . $targetUrl); // 跳转到 user/profile*/
    }
}
