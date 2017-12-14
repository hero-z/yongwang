<?php
namespace App\Http\Controllers\PingAn;
use App\Models\PinganWitnessAccount;
use App\Models\WithDraw;
use Illuminate\Http\Request;
class WitNessInfoController extends BaseController{
    public function WitNessInfo(Request $request)
    {
        $withdrawInfo=WithDraw::where('lp_jzb_account_type',2)->paginate(8);
        return view('admin.pingan.store.withdraw',compact('withdrawInfo'));
    }
    public function QueryWitNess(){
        try{
            $data['lp_jzb_account_type']=2;
            $aop=$this->AopClient();
            $aop->method = "fshows.liquidation.witness.balance.query";
            $data = array('content' => json_encode($data));
            $response = $aop->execute($data);
            $responseArray = json_decode($response, true);
                    if ($responseArray['success']) {
                        return json_encode([
                            "success"=>1,
                            'data'=>$responseArray['return_value']
                        ]);
                    }else{
                        return json_encode([
                            'msg'=>$responseArray['error_message'],
                            "success"=>0
                        ]);
                    }
        }catch (\Exception $e){
            return json_encode([
                "success"=>0,
                'msg'=>$e->getMessage()
            ]);
        }
    }

    public function WithDraw(Request $request)
    {
        try{
                    $data['lp_jzb_account_type']=2;
                    $data['tran_amount']=$request->tran_amount;
                    $aop=$this->AopClient();
                    $aop->method = "fshows.liquidation.witness.withdraw";
                    $datas = array('content' => json_encode($data));
                    $response = $aop->execute($datas);
                    $responseArray = json_decode($response, true);
                    if ($responseArray['success']) {
                        /*$data['sub_merchant_id']=$data['lp_store_id'];*/
                        $data['withdraw_no']=$responseArray['return_value']["withdraw_no"];
                        $withdraw=WithDraw::create($data);
                        if($withdraw){
                            return json_encode([
                                "success"=>1,
                                'data'=>"提现申请提交成功"
                            ]);
                        }else{
                            return json_encode([
                                "success"=>0,
                                'msg'=>"提现申请提交成功,但是插库失败"
                            ]);
                        }

                    }else{
                        return json_encode([
                            'msg'=>$responseArray['error_message'],
                            "success"=>0
                        ]);
                    }
        }catch (\Exception $e){
            return json_encode([
                "success"=>0,
                'msg'=>$e->getMessage()
            ]);
        }
    }
}