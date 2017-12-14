<?php

/*
	民生银行二维码表

CREATE TABLE `mscqr_lsits` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `cno` varchar(255) DEFAULT NULL,
 `user_id` int(11) DEFAULT NULL,
 `user_name` varchar(50) DEFAULT NULL,
 `from_info` varchar(50) DEFAULT NULL,
 `num` int(11) DEFAULT NULL,
 `s_num` int(11) DEFAULT NULL,
 `created_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `mscqr_lsitsinfos` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) DEFAULT NULL,
 `user_name` varchar(255) DEFAULT NULL,
 `code_number` varchar(255) DEFAULT NULL,
 `code_type` int(11) DEFAULT NULL,
 `store_id` varchar(50) DEFAULT NULL,
 `store_name` varchar(50) DEFAULT NULL,
 `from_info` varchar(50) DEFAULT NULL,
 `cno` varchar(255) DEFAULT NULL,
 `created_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

*/
namespace App\Http\Controllers\MinSheng;

use App\Http\Controllers\Controller;

use App\Http\Controllers\MinSheng\MinSheng;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use Illuminate\Support\Facades\Auth;
use Comodojo\Zip\Zip;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;


use App\Http\Controllers\Tools\Tools;

class QrController extends Controller
{ 

    /*
        为商户批量生成空码
    */
    public function QrLists()
    {
        // $lists = PufacqrLsits::where('user_id', Auth::user()->id)->paginate(8);
        $lists = DB::table('mscqr_lsits')->where('user_id', Auth::user()->id)->paginate(8);
        return view('minsheng.qr.qrlist', compact('lists'));
    }

    /*
        下载二维码
    */
    public function DownloadQr(Request $request)
    {
        $cno = $request->get('cno');
        try {
            $zip = Zip::create($cno . '.zip');;
            $zip->add(public_path() . '/QrCode/' . $cno . '/', true);
        } catch (\Exception $exception) {
            return '打包失败';
        }
        return redirect(url('/' . $cno . '.zip'));
    }


    /*
        生成二维码图片到服务器
    */
    public function createQr(Request $request)
    {
        //生成的批次
        $cno = time();
        //生成数量
        $num = $request->get('num', 100);
        //
        try {
        	$data=[
                'cno' => $cno,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->name,
                'from_info' => 'minsheng',
                'num' => $num,
                's_num' => 0,
                    'created_at'=>date('Y-m-d H:i:s')

            ];
			$insert=DB::table("mscqr_lsits")->insert($data);
            // PufacqrLsits::create();
        } catch (\Exception $exception) {
            return json_encode([
                'status' => 0,
                'msg' => '民生二维码生成失败，请重试！'
            ]);
        }

        for ($i = 1; $i <= $num; $i++) {
    		$code_number=Tools::makeId();
            // $code_number = 'f'.time() . rand(1000, 9999);//编号
            $url = url('/api/minsheng/payway?code_number=' . $code_number);//生成的url准备生成二维码;
            try {
            	$data=[
                    'cno' => $cno,
                    'user_id' => Auth::user()->id,
                    'user_name' => Auth::user()->name,
                    'from_info' => 'minsheng',
                    'code_number' => $code_number,
                    'code_type' => 0,//空码
                    'created_at'=>date('Y-m-d H:i:s')
                ];
				$insert=DB::table("mscqr_lsitsinfos")->insert($data);
                // PufacqrLsitsinfo::create();
            } catch (\Exception $exception) {
                return json_encode([
                    'status' => 0,
                    'msg' => '民生二维码生成失败，请重试！'
                ]);
            }
            try {
                //生成二维码文件
                if (!is_dir(public_path('QrCode/' . $cno . '/'))) {
                    mkdir(public_path('QrCode/' . $cno . '/'), 0777);
                }
                $renderer = new Png();
                $renderer->setHeight(500);
                $renderer->setWidth(500);
                $writer = new Writer($renderer);
                $writer->writeFile($url, public_path('QrCode/' . $cno . '/' . $code_number . '.png'));
            } catch (\Exception $exception) {
                return json_encode([
                    'status' => 0,
                    'msg' => '生成二维码失败！请检测文件权限！'
                ]);

            }
        }
        return json_encode([
            'status' => 1,
            'msg' => '生成二维码成功！'
        ]);
    }






}