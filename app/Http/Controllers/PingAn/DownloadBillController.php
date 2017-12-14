<?php
/**
 * Created by PhpStorm.
 * User: hero
 * Date: 2017/5/24
 * Time: 14:08
 */

namespace App\Http\Controllers\PingAn;


use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

class DownloadBillController  extends   BaseController
{
    public function downloadbill(){
        return view('admin.pingan.store.downloadbill');
    }
    public function downloadbillpost(Request $request){
        $bill_date=date('Ymd',strtotime($request->bill_date));
        $pay_platform=$request->pay_platform;
        $file_type=$request->file_type;
        $aop = $this->AopClient();
        $aop->method = "fshows.liquidation.finance.downloadbill";
        if($pay_platform=='5'){
            $file_type=2;
        }
//        $pay_platform=5;
//        $file_type=2;
        $content = [
            'bill_date' => $bill_date,
            'pay_platform' => $pay_platform,
            'file_type' => $file_type,
        ];
        $data = array('content' => json_encode($content));
        try {
            $response = $aop->execute($data);
            $responseArray = json_decode($response, true);
            //保存数据库
            if ($responseArray['success']) {
                return json_encode([
                    'success'=>1,
                    'download_url'=>$responseArray['return_value']['download_url']
                ]);
            }else{
                return json_encode([
                    'success'=>0,
                    'msg'=>$responseArray['error_message']
                ]);
            }
        }catch (\Exception $e){
            Log::info($e);
        }
    }
    public function pinganquerybill(Request $request){
        return view('admin.pingan.store.billquery');
    }
    public function pinganquerybillpost(Request $request){
        try{
            $order=trim($request->order);
            $type=$request->billtype==1?'out_trade_no':'trade_no';
            $ao = new BaseController();
            $aop = $ao->AopClient();
            $aop->method = "fshows.liquidation.alipay.trade.query";
            $pay = [
                $type => $order,
            ];
            $dataWAop = array('content' => json_encode($pay));
            $response = $aop->execute($dataWAop);
            return $response;
        }catch (\Exception $e){
            return json_encode([
                'success'=>0,
                'error_message'=>$e->getMessage().$e->getLine(),
            ]);
        }

    }
}