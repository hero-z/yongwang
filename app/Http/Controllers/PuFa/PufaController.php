<?php
/**
 * 浦发的配置以及上传图片
 */

namespace App\Http\Controllers\PuFa;


use App\Models\PinganTradeQueries;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use App\Models\PufaStores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\PufaShopCategory;
use Illuminate\Support\Facades\Input;
use App\Models\PufaConfig;
use App\Models\PufaArea;
use App\Models\PufaBank;
use App\Models\PufaBankRelation;
class PufaController extends Controller
{
    static function log($data,$file='')
    {
        return;
        $file=$file ? $file : (storage_path().'/logs/pufa_error_log_picture_upload.txt');
        file_put_contents($file, "\n\n\n".date('Y-m-d H:i:s')."\n".var_export($data,TRUE),FILE_APPEND);
    }

    public function region(Request $request)
    {
        if($request->isMethod('post'))
        {
            $level=trim($request->get('level'));
            $pid=trim($request->get('pid'));
            $data=DB::table('pufa_region')->where('level',$level)->where('pid',$pid)->get();
            $data=json_decode(json_encode($data),true);
            if(!empty($data))
            {
                return json_encode(['status'=>'1','data'=>$data]);
            }
            else
            {
                return json_encode(['status'=>'2']);
            }
        }
    }

    /*
        商户进件资料
    */
    function jinjian()
    {
    	$this->pufabankrelation();
    }

    /*
    	获取浦发商户分类
    */
    function PFCate(Request $request)
    {

        $category_id = $request->get('category_id', '');//分类id 默认为空
       if($category_id){
           $category = PufaShopCategory::where('industry',$category_id)->first();
       }else{
           $category = PufaShopCategory::all();
       }
      return json_encode($category);
    }


    /*
        浦发银行联行号
    */
    function pufabankrelation(Request $request)
    {
        $data=[];
        try
        {
            $keyname=trim($request->get('keyname'));
            $keyname2=trim($request->get('keyname2'));
            if(empty(($keyname))){
                return ;
            }
            $data = PufaBankRelation::where('bankname','like',$keyname.'%'.$keyname2.'%')->get();
            if($data->isEmpty())
            {
                $data=[];
            }
            else
            {
                $data=$data->toArray();
            }


            // file_put_contents('./aaaaaaaaaaaaaaaaaaaaaaaaaaaaa.txt', var_export($data,true),FILE_APPEND);
        }
        catch(\Exception $e)
        {

        }
        return json_encode($data);
    }



    /*
        浦发银行代号
    */
    function pufabank()
    {
        $data = PufaBank::get()->toArray();
        return json_encode($data);
    }



    /*
        获取省
    */
    function province()
    {
        $level=2;
        $data = PufaArea::where('level', $level)->get()->toArray();
        return json_encode($data);
    }



    /*
    	获取省对应的市区

    */

