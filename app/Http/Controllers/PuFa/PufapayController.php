<?php
/**
 * 浦发银行 支付宝、微信二码合一；
 * 以及后台批量生成空码
 */

namespace App\Http\Controllers\PuFa;


use App\Models\AlipayIsvConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\PufaStores;
use App\Http\Controllers\Controller;

use App\Models\PufacqrLsitsinfo;
use App\Models\PufacqrLsits;

use Illuminate\Support\Facades\Auth;
use Comodojo\Zip\Zip;

use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\DB;

class PufapayController extends Controller
{

    //判断支付方式并拼接授权地址---多码合一
    public function payway(Request $request)
    {

        $code_number = $request->get('code_number');//获得空码编号
        $cashier_id=$request->get('cashier_id','');
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
/*
        //判断是不是翼支付
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Bestpay') !== false) {
            $pay_type = 'Bestpay';
        }
        //判断是不是京东
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'WalletClient') !== false) {
            $pay_type = 'jd';
        }
 */       
        if ($pay_type == "other") {
            echo '请用支付宝或者微信扫描二维码';
        }



        try {
            $info = PufacqrLsitsinfo::where('code_number', $code_number)->first();
        } catch (\Exception $exception) {
            return '二维码有误！请联系服务商重新换取新的二维码！';
        }
        //空码  给商户注册门店资料
        if ($info->code_type == 0) {

            return redirect(url('/api/pufa/autoStore?user_id=' . $info->user_id . '&code_number=' . $code_number));
        }



        //付款码   判断有无store_id分店标识
        if ($info->code_type == 1) {
            try {

                // 只能用总店的id去查支付类型是否开通
                $paytype = DB::table("pufa_pay_road")->where("store_id", $info->store_id)->where('status','2')->get();
                $zhifubao=false;
                $weixin=false;
                foreach($paytype as $k=>$v)
                {
                    if($v->code=='pay.alipay.jspayv3')
                    {
                        $zhifubao=true;
                    }
                    if($v->code=='pay.weixin.jspay')
                    {
                        $weixin=true;
                        
                    }
                }


                $store_id = $request->get('store_id');//分店标识

                // 分店信息---后台生成的分店标识带store_id
                if($store_id)
                {
                    $store = PufaStores::where('store_id', $store_id)->first();
                }
                // 扫二维码进件的地址-----总店支付
                else
                {
                    // 总店付款码状态
                    $store = PufaStores::where('store_id', $info->store_id)->first();    
                }

                if ($store->pay_status == 1) {
                    echo '付款码状态关闭！请联系客服！';die;
                    dd();
                }

            } catch (\Exception $exception) {

            }
            //支付宝付款
            if ($pay_type == "alipay") {
                $store_id = $store->store_id;//浦发商户id
                
                // 判断商户是否开通支付宝支付方式
                if(!$zhifubao)
                {
                    echo '商家没有开启支付宝收款，请联系服务商！';die;
                }

                $config = AlipayIsvConfig::where('id', 1)->first()->toArray();//支付宝配置信息app_id
                $config['app_auth_url'] = Config::get('alipayopen.app_auth_url');
                $code_url = $config['app_auth_url'] . '?app_id=' . $config['app_id'] . "&redirect_uri=" . $config['callback'] . '&scope=auth_base&state=PF_' . $store_id.'_'.$cashier_id;
                // echo $code_url;die;
                return redirect($code_url);

            }
            //微信付款
            if ($pay_type == "weixin") {
                // 判断商户是否开通支付宝支付方式
                if(!$weixin)
                {
                    echo '商家没有开启微信收款，请联系服务商！';die;
                }
                $store_id = $store->store_id;//浦发商户id
                $code_url = url("admin/weixin/oauth?sub_info=PF_{$store_id}_{$cashier_id}");
                return redirect($code_url);
            }
/*
            //翼支付
            if ($pay_type == "Bestpay") {
                $sub_merchant_id = $store->sub_merchant_id;//商户id
                $code_url=url('admin/pingan/pay_view?sub_merchant_id='.$sub_merchant_id);
                return redirect($code_url);
            }
            //京东
            if ($pay_type == "jd") {
                $sub_merchant_id = $store->sub_merchant_id;//商户id
                $code_url=url('admin/pingan/jdpay_view?sub_merchant_id='.$sub_merchant_id);
                return redirect($code_url);
            }
*/
        }

    }

    /*
        为商户批量生成空码
    */
    public function QrLists()
    {
        $auth = Auth::user()->can('pufaCode');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $lists = PufacqrLsits::where('user_id', Auth::user()->id)->paginate(8);

        // echo '<pre>'; print_r($lists->toArray());die;
        return view('pufa.qr.qrlist', compact('lists'));

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
            PufacqrLsits::create([
                'cno' => $cno,
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->name,
                'from_info' => 'pufa',
                'num' => $num,
                's_num' => 0,

            ]);
        } catch (\Exception $exception) {
            return json_encode([
                'status' => 0,
                'msg' => '插入数据库表PufacqrLsitsinfo失败'
            ]);
        }

        for ($i = 1; $i <= $num; $i++) {
            $code_number = 'f'.time() . rand(1000, 9999);//编号
            $url = url('/api/pufa/payway?code_number=' . $code_number);//生成的url准备生成二维码;
            try {
                PufacqrLsitsinfo::create([
                    'user_id' => Auth::user()->id,
                    'user_name' => Auth::user()->name,
                    'code_number' => $code_number,
                    'code_type' => 0,//空码
                    'from_info' => 'pufa',
                    'cno' => $cno
                ]);
            } catch (\Exception $exception) {
                return json_encode([
                    'status' => 0,
                    'msg' => '插入数据库表PufacqrLsitsinfo失败'
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