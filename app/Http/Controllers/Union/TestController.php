<?php
/**
 * Date: 2017-04-25
 * Time: 11:10
 * 银联测试方法
 */
namespace App\Http\Controllers\Union;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;




use App\Common\Union\AppConfig;
use App\Common\Union\AppUtil;
use App\Common\Union\Unionpay;


class TestController extends \App\Http\Controllers\Controller
{
    static function log($data,$file='')
    {
        $file=$file ? $file : (storage_path().'/logs/union_error_log_store_alipay.txt');
        file_put_contents($file, "\n\n\n".date('Y-m-d H:i:s')."\n".var_export($data,TRUE),FILE_APPEND);
    }


  

    public function index()
    {

$data=\App\Common\AllStore::get();
var_dump($data);



return;
        $params = array();
        $params["cusid"] = AppConfig::CUSID;
        $params["appid"] = AppConfig::APPID;
        $params["version"] = AppConfig::APIVERSION;
        $params["randomstr"] = "1450432107647";//
        $params["trxamt"] = "1";
        $params["reqsn"] = "123456";//订单号,自行生成
        $params["body"] = "商品名称";
        $params["remark"] = "备注信息";
        $params["authcode"] = "6222299835291130027";//支付授权码   62
        $params["limit_pay"] = "no_credit";
        $params["sign"] = AppUtil::SignArray($params,AppConfig::APPKEY);//签名
        
        $paramsStr = AppUtil::ToUrlParams($params);

// https://vsp.allinpay.com/apiweb/unitorder/scanpay
 // https://vsp.allinpay.com/apiweb/unitorder/pay


        // $url = AppConfig::APIURL . "/pay";
 $url='https://vsp.allinpay.com/apiweb/unitorder/scanpay';

self::log($params);

        $rsp = Unionpay::request($url, $paramsStr);

self::log($rsp);

var_dump($rsp);

        $data = json_decode($rsp, true); 

        $verify=Unionpay::validSign($data);
        // 验签失败
        if($verify['status']!='1')
        {

            $message=$verify['message'];
            return;


        }
        // 验签通过



        // 交易失败
        if($data['trxcode']!='0000'||$data['trxcode']!='2008'||$data['trxcode']!='3999')
        {
            $message=isset($data['errmsg'])?$data['errmsg']:'交易失败';
        }





    }
}