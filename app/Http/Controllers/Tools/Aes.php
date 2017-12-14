<?php
namespace App\Http\Controllers\Tools;
/**
 * AES128加解密类   pkcs5  打包加密
 * @author dy
 *
 */
class Aes{

    //密钥
    public $_secrect_key;

  private function __construct(){}
  public static $obj=null;
  public static function start()
  {
    if(!(self::$obj instanceof self))
      return self::$obj=new self;
    return self::$obj;
  }
  // 生成随机16位密码
  public function makeAesKey()
  {
    return substr(md5(date('Y-m-d H:i:s')),0,16);
  }
    
  public function encrypt($input) {
    $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
    $input = $this->pkcs5_pad($input, $size);
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
    $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    mcrypt_generic_init($td, $this->_secrect_key, $iv);
    $data = mcrypt_generic($td, $input);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    $data = base64_encode($data);
    return $data;
  }
 
  private function pkcs5_pad ($text, $blocksize) {
    $pad = $blocksize - (strlen($text) % $blocksize);
    return $text . str_repeat(chr($pad), $pad);
  }
 
  public function decrypt($sStr, $sKey=null) {
    $sKey=$sKey?$sKey:$this->_secrect_key;
    $decrypted= mcrypt_decrypt(
      MCRYPT_RIJNDAEL_128,
      $sKey,
      base64_decode($sStr),
      MCRYPT_MODE_ECB
    );

    $dec_s = strlen($decrypted);
    $padding = ord($decrypted[$dec_s-1]);
    $decrypted = substr($decrypted, 0, -$padding);
    return $decrypted;
  } 
}
 
 
/* 
$key = "1234567891234567";
$data = "example";

$aes=new Aes($key);

$value = $aes->encrypt($data , $key );
echo $value.'<br/>';
echo $aes->decrypt($value, $key );
 
*/