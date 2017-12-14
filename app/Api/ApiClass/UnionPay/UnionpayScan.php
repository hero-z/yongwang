<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2017/6/24
 * Time: 上午11:37
 */

namespace App\Api\ApiClass\UnionPay;


use App\Http\Controllers\UnionPay\AopClient;
use App\Http\Controllers\UnionPay\BaseController;
use App\Models\Order;
use Illuminate\Http\Request;

class UnionpayScan
{
    //银联被扫接口
    public function scanSend($auth_code, $total_amount, $store_id, $m_id)
    {
        if ($auth_code && $total_amount && $store_id && $m_id) {
            try {
                $ao = new BaseController();
                $aop = $ao->AopClient();
                $aop->method = "fshows.paycompany.liquidation.pay.unionpay.scan";
                $out_trade_no = 'u' . date('YmdHis', time()) . rand(10000, 99999);
                $pay = [
                    'out_trade_no' => $out_trade_no,
                    'notify_url' => url('/admin/UnionPay/notify_url'),
                    'total_amount' => $total_amount,
                    'out_merchant_id' => $store_id,
                    'auth_code' => $auth_code
                ];
                $data = array('content' => json_encode($pay));
                $response = $aop->execute($data);
                $responseArray = json_decode($response, true);
                if ($responseArray['success']) {
                    $insert = [
                        'out_trade_no' => $out_trade_no,
                        'store_id' => $store_id,
                        'type' => 402,
                        'merchant_id' => (int)$m_id,
                        'total_amount' => $total_amount,
                    ];
                    Order::create($insert);
                    sleep(10);
                   dd($this->selectOrder($out_trade_no));

                } else {
                    return json_encode([
                        'status' => $responseArray['error_code'],
                        'msg' => $responseArray['error_message']
                    ]);

                }

            } catch (\Exception $exception) {
                return json_encode([
                    'status' => $exception->getCode(),
                    'msg' => $exception->getMessage(),
                ]);
            }
        } else {
            return json_encode([
                'status' => 0,
                'msg' => '参数不完整！请检测参数是否正确'
            ]);
        }
    }

//查询订单
    public function selectOrder($out_trade_no)
    {
        $ao = new BaseController();
        $aop = $ao->AopClient();
        $aop->method = "fshows.paycompany.liquidation.order.query";
        $pay = [
            'out_trade_no' => $out_trade_no,
        ];
        $data = array('content' => json_encode($pay));
        $response = $aop->execute($data);
        return $response;
    }

}