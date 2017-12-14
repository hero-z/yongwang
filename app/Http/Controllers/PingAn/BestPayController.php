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
use App\Models\PinganTradeQueries;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BestPayController extends BaseController
{

    public function pay_view(Request $request)
    {
        $external_id = $request->get('external_id');
        $merchant_id=$request->get('merchant_id');//收银员
        $store = PinganStore::where('external_id', $external_id)->first();
        return view('admin.pingan.bestpay.pay_view', compact('store','merchant_id'));

    }

    public function BestPayPost(Request $request)
    {
        $remark=$request->get("remark");
        $sub_merchant_id = $request->get('sub_merchant_id');
        $merchant_id=$request->get('merchant_id');//收银员
        $total_amount = $request->get('total_amount');
        $aop = $this->AopClient();
        $aop->method = "fshows.liquidation.bestpay.h5pay";
        $store = PinganStore::where('sub_merchant_id', $sub_merchant_id)->first();
        $pay = [
            'out_trade_no' => 'pb' . date('YmdHis', time()) . rand(10000, 99999),
            'notify_url' => url('/admin/pingan/best_notify_url'),
            'total_fee' => $total_amount,
            'body' => $store->alias_name . '门店收款信息',
            'sub_merchant_id' => $sub_merchant_id
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
                    "out_trade_no" => $pay['out_trade_no'],
                    "status" => "",
                    "merchant_id"=>(int)$merchant_id,
                    "type" => "304",
                    "total_amount" => $responseArray['return_value']['total_fee'],
                    'store_id' => $store->external_id,
                    "buyer_id"=>"",
                    "remark"=>$remark,
                     
                ];

                Order::create($insert);
            }

        } catch (\Exception $exception) {
            Log::info($exception);

        }

        return $response;
    }
    //接收返回状态
    public function acceptStatu(Request $request){
            return view("admin.pingan.bestpay.bestpaySuccess");
    }

}