<?php 
namespace App\Http\Controllers\Tools;

/*
	调用民生银行接口需要的工具

*/
class RSA{  
	private function __construct(){}
	public static $obj=null;
	public static function start()
	{
		if(!(self::$obj instanceof self))
			return self::$obj=new self;
		return self::$obj;
	}

	//我方公钥

	public $self_public_key;

	//我方私钥
	public $self_private_key;
	
	//对方公钥
	public $third_public_key;

// 将一行字符串转为多行的rsa公钥
	public function  matePubKey($publicKey)
	{
		return  '-----BEGIN PUBLIC KEY-----'."\n".wordwrap($publicKey, 64, "\n", true)."\n".'-----END PUBLIC KEY-----';
	}

// 将一行字符串转为多行的rsa私钥
	public function matePriKey($privateKey)
	{
		return   "-----BEGIN RSA PRIVATE KEY-----\n" .
				wordwrap($privateKey, 64, "\n", true) .
				"\n-----END RSA PRIVATE KEY-----";
	}



/**
 * 我方加签(使用我方的私钥)
 * @param  需要加签的数据
 * @return  返回加签完成后的字串
 */
public function sign($data, $code = 'base64'){  
    $ret = false;  
    if (openssl_sign($data, $ret, $this->self_private_key)){  
        $ret = $this->_encode($ret, $code);  
    }  
    return $ret;  
}  
/**
 * 我方验签(使用对方公钥)
 * @param  $data为传输的数据
 * @param  传输的加签数据
 * @return  返回验签的结果   true或者false
 */ 
public function checksign($data, $sign, $code = 'base64'){  
    $ret = false;      
    $sign = $this->_decode($sign, $code);  
    if ($sign !== false) {  
        switch (openssl_verify($data, $sign, $this->third_public_key)){  
            case 1: $ret = true; break;      
            case 0:      
            case -1:       
            default: $ret = false;       
        }  
    }  
    return $ret;  
}  

				//签名编码
				    private function _encode($data, $code){  
				        switch (strtolower($code)){  
				            case 'base64':  
				                $data = base64_encode(''.$data);  
				                break;  
				            case 'hex':  
				                $data = bin2hex($data);  
				                break;  
				            case 'bin':  
				            default:  
				        }  
				        return $data;  
				    }  
				//解签编码
				    private function _decode($data, $code){  
				        switch (strtolower($code)){  
				            case 'base64':  
				                $data = base64_decode($data);  
				                break;  
				            case 'hex':  
				                $data = $this->_hex2bin($data);  
				                break;  
				            case 'bin':  
				            default:  
				        }  
				        return $data;  
				    }  
				  

				 private function _hex2bin($hex = false){  
				        $ret = $hex !== false && preg_match('/^[0-9a-fA-F]+$/i', $hex) ? pack("H*", $hex) : false;      
				        return $ret;  
				    } 


/**
 * 我方加密(使用对方公钥)  
 * @param  $data  要加密的数据
 * @param  $code  密文编码
 * @param  $padding  填充方式（貌似php有bug，所以目前仅支持OPENSSL_PKCS1_PADDING）
 * @return  加密后的字串
 */
public function encrypt($data, $code = 'base64', $padding = OPENSSL_PKCS1_PADDING){  
    $ret = false;      
    if (!$this->_checkPadding($padding, 'en')) return false;  
    if (openssl_public_encrypt($data, $result, $this->third_public_key, $padding)){  
        $ret = $this->_encode($result, $code);  
    }  
    return $ret;  
} 

public function encrypt34($data, $code = 'base64', $padding = OPENSSL_PKCS1_PADDING){  
    $ret = false;      
    if (!$this->_checkPadding($padding, 'en')) return false;  
    if (openssl_public_encrypt($data, $result, $this->third_public_key, $padding)){  
        $ret = $this->_encode($result, $code);  
    }  
    return $ret;  
} 



/**
 * 我方解密(使用我方私钥)
 * @param  $data  要解密的数据
 * @param  $code  密文编码
 * @param  $padding  填充方式（貌似php有bug，所以目前仅支持OPENSSL_PKCS1_PADDING）
 * @return $rev 明文 
 * @return
 */
public function decrypt($data, $code = 'base64', $padding = OPENSSL_PKCS1_PADDING, $rev = false){  
    $ret = false;  
    $data = $this->_decode($data, $code);  
    if (!$this->_checkPadding($padding, 'de')) die('rsa加密算法出现问题！');  
    if ($data !== false){  
        if (openssl_private_decrypt($data, $result, $this->self_private_key, $padding)){  
            $ret = $rev ? rtrim(strrev($result), "\0") : ''.$result;  
        }   
    }  
    return $ret;  
}  



			    /** 
			     * 检测填充类型 
			     * 加密只支持PKCS1_PADDING 
			     * 解密支持PKCS1_PADDING和NO_PADDING 
			     *  
			     * @param int 填充模式 
			     * @param string 加密en/解密de 
			     * @return bool 
			     */  
			    private function _checkPadding($padding, $type){  
			        if ($type == 'en'){  
			            switch ($padding){  
			                case OPENSSL_PKCS1_PADDING:  
			                    $ret = true;  
			                    break;  
			                default:  
			                    $ret = false;  
			            }  
			        } else {  
			            switch ($padding){  
			                case OPENSSL_PKCS1_PADDING:  
			                case OPENSSL_NO_PADDING:  
			                    $ret = true;  
			                    break;  
			                default:  
			                    $ret = false;  
			            }  
			        }  
			        return $ret;  
			    }  
			  
}

/*
使用实例

$r = new RSA;
$begin='我是原始数据';
echo '<br/>原始数据：'.$begin;
//使用我方私钥加签
$signdata=$r->sign($begin);
echo "<br/><br/><br/>加签后：{$signdata}";
//验签  使用传输数据以及传输数据中的签名来验签---使用对方公钥
$signresult=$r->checksign($begin,$signdata);
// var_dump($signresult);
echo "<br/>验签结果：";
var_dump($signresult);

 
//加密 使用对方公钥加密
$miwen=$r->encrypt($begin);
echo "<br>密文是：{$miwen}";
//解密  使用我方私钥解密
$yuan=$r->decrypt($miwen);
echo "<br/>解密后是：{$yuan}";

 
*/
