<?php 
namespace App\Common\Union;
use App\Common\Union\AppConfig;
use App\Common\Union\AppUtil;

class Unionpay
{


	//发送请求操作仅供参考,不为最佳实践
	public static function request($url,$params){
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
	/*
		入参：接口返回数据数组格式
		返回：  1  验签通过
				2 验签失败
				3  通信失败

	*/
	public static function validSign($array,$key){

		if(isset($array["retcode"])&&("SUCCESS"==$array["retcode"])){
			$signRsp = strtolower($array["sign"]);
			$array["sign"] = "";
			$sign =  strtolower(AppUtil::SignArray($array, $key));
			if($sign==$signRsp){
				return ['status'=>'1','message'=>'验签通过！'];
			}
			else {
				return ['status'=>'2','message'=>'验签失败！'];
				echo "验签失败:".$signRsp."--".$sign;
			}
		}
		else{
			$msg=isset($array['retmsg'])?'通信失败：'.$array['retmsg']:'通信失败';
			return ['status'=>'3','message'=>$msg];
		}
		
	}
	


/*

	public static function pay()
	{

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

	    $rsp = self::request($url, $paramsStr);

	    $rspArray = json_decode($rsp, true); 

	    if(self::validSign($rspArray)){
	    	echo "验签正确,进行业务处理";
	    }
	    



	}


	public static function notice()
	{
		
	}


	public static function search()
	{
		
	}

*/


}












 ?>