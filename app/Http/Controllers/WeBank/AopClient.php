<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/1/18
 * Time: 12:34
 */

namespace App\Http\Controllers\WeBank;

use Illuminate\Support\Facades\Log;

class AopClient
{

    //应用ID
    public $appId;
    //secret
    public $secret;
    //微信配置
    public $wx_app_id;
    //secret
    public $wx_secret;
    //accesstoken
    public $access_token;
    //ticket
    public $ticket;
    //代理商编号
    public $agency_id;
    //PEM证书
    public $client_cert;
    //KEY
    public $client_key;
    //口令
    public $client_pass;
    //格式
    public $client_cert_type="PEM";
    //url头
//    public $headUrl = "https://l.test-svrapi.webank.com";
    public $headUrl = "https://svrapi.webank.com";
    //返回数据格式
    public $format = "json";
    //api版本
    public $version = "1.0.0";
    //授权类型
    public $grant_type = 'client_credential';
    //ticket类型
    public $type = 'SIGN';

    // 设置代理，为 NULL 则不使用
    const PROXY = NULL;
    //const PROXY = "119.29.195.110:8080";
    const PROXY_TYPE = NULL; // http, socks4, socks5
    // 设置代理用户名密码，为 NULL 则不使用
    const PROXY_USER = NULL;
    const PROXY_PASSWORD = NULL;
    //错误码
    const OPENAPI_SUCCESS = 0;
    const OPENAPI_PARAMS_ERROR = -1;
    const OPENAPI_NETWORK_ERROR = -2;
    const OPENAPI_INTEGRITY_ERROR = -3;
    const OPENAPI_GET_SIGN_ERROR = -4;
    //http_curl
    public static $_httpInfo = '';
    public static $_curlHandler;
    public function getUA()
    {
        return 'ymx' . $this->version;
    }
    function my_curl_reset($handler)
    {
        curl_setopt($handler, CURLOPT_URL, '');
        curl_setopt($handler, CURLOPT_HTTPHEADER, array());
        curl_setopt($handler, CURLOPT_POSTFIELDS, array());
        curl_setopt($handler, CURLOPT_TIMEOUT, 0);
        curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($handler, CURLOPT_SSLCERT, '');
        curl_setopt($handler, CURLOPT_SSLKEY, '');
        curl_setopt($handler, CURLOPT_SSLKEYPASSWD, '');
    }
    /**
     * 封装方法，用来发送请求并处理返回
     * @param array $request 请求相关信息
     * @param boolean $needCert 是否需要客户端证书
     * @return array|mixed 处理过的请求返回信息
     */
    public function sendRequest($request, $needCert = true)
    {
        if ($needCert) {
            $request = $this->setClientCert($request);
        }
        $rsp = self::send($request);
        if ($rsp === false) {
            self::logCurlInfo();
            return array(
                'code' => self::OPENAPI_NETWORK_ERROR. '|' . curl_errno(self::$_curlHandler),
                'msg' => 'network error',
            );
        }
        $info = self::info();
        $ret = json_decode($rsp, true);
        if ($ret === NULL) {
            self::logCurlInfo();
            return array(
                'code' => self::OPENAPI_NETWORK_ERROR,
                'msg' => $rsp,
                'data' => array()
            );
        }
        return $ret;
    }

