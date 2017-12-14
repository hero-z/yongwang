<?php
/**
 * Date: 2017-04-25
 * Time: 11:10
 * 银联测试方法
 */
namespace App\Http\Controllers\Union;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



 

class NoticeController extends \App\Http\Controllers\Controller
{



    static function log($data,$file='')
    {
        $file=$file ? storage_path().'/logs/'.$file : (storage_path().'/logs/union_pay.txt');
        file_put_contents($file, "\n\n\n".date('Y-m-d H:i:s')."\n".var_export($data,TRUE),FILE_APPEND);
    }






    public function index(Request $request)
    {
    	self::log($request->all());die;


echo __FILE__;die;

return view('union.test');



    }
}