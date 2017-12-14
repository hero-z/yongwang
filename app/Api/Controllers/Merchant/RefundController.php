<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2017/7/29
 * Time: 下午4:48
 */

namespace App\Api\Controllers\Merchant;


use App\Models\Order;
use Illuminate\Http\Request;

class RefundController extends BaseController
{

    public function payRefund(Request $request)
    {
        try {
            $out_trade_no = $request->get('out_trade_no');
            $trade_no = $request->get('trade_no');
            $refund_amount = $request->get('refund_amount');
            $refund_reason = $request->get('refund_reason');
            $out_request_no = $request->get('out_request_no', time());
            if (!$trade_no) {
                return json_encode([
                    'status' => 0,
                    'msg' => '参数2不正确'
                ]);
            }

            if (!$refund_amount) {
                return json_encode([
                    'status' => 0,
                    'msg' => '参数不正确'
                ]);
            }
            if ($out_trade_no && $refund_amount) {
                $order = Order::where('out_trade_no', $out_trade_no)->first();
            }

            if ($trade_no && $refund_amount) {
                $order = Order::where('trade_no', $trade_no)->first();
            }
            if ($order) {


                //平安银行
                if (in_array($order->type, [301, 302, 303, 304, 305, 306, 307, 308, 309])) {
                    $ao = new \App\Http\Controllers\PingAn\BaseController();
                    $aop = $ao->AopClient();
                    $aop->method = "fshows.liquidation.pay.refund";
                    $pay = [
                        'out_trade_no' => $order->out_trade_no,
                        'out_refund_no' => $out_request_no,

                    ];
                    $dataAop = array('content' => json_encode($pay));
                    try {
                        $response = $aop->execute($dataAop);
                        $responseArray = json_decode($response, true);
                      if ($responseArray['success']){
                          //退款请求成功  更新状态 再加一个字段 退款金额

                          //少代码

                          return json_encode([
                               'status' => 1,
                               'data' => $responseArray['return_value'],
                          ]);

                      }else{
                          return json_encode([
                              'status' => $responseArray['error_code'],
                              'msg' => $responseArray['error_message'],
                          ]);
                      }

                    } catch (\Exception $exception) {
                        return json_encode([
                            'status' => 0,
                            'msg' => $exception->getMessage(),
                        ]);
                    }
                }







                return json_encode([
                    'status' => 0,
                    'msg' => '此订单暂时不支持退款'
                ]);

            } else {
                return json_encode([
                    'status' => 0,
                    'msg' => '订单号不存在'
                ]);
            }


        } catch (\Exception $exception) {
            return json_encode([
                'status' => 0,
                'msg' => $exception->getMessage()
            ]);
        }


    }

}