<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2017/7/28
 * Time: 上午11:57
 */

namespace App\Api\Controllers\Merchant;


use App\Models\MerchantShops;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewLandController extends BaseController
{


    public function newlandCreateOrder(Request $request){
        try{
            $user = $this->getMerchantInfo();
            $total_amount=$request->get('total_amount');
            $out_trade_no=$request->get('out_trade_no');
            $trade_no=$request->get('trade_no');
            $pay_status=$request->get('pay_status');

            if ($total_amount&&$out_trade_no&&$pay_status){
                $data = $request->all();
                $rules = [
                    'out_trade_no' => 'required|unique:orders',
                ];
                $validator = Validator::make($data, $rules);
                if ($validator->fails()) {
                    return json_encode([
                        'status' => 0,
                        'msg' => '订单号已存在'
                    ]);
                }
                $store = MerchantShops::where('merchant_id', $user['id'])->where('store_type', 'newland')->first();
                $data=[
                    'out_trade_no'=>$out_trade_no,
                    'trade_no'=>$trade_no,
                    'store_id'=>$store->store_id,
                    'merchant_id'=>$user['id'],
                    'type'=>1001,
                    'total_amount'=>$total_amount,
                    'pay_status'=>$pay_status
                ];
                Order::create($data);
                return json_encode([
                    'status' => 1,
                    'msg' => '订单入库成功'
                ]);

            }else{
                return json_encode([
                    'status' => 0,
                    'msg' => '缺少参数'
                ]);
            }
        }catch (\Exception $exception){
            return json_encode([
                'status' => 0,
                'msg' => $exception->getMessage().$exception->getLine()
            ]);
        }

    }
}