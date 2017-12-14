<?php 
namespace App\Common\PaiPai;

class Sign
{
	public static function makeSign($data)
	{
		$key='88888';

        isset($data['sign']) && $data['sign']='';
		$data=array_filter($data);

		ksort($data);
		$data['key']=$key;
		$str=http_build_query($data);
		$str=urldecode($str);
		$str=strtoupper(md5($str));

		return $str;
		$data['sign']=$str;
	}

}