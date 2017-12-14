<?php
namespace App\Common\Union;

use App\Common\Union\AppConfig;
use App\Common\Union\AppUtil;

	// header("Content-type:text/html;charset=utf-8");
	// require_once 'AppConfig.php';
	// require_once 'AppUtil.php';

	//2017-05-17后推荐使用function scanpay
	function pay(){
		$params = array();
		$params["cusid"] = AppConfig::CUSID;
	    $params["appid"] = AppConfig::APPID;
	    $params["version"] = AppConfig::APIVERSION;
	    $params["trxamt"] = "1";
	    $params["reqsn"] = "123456";//订单号,自行生成
	    $params["paytype"] = "W04";
	    $params["randomstr"] = "1450432107647";//
	    $params["body"] = "商品名称";
	    $params["remark"] = "备注信息";
	    $params["authcode"] = "131431342132143214123";
		$params["acct"] = "";
	    $params["limit_pay"] = "no_credit";
        $params["notify_url"] = "";
	    $params["sign"] = AppUtil::SignArray($params,AppConfig::APPKEY);//签名
	    
	    $paramsStr = AppUtil::ToUrlParams($params);
	    $url = AppConfig::APIURL . "/pay";
	    $rsp = request($url, $paramsStr);
	    echo "请求返回:".$rsp;
	    echo "<br/>";
	    $rspArray = json_decode($rsp, true); 
	    if(validSign($rspArray)){
	    	echo "验签正确,进行业务处理";
	    }
	    
	}
	
	function scanpay(){
		$params = array();
		$params["cusid"] = AppConfig::CUSID;
	    $params["appid"] = AppConfig::APPID;
	    $params["version"] = AppConfig::APIVERSION;
	    $params["trxamt"] = "1";
	    $params["reqsn"] = "123456";//订单号,自行生成
	    $params["randomstr"] = "1450432107647";//
	    $params["body"] = "商品名称";
	    $params["remark"] = "备注信息";
	    $params["authcode"] = "131431342132143214123";		
	    $params["limit_pay"] = "no_credit";      
	    $params["sign"] = AppUtil::SignArray($params,AppConfig::APPKEY);//签名
	    
	    $paramsStr = AppUtil::ToUrlParams($params);
	    $url = AppConfig::APIURL . "/scanpay";
	    $rsp = request($url, $paramsStr);
	    echo "请求返回:".$rsp;
	    echo "<br/>";
	    $rspArray = json_decode($rsp, true); 
	    if(validSign($rspArray)){
	    	echo "验签正确,进行业务处理";
	    }
	    
	}
	
	//当天交易用撤销
	function cancel(){
		$params = array();
		$params["cusid"] = AppConfig::CUSID;
	    $params["appid"] = AppConfig::APPID;
	    $params["version"] = AppConfig::APIVERSION;
	    $params["trxamt"] = "1";
	    $params["reqsn"] = "123456788";
	    $params["oldreqsn"] = "123456";//原来订单号
	    $params["randomstr"] = "1450432107647";//
	    $params["sign"] = AppUtil::SignArray($params,AppConfig::APPKEY);//签名
	    $paramsStr = AppUtil::ToUrlParams($params);
	    $url = AppConfig::APIURL . "/cancel";
	    $rsp = request($url, $paramsStr);
		echo "请求返回:".$rsp;
	    echo "<br/>";
	    $rspArray = json_decode($rsp, true); 
	    if(validSign($rspArray)){
	    	echo "验签正确,进行业务处理";
	    }
	}
	
	//当天交易请用撤销,非当天交易才用此退货接口
	function refund(){
		$params = array();
		$params["cusid"] = AppConfig::CUSID;
	    $params["appid"] = AppConfig::APPID;
	    $params["version"] = AppConfig::APIVERSION;
	    $params["trxamt"] = "1";
	    $params["reqsn"] = "1234567889";
	    $params["oldreqsn"] = "123456";//原来订单号
	    $params["randomstr"] = "1450432107647";//
	    $params["sign"] = AppUtil::SignArray($params,AppConfig::APPKEY);//签名
	    $paramsStr = AppUtil::ToUrlParams($params);
	    $url = AppConfig::APIURL . "/refund";
	    $rsp = request($url, $paramsStr);
		echo "请求返回:".$rsp;
	    echo "<br/>";
	    $rspArray = json_decode($rsp, true); 
	    if(validSign($rspArray)){
	    	echo "验签正确,进行业务处理";
	    }
	}
	
	function query(){
		$params = array();
		$params["cusid"] = AppConfig::CUSID;
	    $params["appid"] = AppConfig::APPID;
	    $params["version"] = AppConfig::APIVERSION;
	    $params["reqsn"] = "123456";
	    $params["randomstr"] = "1450432107647";//
	    $params["sign"] = AppUtil::SignArray($params,AppConfig::APPKEY);//签名
	    $paramsStr = AppUtil::ToUrlParams($params);
	    $url = AppConfig::APIURL . "/query";
	    $rsp = request($url, $paramsStr);
		echo "请求返回:".$rsp;
	    echo "<br/>";
	    $rspArray = json_decode($rsp, true); 
	    if(validSign($rspArray)){
	    	echo "验签正确,进行业务处理";
	    }
	}
	
	//发送请求操作仅供参考,不为最佳实践
	function request($url,$params){
		$ch = curl_init();
		$this_header = array("content-type: application/x-www-form-urlencoded;charset=UTF-8");
		curl_setopt($ch,CURLOPT_HTTPHEADER,$this_header);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		 
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//如果不加验证,就设false,商户自行处理
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		 
		$output = curl_exec($ch);
		curl_close($ch);
		return  $output;
	}
	
	//验签
	function validSign($array){
		if("SUCCESS"==$array["retcode"]){
			$signRsp = strtolower($array["sign"]);
			$array["sign"] = "";
			$sign =  strtolower(AppUtil::SignArray($array, AppConfig::APPKEY));
			if($sign==$signRsp){
				return TRUE;
			}
			else {
				echo "验签失败:".$signRsp."--".$sign;
			}
		}
		else{
			echo $array["retmsg"];
		}
		
		return FALSE;
	}
	
	pay();
	//cancel();
	//refund();
	//query();
?>