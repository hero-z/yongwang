<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/2/17
 * Time: 11:45
 */

namespace App\Http\Controllers\Qr;


use App\Http\Controllers\Controller;
use App\Models\QrList;
use App\Models\QrListInfo;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;
use Comodojo\Zip\Zip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QrController extends Controller
{

  //生成列表
    public function QrLists()
    {
        $lists= DB::table('qr_lists')
            ->join('users', 'qr_lists.user_id', '=', 'users.id')
            ->where('user_id', Auth::user()->id)
            ->select('qr_lists.*', 'users.name')
            ->orderBy('created_at','desc')
            ->paginate(8);
        return view('admin.qr.qrlist', compact('lists'));

    }

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

    public function createQr(Request $request)
    {
        //生成的批次
        $cno = date('Ymdhis',time()).rand(1000,9999);
        //生成数量
        $num = 100;
        //
        try {
            QrList::create([
                'cno' => $cno,
                'user_id' => Auth::user()->id,
                'num' => $num,
                's_num' => 0,
            ]);
        } catch (\Exception $exception) {
            dd($exception);
            return json_encode([
                'status' => 0,
                'msg' => '插入数据库表QrList失败'
            ]);
        }

        for ($i = 1; $i <= $num; $i++) {
            $code_number = date('Ymdhis',time()). rand(100000, 999999);//编号
            $url = url('/Qrcode?code_number=' . $code_number);//生成的url准备生成二维码;
            try {
                QrListInfo::create([
                    'user_id' => Auth::user()->id,
                    'code_number' => $code_number,
                    'code_type' => 0,//空码
                    'cno' => $cno
                ]);
            } catch (\Exception $exception) {
                dd($exception);
                return json_encode([
                    'status' => 0,
                    'msg' => '插入数据库表QrListInfo失败'
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
                    'msg' => '生成二维码失败！请检测文件权限'
                ]);

            }
        }
        return json_encode([
            'status' => 1,
            'msg' => '生成二维码成功'
        ]);
    }
}