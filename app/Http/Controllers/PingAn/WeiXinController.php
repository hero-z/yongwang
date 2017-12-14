<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/2/21
 * Time: 10:56
 */

namespace App\Http\Controllers\PingAn;


use App\Merchant;
use App\Models\PinganStore;
use App\Models\PinganTradeQueries;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class WeiXinController extends BaseController
{

    //订单金额页面
    public function orderview(Request $request)
    {
        $external_id = $request->get('external_id');//子商户号
        $m_id = $request->get('m_id');
        try {
            $shop = PinganStore::where('external_id', $external_id)->first();
        } catch (\Exception $exception) {
            Log::info($exception);
        }
        return view('admin.pingan.weixin.orderview', compact('shop', 'm_id'));
    }

    //提交过来的平安订单
    public function PAWxOrder(Request $request)
    {
        $wx_user_data = $request->session()->get('wx_user_data');
        $aop = $this->AopClient();
        $aop->method = "fshows.liquidation.wxpay.mppay";
        $out_trade_no = 'pw' . date('YmdHis', time()) . rand(10000, 99999);
        $remark=$request->get("remark");
        $store = PinganStore::where('external_id', $request->get('external_id'))->first();
        $pay = [
            "sub_merchant_id" => $store->sub_merchant_id,
            "body" => $store->alias_name . "收款",
            "out_trade_no" => $out_trade_no,
            "total_fee" => $request->get('total_fee'),
            "sub_openid" => $wx_user_data[0]['id'],
            "spbill_create_ip" => \EasyWeChat\Payment\get_client_ip(),
            "notify_url" => url('/admin/pingan/wx_notify_url')
        ];
        $data = array('content' => json_encode($pay));
        try {
            $response = $aop->execute($data);
            $responseArray = json_decode($response, true);
            //保存数据库
            if ($responseArray['success']) {
                $merchant_name = "";
                try {
                    if ($request->get('m_id')) {
                        $merchant_name = Merchant::where('id', $request->get('m_id'))->first()->name;//收营员名称
                    }
                } catch (\Exception $exception) {
                }

                $insert = [
                    'trade_no' => '',
                    "out_trade_no" => $out_trade_no,
                    "status" => "",
                    "type" => "302",
                    'merchant_id' => (int)$request->get('m_id'),
                    "buyer_id"=>$wx_user_data[0]['id'],
                    "total_amount" => $request->get('total_fee'),
                    'store_id' => $store->external_id,
                    "remark"=>$remark
                ];
                Order::create($insert);
            }
        } catch (\Exception $exception) {
            Log::info($exception);

        }
        return $response;
    }

    //同步状态
    public function OrderStatus(Request $request)
    {
        return view("admin.pingan.weixin.paysuccess");
    }

}