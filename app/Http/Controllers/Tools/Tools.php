<?php 
namespace App\Http\Controllers\Tools;

/*
	调用民生银行接口需要的工具
*/
class Tools
{
    // 统一生成标识
    static function makeId($mark='')
    {
        return $mark.date('YmdHis') . rand(100000, 999999);//
    }
    static function json_curl($url,$postFields = null){
        $headers = array('content-type: application/json;charset=UTF-8');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return false;
            throw new \Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                return false;
                throw new \Exception($reponse, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $result;
    }
    static function curl($url, $postFields = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $postBodyString = "";
        $encodeArray = Array();
        $postMultipart = false;
        if (is_array($postFields) && 0 < count($postFields)) {

            foreach ($postFields as $k => $v) {
                if ("@" != substr($v, 0, 1)) //判断是不是文件上传
                {

                    $postBodyString .= "$k=" . urlencode(self::characet($v, 'UTF-8')) . "&";
                    $encodeArray[$k] = self::characet($v, 'UTF-8');
                } else //文件上传用multipart/form-data，否则用www-form-urlencoded
                {
                    $postMultipart = true;
                    $encodeArray[$k] = new \CURLFile(substr($v, 1));
                }

            }
            unset ($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMultipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $encodeArray);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
            }
        }

        if ($postMultipart) {

            $headers = array('content-type: multipart/form-data;charset=' . 'UTF-8' . ';boundary=' . self::getMillisecond());
        } else {

            $headers = array('content-type: application/x-www-form-urlencoded;charset=' . 'UTF-8');
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $reponse = curl_exec($ch);
// var_dump($reponse);
        if (curl_errno($ch)) {
        	return false;
            throw new \Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
            	return false;
                throw new \Exception($reponse, $httpStatusCode);
            }
        }

        curl_close($ch);
        return $reponse;
    }

  	static  function characet($data, $targetCharset)
    {
        if (!empty($data)) {
            $fileType ="UTF-8";
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset);
                //				$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }
        return $data;
    }

    static function getMillisecond()
    {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }




    /*
    	将3层数组转为3层xml
        将键值对数组，生成xml数据

        垃圾方法
    */
    public static function overmakeXml($array){
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';

        foreach($array as $k=>$v){
    		$cc='<'.$k.'>';
    		$bb='';
	        	if(is_array($v))
	        	{
	        		$dd='';
	        		foreach($v as $kk=>$vv)
	        		{
	        			$dd.='<'.$kk.'>';
        				$ee='';

		        			if(is_array($vv))
		        			{
		        				foreach($vv as $kkk=>$vvv)
		        				{
	            					$ee.='<'.$kkk.'>'.$vvv.'</'.$kkk.'>';
		        				}
		        			}
		        			else
		        			{
				            	$ee.=$vv;
		        			}
	        			$dd.=$ee;
	        			$dd.='</'.$kk.'>';

	        		}

	        		$bb.=$dd;

	        	}
	        	else
	        	{
		            $bb.=$v;
	        	}

        	$cc.=$bb;
        	$cc.='</'.$k.'>';
        }
    	
    	$xml.=$cc;

        return $xml;
    }


    /*

        任意数组层数，返回xml格式
    */
    public static function makeXml($data)
    {
        $xml=self::_makeXml($data);
        return  '<?xml version="1.0" encoding="UTF-8" ?>'.$xml;
    }

    public static function _makeXml($data)
    {
        $str='';
        foreach($data as $k=>$v)
        {
            $str.="<{$k}>";
            if(is_array($v))
            {
                $str.=self::_makeXml($v);
            }
            else
            {
                $str.="{$v}";
            }
            $str.="</{$k}>";
        }
        return $str;

    }



/*
	解无限层xml为数组形式

*/
	static function xml_to_array( $xml )
	{
	    $reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
	    if(preg_match_all($reg, $xml, $matches))
	    {
	        $count = count($matches[0]);
	        $arr = array();
	        for($i = 0; $i < $count; $i++)
	        {
	            $key= $matches[1][$i];
	            $val = self::xml_to_array( $matches[2][$i] );  // 递归
	            if(array_key_exists($key, $arr))
	            {
	                if(is_array($arr[$key]))
	                {
	                    if(!array_key_exists(0,$arr[$key]))
	                    {
	                        $arr[$key] = array($arr[$key]);
	                    }
	                }else{
	                    $arr[$key] = array($arr[$key]);
	                }
	                $arr[$key][] = $val;
	            }else{
	                $arr[$key] = $val;
	            }
	        }
	        return $arr;
	    }else{
	        return $xml;
	    }
	}





}