	function city(Request $request)
	{
		$pid=$request->get('pid');
        // 补0 成6位
        $pid=str_pad($pid,6,'0',STR_PAD_LEFT);

    	$level=3;
        $data = PufaArea::where('level', $level)->where('pid', $pid)->get()->toArray();
        return json_encode($data);

	}
     /*
    	上传图片并传给浦发
    	入参：
    		文件--图片
    	返回：
    		状态值  1图片上传成功，2图片格式不正确，3接口调用失败，4服务商接口异常
    		浦发图片路径
    		自己服务器路径
     */
    public function uploadImagePufa(Request $request)
    {
    	set_time_limit(0);
        try
        {
            $return=[
                'status'=>3,
                'image_url'=>'',
                'pf_image_url'=>'',
                'message'=>''
            ];

            $file = Input::file('image');
            $code_number=$request->get('code_number');

            if ($file->isValid()) {
                $entension = $file->getClientOriginalExtension(); //上传文件的后缀   png  

                //可以上传的图片  浦发只能上传jpg的图片
                if(!in_array($entension, ['jpg',]))
                {
                    $return['status']=2;
                    $return['message']='图片只支持jpg格式！';
                    return json_encode($return); 
                }
                $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
                $path = $file->move(public_path() . '/uploads/'.$code_number.'/', $newName);
            }
            //图片放在服务器的磁盘绝对路径
            $picdiskpath=public_path().'/uploads/' .$code_number.'/'. $newName;
            // 上传图片的网站路径
            $picweburl=url('/uploads/' .$code_number.'/'. $newName);

            if(is_file($picdiskpath))
            {
                try
                {
                    // 判断图片尺寸大于  500kb  调函数压缩（原图将被替换）
                    $jpgsize=500*1024;
                    if(filesize($picdiskpath)>$jpgsize)
                    {
                        self::reduce($picdiskpath,$jpgsize);
                    }

                }
                catch(\Exception $e)
                {
                    @unlink($picdiskpath);
                    $return['status']=3;
                    $return['message']='系统压缩图片错误，请设置图片大小为500KB以下后重试！';
                    return json_encode($return); 
                }

            }
            else
            {
                $return['status']=3;
                $return['message']='图片上传失败，请联系服务商！';
                return json_encode($return); 
            }


        }catch(\Exception $e)
        {
self::log($e->getMessage().$e->getFile().$e->getLine());
            $return['status']=4;
            $return['message']='图片上传失败！';
            return json_encode($return); 
        }

//测试时绕过浦发接口---start
/*
$return['status']=1;
$return['message']='图片上传成功！';
$return['image_url']=$picweburl;
$return['pf_image_url']='浦发回传的地址';
return json_encode($return);
*/
//测试时绕过浦发接口---end



//上传到浦发start-------------------------------------
		        
        //获取浦发 进件资料 接口配置
        try
        {
            $pufaconfig = PufaConfig::where("id", '1')->first();
            $request_url =($pufaconfig->infourl);
            $key=($pufaconfig->security_key);
            $partner=($pufaconfig->partner);
        }
        catch(\Exception $e)
        {

self::log($e->getMessage().$e->getFile().$e->getLine());
            $return['status']=3;
            $return['message']='图片上传失败，请联系服务商！';
            return json_encode($return); 

            echo $e->getMessage();
            echo '存在数据库中的浦发进件资料配置找不到了';
            die;
        }


        try
        {

            $commondata=[
                'partner'=>$partner, 
                'serviceName'=>'pic_upload',
                'dataType'=>'xml',
                'charset'=>'UTF-8',
                'data'=>'<?xml version="1.0" encoding="UTF-8"?><picUpload><picType>1</picType></picUpload>',
                'dataSign'=>'',
            ];
            // 生成签名、生成xml数据
            $data = Tools::createjjSign($commondata, $key);

            // $filepath=public_path().'/img/p3.jpg';//磁盘绝对路径
            // 向浦发接口发送xml下单数据
            $xmlresult = Tools::curl2($request_url,$data,[$picdiskpath]);//获取银行xml数据

            if($xmlresult)
            {
                $restoarr=Tools::setContent($xmlresult);
                
                if($restoarr['isSuccess']=='T')
                {

$return['status']=1;
$return['message']='图片上传成功！';
$return['image_url']=$picweburl;
$return['pf_image_url']=$restoarr['pic'];
return json_encode($return);

                    echo '图片上传成功','<br>',$restoarr['pic'];
                    die;
                }

$return['status']=4;
$return['message']='图片上传失败：'.$restoarr['errorMsg'];
return json_encode($return);

            }

$return['status']=4;
$return['message']='图片上传失败，服务商接口异常！';
return json_encode($return);
             

        }
        catch(\Exception $e)
        {

    self::log($e->getMessage().$e->getFile().$e->getLine());
$return['status']=4;
$return['message']='图片上传失败，服务商接口异常！';
return json_encode($return); 

        }
 




        /*
            图片上传失败
array(4) {
  ["errorCode"]=>
  string(5) "S0008"
  ["errorMsg"]=>
  string(21) "图片格式不合法"
  ["isSuccess"]=>
  string(1) "F"
  ["pic"]=>
  string(0) ""
}
        
            图片上传成功
array(2) {
  ["isSuccess"]=>
  string(1) "T"
  ["pic"]=>
  string(40) "f5d33b68-cc88-4fbf-aae3-04c9650bdba6.jpg"
}
            

        */




//上传到浦发end-------------------------------------


    }







/*
将jpg图片减小至指定磁片占用大小


*/

    public static function reduce($path,$finalsize)
    {
        $percent =0.9;  //图片压缩比
        list($width, $height) = getimagesize($path); //获取原图尺寸
        //缩放尺寸
        $newwidth = $width * $percent;
        $newheight = $height * $percent;
        $src_im = imagecreatefromjpeg($path);
        $dst_im = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        imagejpeg($dst_im,$path); //输出压缩后的图片
        imagedestroy($dst_im);
        imagedestroy($src_im);
        
        clearstatcache();//清除  filesize的缓存
        $newfilesize=filesize($path);

        if($newfilesize>$finalsize)
        {
            self::reduce($path,$finalsize);
        }
        else
        {
            return true;
        }

    }





/*
    jpg图片等比例压缩，原图会被替换
    入参：
        原图磁盘路径
        生成新图宽（不允许大于原图）
        生成新图高（不允许大于原图）

*/
protected function resizeImage($path,$maxwidth='',$maxheight='')
{
    $im=imagecreatefromjpeg($path);
    $pic_width = imagesx($im);
    $pic_height = imagesy($im);
    if(($maxwidth && $pic_width > $maxwidth) && ($maxheight && $pic_height > $maxheight))
    {
        if($maxwidth && $pic_width>$maxwidth)
        {
            $widthratio = $maxwidth/$pic_width;
            $resizewidth_tag = true;
        }
        if($maxheight && $pic_height>$maxheight)
        {
            $heightratio = $maxheight/$pic_height;
            $resizeheight_tag = true;
        }
        if($resizewidth_tag && $resizeheight_tag)
        {
            if($widthratio<$heightratio)
            $ratio = $widthratio;
            else
            $ratio = $heightratio;
        }
        if($resizewidth_tag && !$resizeheight_tag)
            $ratio = $widthratio;
        if($resizeheight_tag && !$resizewidth_tag)
            $ratio = $heightratio;
        $newwidth = $pic_width * $ratio;
        $newheight = $pic_height * $ratio;
        if(function_exists("imagecopyresampled"))
        {
            $newim = imagecreatetruecolor($newwidth,$newheight);
            imagecopyresampled($newim,$im,'','','','',$newwidth,$newheight,$pic_width,$pic_height);
        }
        else
        {
            $newim = imagecreate($newwidth,$newheight);
            imagecopyresized($newim,$im,'','','','',$newwidth,$newheight,$pic_width,$pic_height);
        }
        imagejpeg($newim,$path);

        imagedestroy($newim);
    }
    else
    {
        imagejpeg($im,$path);
    } 
}


}