    /**
     * send http request
     * @param  array $request http请求信息
     *                   url             : 请求的url地址
     *                   method          : 请求方法，'get', 'post', 'put', 'delete', 'head'
     *                   data            : 请求数据，如有设置，则method为post
     *                   header          : 需要设置的http头部
     *                   host            : 请求头部host
     *                   timeout         : 请求超时时间
     *                   cert            : ca文件路径
     *                   ssl_version     : SSL版本号
     *                   client_cert     : 客户端证书路径
     *                   client_key      : 客户端证书私钥路径
     *                   client_key_pass : 客户端证书密码
     * @return string    http请求响应
     */
    private function send($request)
    {
        if (self::$_curlHandler) {
            if (function_exists('curl_reset')) {
                curl_reset(self::$_curlHandler);
            } else {
                self::my_curl_reset(self::$_curlHandler);
            }
        } else {
            self::$_curlHandler = curl_init();
        }

//        curl_setopt(self::$_curlHandler, CURLOPT_VERBOSE, true);
        curl_setopt(self::$_curlHandler, CURLOPT_URL, $request['url']);
        switch (true) {
            case isset($request['method']) && in_array(strtolower($request['method']), array('get', 'post', 'put', 'delete', 'head')):
                $method = strtoupper($request['method']);
                break;
            case isset($request['data']):
                $method = 'POST';
                break;
            default:
                $method = 'GET';
        }

        $header = isset($request['header']) ? $request['header'] : array();
        $header[] = 'Method:' . $method;
        $header[] = 'User-Agent:' . $this->getUA();
        $header[] = 'Connection: keep-alive';

        if ('POST' == $method) {
            $header[] = 'Expect: ';
        }

        isset($request['host']) && $header[] = 'Host:' . $request['host'];
        curl_setopt(self::$_curlHandler, CURLOPT_HTTPHEADER, $header);
        curl_setopt(self::$_curlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(self::$_curlHandler, CURLOPT_CUSTOMREQUEST, $method);
        isset($request['timeout']) && curl_setopt(self::$_curlHandler, CURLOPT_TIMEOUT, $request['timeout']);

        isset($request['data']) && in_array($method, array('POST', 'PUT')) && curl_setopt(self::$_curlHandler, CURLOPT_POSTFIELDS, $request['data']);
        if (isset($request['proxy'])) {
            curl_setopt(self::$_curlHandler, CURLOPT_PROXY, $request['proxy']);
        }

        if (isset($request['proxy_auth'])) {
            curl_setopt(self::$_curlHandler, CURLOPT_PROXYUSERPWD, $request['proxy_auth']);
        }

        if (isset($request['proxy_type'])) {
            switch ($request['proxy_type']) {
                case 'http':
                    curl_setopt(self::$_curlHandler, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                    break;
                case 'socks4':
                    curl_setopt(self::$_curlHandler, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
                    break;
                case 'socks5':
                    curl_setopt(self::$_curlHandler, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                    break;
                default:
                    curl_setopt(self::$_curlHandler, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                    break;
            }
        }

        $ssl = substr($request['url'], 0, 8) == "https://" ? true : false;
        if (isset($request['cert'])) {
            curl_setopt(self::$_curlHandler, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt(self::$_curlHandler, CURLOPT_CAINFO, $request['cert']);
            curl_setopt(self::$_curlHandler, CURLOPT_SSL_VERIFYHOST, 2);
            if (isset($request['ssl_version'])) {
                curl_setopt(self::$_curlHandler, CURLOPT_SSLVERSION, $request['ssl_version']);
            } else {
                curl_setopt(self::$_curlHandler, CURLOPT_SSLVERSION, 4);
            }
        } else {
            if ($ssl) {
                curl_setopt(self::$_curlHandler, CURLOPT_SSL_VERIFYPEER, false);   //true any ca
//                curl_setopt(self::$_curlHandler, CURLOPT_SSL_VERIFYHOST, 1);       //check only host
                if (isset($request['ssl_version'])) {
                    curl_setopt(self::$_curlHandler, CURLOPT_SSLVERSION, $request['ssl_version']);
                } else {
                    curl_setopt(self::$_curlHandler, CURLOPT_SSLVERSION, 4);
                }
            }
        }

        if ($ssl && isset($request['client_cert_type'])) {
            switch ($request['client_cert_type']) {
                case 'P12':
                    if (isset($request['client_cert']) && isset($request['client_key_pass'])) {
                        curl_setopt(self::$_curlHandler, CURLOPT_SSLCERTTYPE, $request['client_cert_type']);
                        curl_setopt(self::$_curlHandler, CURLOPT_SSLCERT, $request['client_cert']);
                        curl_setopt(self::$_curlHandler, CURLOPT_SSLKEYPASSWD, $request['client_key_pass']);
                    }
                    break;
                case 'PEM':
                    if (isset($request['client_cert']) && isset($request['client_key']) && isset($request['client_key_pass'])) {
                        curl_setopt(self::$_curlHandler, CURLOPT_SSLCERTTYPE, $request['client_cert_type']);
                        curl_setopt(self::$_curlHandler, CURLOPT_SSLCERT, $request['client_cert']);
                        curl_setopt(self::$_curlHandler, CURLOPT_SSLKEY, $request['client_key']);
                        curl_setopt(self::$_curlHandler, CURLOPT_SSLKEYPASSWD, $request['client_key_pass']);
                    }
            }
        };
        $ret = curl_exec(self::$_curlHandler);
        self::$_httpInfo = curl_getinfo(self::$_curlHandler);
        return $ret;
    }

    private static function info()
    {
        return self::$_httpInfo;
    }

    /**
     * 添加证书信息到 Http Req 参数数组
     * @param $request array 构造好的 Http Req 参数数组
     * @return mixed 添加了证书信息的 Http Req 参数数组
     */
    private function setClientCert($request)
    {
       /* 忽略代理
       if (self::PROXY) {
            $request['proxy'] = self::PROXY;
            if (self::PROXY_TYPE) {
                $request['proxy_type'] = strtolower(self::PROXY_TYPE);
            }
            if (self::PROXY_USER) {
                $request['proxy_auth'] = self::PROXY_USER . ':' . self::PROXY_PASSWORD;
            }
        }*/
        $request['client_cert_type'] = $this->client_cert_type;
        $request['client_cert'] = $this->client_cert;
        $request['client_key'] = $this->client_key;
        $request['client_key_pass'] = $this->client_pass;
        return $request;
    }
    /**
     * 错误日志
     */
    private static function logCurlInfo()
    {
        Log::info('执行curl出错');
        Log::error(var_export(curl_error(self::$_curlHandler), true));
        $return_code = curl_getinfo(self::$_curlHandler, CURLINFO_HTTP_CODE);
        switch ($return_code) {
            case 400:
                Log::error("返回码为 400，可能是未带证书或证书不正确！");
                break;
            case 403:
                Log::error("返回码为 403，IP 未在白名单内！请加白名单或使用代理！");
                break;
        }
        Log::error($return_code);
        Log::error(var_export(self::info(), true));
    }
    /**
     * 生成随机字符串
     * @param int $num
     * @param string $keyspace
     * @return string
     */
    public static function getNonce($num=32,$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'){
        $str='';
        $length = strlen($keyspace)-1;
        for ($i=0;$i<$num;$i++){
            $start=rand(0,$length);
            $str.=substr($keyspace, $start,1);
        }
        return $str;
    }
    /**
     * 获取毫秒级时间戳
     * @return string
     */
    public static function getTimeStamp(){
        list($t1, $t2) = explode(' ', microtime());
        $time="".(float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
        return $time;
    }
    /**
     * 获取订单号
     * @param string $mark
     * @return string
     */
    public static function getOrderNum($mark=''){
        return $mark.date('YmdHis') . rand(100000, 999999);//
    }

    /**
     * 生成店铺ID
     * @param string $mark
     * @return string
     */
    public static function getStoreId($mark=''){
        return $mark.time() . rand(100000, 999999);//
    }

    /**
     * 签名
     * @param $params
     * @return bool|string
     */
    public function getSign($params)
    {
        $ticket = $this->ticket;
        if (!$ticket) {
            Log::error("Ticket is empty!");
            echo "Ticket 为空！无法计算签名！\n";
            return false;
        }
        array_push($params, $ticket);
        sort($params);
        $data_string = implode($params);
        return sha1($data_string);
    }
}