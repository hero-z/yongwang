<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/4/14
 * Time: 17:39
 */

namespace App\Http\Controllers\UnionPay;


use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UnionPayStore;
class UnionPayController extends BaseController
{

    //下订单
    public function order(Request $request)
    {
        $m_id=$request->get('m_id');
        $total_amount = $request->get('total_amount');
        $store_id = $request->get('store_id');
        //判断是否开启收款
        if(DB::table("union_pay_stores")->where("store_id",$store_id)->first()->pay_status==0){
            return json_encode([
                'status' =>0,
                'msg' => "店铺尚未开启收款"
            ]);
        }else{
            $aop = $this->AopClient();
            $aop->method = "fshows.paycompany.liquidation.pay.unionpay.qrcode";
            $out_trade_no='u' . date('YmdHis', time()) . rand(10000, 99999);
            $pay = [
                'out_trade_no' =>$out_trade_no,
                'notify_url' => url('/admin/UnionPay/notify_url'),
                'total_amount' => $total_amount,
                'out_merchant_id' => $store_id,
            ];
            $data = array('content' => json_encode($pay));
            $response = $aop->execute($data);
            $responseArray = json_decode($response, true);
            if ($responseArray['success']) {
                $insert=[
                    'out_trade_no'=>$out_trade_no,
                    'store_id'=> $store_id,
                    'type'=>401,
                    'merchant_id'=>(int)$m_id,
                    'total_amount'=>$total_amount,
                ];
                Order::create($insert);
                return json_encode([
                    'status' => 1,
                    'code_url' => $responseArray['return_value']['qrcode_url'],
                ]);
            } else {

                return json_encode([
                    'status' => $responseArray['error_code'],
                    'msg' => $responseArray['error_message']
                ]);

            }
        }

    }
}