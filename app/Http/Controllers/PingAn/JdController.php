<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/16
 * Time: 14:34
 */

namespace App\Http\Controllers\PingAn;


use App\Merchant;
use App\Models\PinganStore;
use App\Models\Order;
use App\Models\PinganTradeQueries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;

class JdController extends BaseController
{
    public function jdpay_view(Request $request)
    {
//        dd($request->all());
        $external_id = $request->get('external_id');
        $merchant_id = $request->get('merchant_id');//收银员
        $store = PinganStore::where('external_id', $external_id)->first();
        return view('admin.pingan.jd.jdpay_view', compact('store', 'merchant_id'));
    }

    public function jdpost(Request $request)
    {
        $remark=$request->get('remark');
        $external_id = $request->get('external_id');
        $merchant_id = $request->get('merchant_id');//收银员
        $total_amount = $request->get('total_amount');
        $aop = $this->AopClient();
        $aop->method = "fshows.liquidation.jdpay.h5pay";
        $store = PinganStore::where('external_id', $external_id)->first();
        $out_trade_no = 'pj' . date('YmdHis', time()) . rand(10000, 99999);
        $pay = [
            'sub_merchant_id' => $store->sub_merchant_id,
            'body' => $store->alias_name . '门店收款信息',
            'out_trade_no' => $out_trade_no,
            'total_fee' => $total_amount,
            'notify_url' => url('/admin/pingan/jd_notify_url')

        ];
        $data = array('content' => json_encode($pay));

        try {
            $response = $aop->execute($data);
            $responseArray = json_decode($response, true);
            //保存数据库
            if ($responseArray['success']) {
                $merchant_name = "";
                try {
                    if ($merchant_id) {
                        $merchant_name = Merchant::where('id', $merchant_id)->first()->name;//收营员名称
                    }
                } catch (\Exception $exception) {
                }
                $insert = [
                    'trade_no' => $responseArray['return_value']['trade_no'],
                    "out_trade_no" => $out_trade_no,
                    'store_id' => $store->external_id,
                    "type" => "303",
                    "merchant_id" =>(int)$merchant_id,
                    "total_amount" => $total_amount,
                    "buyer_id"=>"",
                    "status" => "",
                    "remark"=>$remark,
                    "created_at" => date('Y-m-d H:i:s', time()),
                    "updated_at" => date('Y-m-d H:i:s', time())
                ];
//                dd($insert);
               Order::create($insert);
            }

        } catch (\Exception $exception) {
            Log::info($exception);

        }
        return json_encode(['response' => $response, 'body' => $store->alias_name . '门店收款信息']);
    }

    public function acceptStatus(Request $request)
    {
        return view('admin.pingan.jd.paysuccess');
    }
}