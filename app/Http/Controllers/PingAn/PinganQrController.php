<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/2/17
 * Time: 11:45
 */

namespace App\Http\Controllers\PingAn;


use App\Models\AlipayIsvConfig;
use App\Models\MerchantShops;
use App\Models\PinganConfig;
use App\Models\PingancqrLsits;
use App\Models\PingancqrLsitsinfo;
use App\Models\PinganStore;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;
use Comodojo\Zip\Zip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PinganQrController extends BaseController
{


    public function QrLists()
    {
        $lists = PingancqrLsits::where('user_id', Auth::user()->id)->paginate(8);
        return view('admin.pingan.qr.qrlist', compact('lists'));

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
        $cno = time();
        //生成数量
        $num = $request->get('num', 100);
        //
        try {
            PingancqrLsits::create([
                'cno' => $cno,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->name,
                'from_info' => 'pingan',
                'num' => $num,
                's_num' => 0,

            ]);
        } catch (\Exception $exception) {
            return json_encode([
                'status' => 0,
                'msg' => '插入数据库表PingancqrLsitsinfo失败'
            ]);
        }

        for ($i = 1; $i <= $num; $i++) {
            $code_number = time() . rand(1000, 9999);//编号
            $url = url('/Qrcode?code_number=' . $code_number);//生成的url准备生成二维码;
            try {
                PingancqrLsitsinfo::create([
                    'user_id' => Auth::user()->id,
                    'user_name' => Auth::user()->name,
                    'code_number' => $code_number,
                    'code_type' => 0,//空码
                    'from_info' => 'pingan',
                    'cno' => $cno
                ]);
            } catch (\Exception $exception) {
                return json_encode([
                    'status' => 0,
                    'msg' => '插入数据库表PingancqrLsitsinfo失败'
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

    //判断二维码类型
    public function Qrcode(Request $request)
    {
        $code_number = $request->get('code_number');//获得空码编号
        $pay_type = "other";
//        dd($_SERVER['HTTP_USER_AGENT']);
        //判断是不是微信
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $pay_type = 'weixin';
        }
        //判断是不是支付宝
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $pay_type = 'alipay';
        }
        //判断是不是翼支付
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Bestpay') !== false) {
            $pay_type = 'Bestpay';
        }
        //判断是不是京东
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'WalletClient') !== false||strpos($_SERVER['HTTP_USER_AGENT'], 'JDJR-App') !== false) {
            $pay_type = 'jd';
        }
        if ($pay_type == "other") {
            $errorInfo="您使用的客户端与要求不符~";
            $warningInfo="  请选择正确客户端进行扫码支付~";
            return view("admin.errorWarning",compact("errorInfo","warningInfo"));

        }
        try {
            $info = PingancqrLsitsinfo::where('code_number', $code_number)->first();
        } catch (\Exception $exception) {
            $errorInfo="二维码有误~";
            $warningInfo=" 请联系服务商重新换取新的二维码~";
            return view("admin.errorWarning",compact("errorInfo","warningInfo"));
        }
        if(!$info){
            $errorInfo="二维码有误~";
            $warningInfo=" 请联系服务商重新换取新的二维码~";
            return view("admin.errorWarning",compact("errorInfo","warningInfo"));
        }
        //空码
        if ($info->code_type == 0) {

            return redirect(url('/admin/pingan/autoStore?user_id=' . $info->user_id . '&code_number=' . $code_number));
        }
        //付款码
        if ($info->code_type == 1) {
            try {
                $store = PinganStore::where('external_id', $info->store_id)->first();
                if ($store->pay_status == 0) {
                    $errorInfo="付款码状态关闭~";
                    $warningInfo=" 请联系客服~";
                    return view("admin.errorWarning",compact("errorInfo","warningInfo"));
                }

                if (!$store->bank_card_no){
                    $errorInfo="未设置银行卡~";
                    $warningInfo=" 请商户设置收款银行卡~";
                    return view("admin.errorWarning",compact("errorInfo","warningInfo"));
                }
            } catch (\Exception $exception) {

            }

            $merchant_id=$request->get('merchant_id');
            //支付宝付款
            if ($pay_type == "alipay") {
                $external_id = $store->external_id;
                $config = PinganConfig::where('id', 1)->first()->toArray();//平安配置信息app_id
                $config = AlipayIsvConfig::where('id', 1)->first()->toArray();//支付宝配置信息app_id
                $config['app_auth_url'] = Config::get('alipayopen.app_auth_url');
                $code_url = $config['app_auth_url'] . '?app_id=' . $config['app_id'] . "&redirect_uri=" . $config['callback'] . '&scope=auth_base&state=PA_' . $external_id.'_'.$merchant_id;
                return redirect($code_url);

            }
            //微信付款
            if ($pay_type == "weixin") {
                $external_id = $store->external_id;
                $code_url = url('admin/weixin/oauth?sub_info=PPay_' . $external_id.'_'.$merchant_id);
                return redirect($code_url);

            }
            //翼支付
            if ($pay_type == "Bestpay") {
                $external_id = $store->external_id;
                $code_url=url('admin/pingan/pay_view?external_id='.$external_id.'&merchant_id='.$merchant_id);
                return redirect($code_url);
            }
            //京东
            if ($pay_type == "jd") {
                $external_id = $store->external_id;
                $code_url=url('admin/pingan/jdpay_view?external_id='.$external_id.'&merchant_id='.$merchant_id);
                return redirect($code_url);
            }
        }
    }
}