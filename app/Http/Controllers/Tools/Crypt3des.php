<?php
namespace App\Http\Controllers\Tools;
/**
*
* PHP版3DES加解密类
*
* 可与java的3DES(DESede)加密方式兼容
*
*/
class Crypt3des
{


    private function __construct()
    {
    }

    public static $obj=null;

    public static function start($des3_key)
    {

        if(is_null(self::$obj))
        {
            $obj=self::$obj=new self();
            $obj->key=$des3_key;
        }
        return self::$obj;
    }

    public $key = "XXXXXXXXXXXXXXXXX";//这个根据实际情况写

    function encrypt($input){//数据加密
        $size = mcrypt_get_block_size(MCRYPT_3DES,'ecb');
        $input = $this->pkcs5_pad($input, $size);
        $key = str_pad($this->key,24,'0');
        $td = mcrypt_module_open(MCRYPT_3DES, '', 'ecb', '');
        $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        //    $data = base64_encode($this->PaddingPKCS7($data));
        $data = base64_encode($data);
        return $data;
    }

    function decrypt($encrypted,$key=null){//数据解密
        $encrypted = base64_decode($encrypted);
        $key=$key?$key:($this->key);
echo '<hr>';        
echo $key;
        $key = str_pad($key,24,'0');
        $td = mcrypt_module_open(MCRYPT_3DES,'','ecb','');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_RAND);
        $ks = mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $encrypted);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $y=$this->pkcs5_unpad($decrypted);
        return $y;
    }
    function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    function pkcs5_unpad($text){
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text)) {
        return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad){
        return false;
        }
        return substr($text, 0, -1 * $pad);
    }

    function PaddingPKCS7($data) {
        $block_size = mcrypt_get_block_size(MCRYPT_3DES, MCRYPT_MODE_CBC);
        $padding_char = $block_size - (strlen($data) % $block_size);
        $data .= str_repeat(chr($padding_char),$padding_char);
        return $data;
    }
}
/*
$rep=new Crypt3des('jiamichuan');//初始化一个对象
$input="hello world";
echo "原文：".$input."<br/>";
$encrypt_card=$rep->encrypt($input);
echo "加密：".$encrypt_card."<br/>";
echo "解密：".$rep->decrypt($rep->encrypt($input));

*/