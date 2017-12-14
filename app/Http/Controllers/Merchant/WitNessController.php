<?php
namespace App\Http\Controllers\Merchant;
use App\Models\MerchantShops;
use App\Models\PinganStore;
use App\Models\PinganWitnessAccount;
use App\Models\WithDraw;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class WitNessController extends \App\Http\Controllers\PingAn\BaseController {
    public function Index()
    {
     $witnessInfo=PinganWitnessAccount::where('merchant_id',Auth::guard('merchant')->user()->id)->first();
     return view('merchant.witness.witness',compact('witnessInfo'));
    }
    public function QueryWitNess(){
        try{
            $merchantShopInfo=MerchantShops::where('merchant_id',Auth::guard('merchant')->user()->id)
                ->where('store_type','pingan')->first();
            if($merchantShopInfo){
                $storeInfo=PinganStore::where('external_id',$merchantShopInfo->store_id)->first();
                if($storeInfo){
                    $data['lp_store_id']=$storeInfo->sub_merchant_id;
                    $data['lp_jzb_account_type']=1;
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
                }else{
                    return json_encode([
                        "success"=>0,
                        'msg'=>"数据库未查到该商户信息"
                    ]);
                }
            }else{
                return json_encode([
                    "success"=>0,
                    'msg'=>"请先开通平安银行通道"
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
            $merchantShopInfo=MerchantShops::where('merchant_id',Auth::guard('merchant')->user()->id)
                ->where('store_type','pingan')->first();
            if($merchantShopInfo){
                $storeInfo=PinganStore::where('external_id',$merchantShopInfo->store_id)->first();
                if($storeInfo){
                    $data['lp_store_id']=$storeInfo->sub_merchant_id;
                    $data['lp_jzb_account_type']=1;
                    $data['tran_amount']=$request->tran_amount;
                    $aop=$this->AopClient();
                    $aop->method = "fshows.liquidation.witness.withdraw";
                    $datas = array('content' => json_encode($data));
                    $response = $aop->execute($datas);
                    $responseArray = json_decode($response, true);
                    if ($responseArray['success']) {
                        $data['sub_merchant_id']=$data['lp_store_id'];
                        $data['withdraw_no']=$responseArray['return_value']["withdraw_no"];
                        $data['merchant_id']=Auth::guard('merchant')->user()->id;
                        $withdraw=WithDraw::create(array_except($data,'lp_store_id'));
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
                }else{
                    return json_encode([
                        "success"=>0,
                        'msg'=>"数据库未查到该商户信息"
                    ]);
                }
            }else{
                return json_encode([
                    "success"=>0,
                    'msg'=>"请先开通平安银行通道"
                ]);
            }
        }catch (\Exception $e){
            return json_encode([
                "success"=>0,
                'msg'=>$e->getMessage()
            ]);
        }
    }

    public function withdrawInfo(){
        $withdrawInfo=WithDraw::where('merchant_id',Auth::guard('merchant')->user()->id)->paginate(8);
        return view('merchant.witness.withdrawinfo',compact('withdrawInfo'));
    }
}