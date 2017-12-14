<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/12/5
 * Time: 15:12
 */

namespace App\Http\Controllers\PingAn;


use App\Models\MerchantShops;
use App\Models\PinganStore;
use App\Models\PinganWitnessAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class WitnessController extends BaseController
{
    public function create($nick_name, $mobile_phone=null)
    {
        try{
            $data=[];
            $aop = $this->AopClient();
            $aop->method = "fshows.liquidation.witness.account.create";
            if($nick_name){
                $data['nick_name']=$nick_name;
            }
            if($mobile_phone){
                $data['mobile_phone']=$mobile_phone;
            }
            $data = array('content' => json_encode($data));
            $response = $aop->execute($data);
            $responseArray = json_decode($response, true);
            return $responseArray;
        }catch (\Exception $e){
            $code=$e->getCode();
            $line=$e->getLine();
            $msg=$e->getMessage();
            Log::info($e);
        }
        return [
            'error_code'=>$code,
            'error_message'=>$msg.$line,
            "success" => false
        ];
    }

    public function bindBank(array $params)
    {
        $code='-11111';
        $msg='';
        $line='';
        $ck=true;
        try{
            if(!array_key_exists('lp_store_id',$params)){
                $ck=false;
                $msg='参数有误!不存在lp_store_id';
            }
            if(!array_key_exists('id_type',$params)){
                $ck=false;
                $msg='参数有误!不存在id_type';
            }
            if(!array_key_exists('id_code',$params)){
                $ck=false;
                $msg='参数有误!不存在id_code';
            }
            if(!array_key_exists('bank_type',$params)){
                $ck=false;
                $msg='参数有误!不存在bank_type';
            }
            if(!array_key_exists('bank_card_id',$params)){
                $ck=false;
                $msg='参数有误!不存在bank_card_id';
            }
            if(!array_key_exists('bank_card_user',$params)){
                $ck=false;
                $msg='参数有误!不存在bank_card_user';
            }
            if(!array_key_exists('bank_name',$params)){
                $ck=false;
                $msg='参数有误!不存在bank_name';
            }
            if(!array_key_exists('bank_code',$params)){
                $ck=false;
                $msg='参数有误!不存在bank_code';
            }
            if(!array_key_exists('s_bank_code',$params)){
                $ck=false;
                $msg='参数有误!不存在s_bank_code';
            }
            if(!array_key_exists('mobile_phone',$params)){
                $ck=false;
                $msg='参数有误!不存在mobile_phone';
            }
            if($ck){
                $aop = $this->AopClient();
                $aop->method = "fshows.liquidation.store.witness.bind.bank";
                $data = array('content' => json_encode($params));
                // Log::info($data);
                $response = $aop->execute($data);
                $responseArray = json_decode($response, true);
                // Log::info($responseArray);
                $responseArray = json_decode($response, true);
                return $responseArray;
            }
        }catch (\Exception $e){
            $code=$e->getCode();
            $line=$e->getLine();
            $msg=$e->getMessage();
            Log::info($e);
        }
        return [
            'error_code'=>$code,
            'error_message'=>$msg.$line,
            "success" => false
        ];
    }

    public function getCity(Request $request)
    {
        $error='';
        $line='';
        try{
            $type=$request->type;
            $id=$request->id;
            if($type==1){
                if($id){
                    $info=DB::table('pub_pay_city')->where('city_nodecode',$id)->where('city_areatype',2)->pluck('city_areaname','city_areacode');
                    if($info){
                        $info=$info->toArray();
                    }
                    return json_encode([
                        'success'=>1,
                        'data'=>$info
                    ]);
                }
            }elseif($type==2){
                if($id){
                    $info=DB::table('pub_pay_city')->where('city_topareacode2',$id)->whereIn('city_areatype',[2,3])->pluck('city_oraareacode','city_areaname');
                    if($info){
                        $info=$info->toArray();
                    }
                    return json_encode([
                        'success'=>1,
                        'data'=>$info
                    ]);
                }
            }else{
                $error='参数出错!';
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return json_encode([
            'success'=>0,
            'msg'=>"获取城区信息失败!".$error.$line
        ]);
    }

    public function getOpenBank(Request $request)
    {
        $error='';
        $line='';
        try{
            $banktype=trim($request->banktype);
            $county_code=trim($request->county_code);
            $keyword=trim($request->keyword);
            $where=[];
            if($keyword){
                $where[]=['bankname','like','%'.$keyword.'%'];
            }
            $info=DB::table('zjjz_cnaps_bankinfo')
                ->where('bankclscode',$banktype)
                ->where('citycode',$county_code)
                ->where($where)
                ->limit(50)
                ->pluck('bankname','bankno');
            if($info){
                $info=$info->toArray();
            }
            return json_encode([
                'success'=>1,
                'data'=>$info
            ]);
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
        }
        return json_encode([
            'success'=>0,
            'msg'=>"获取城区信息失败!".$error.$line
        ]);
    }

    public function verifyMessage(Request $request)
    {
        $info='';
        try{
            $merchant_id = auth()->guard('merchant')->user()->id;
            $store=MerchantShops::where('merchant_id',$merchant_id)->where('store_type','pingan')->where('status','1')->first();
            if($store){
                $pstore=PinganStore::where('external_id',$store->store_id)->first();
                if($pstore){
                    $sub_merchant_id=$pstore->sub_merchant_id;
                    $code=trim($request->code);
                    $witness=PinganWitnessAccount::where('merchant_id',$merchant_id)->first();
                    if($witness){
                        $phone=$witness->card_phone;
                        $aop = $this->AopClient();
                        $aop->method = "fshows.liquidation.store.witness.bind.bank.verify.message";
                        $data = array('content' => json_encode([
                            'lp_store_id'=>$sub_merchant_id,
                            'message_code'=>$code,
                            'mobile_phone'=>$phone
                        ]));
                        // Log::info($data);
                        $response = $aop->execute($data);
                        $responseArray = json_decode($response, true);
                        // Log::info($responseArray);
                        if($responseArray['success']){
                            return json_encode([
                                'success' => true,
                                'msg' =>$responseArray['return_value']['msg']
                            ]);
                        }else{
                            $info='商户见证宝短信验证失败!'.(array_key_exists('error_message',$responseArray)?$responseArray['error_message']:'');
                        }
                    }else{
                        $info='没有平安见证宝!查询异常';
                    }
                }else{
                    $info='查询异常!';
                }
            }else{
                $info='您没有平安通道,或者通道状态不正常!';
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
            $info=$error.$line;
        }
        return json_encode([
            'success'=>0,
            'msg'=>$info
        ]);

    }
    public function verifyMoney(Request $request)
    {
        $info='';
        try{
            $merchant_id = auth()->guard('merchant')->user()->id;
            $store=MerchantShops::where('merchant_id',$merchant_id)->where('store_type','pingan')->where('status','1')->first();
            if($store){
                $pstore=PinganStore::where('external_id',$store->store_id)->first();
                if($pstore){
                    $sub_merchant_id=$pstore->sub_merchant_id;
                    $money=trim($request->money);
                    $aop = $this->AopClient();
                    $aop->method = "fshows.liquidation.store.witness.bind.bank.verify.money";
                    $data = array('content' => json_encode(['lp_store_id'=>$sub_merchant_id,'tran_amount'=>$money]));
                    // Log::info($data);
                    $response = $aop->execute($data);
                    $responseArray = json_decode($response, true);
                    // Log::info($responseArray);
                    if($responseArray['success']){
                        return json_encode([
                            'success' => true,
                            'msg' =>$responseArray['return_value']['msg']
                        ]);
                    }else{
                        $info='商户见证宝金额鉴权失败!'.(array_key_exists('error_message',$responseArray)?$responseArray['error_message']:'');
                    }
                }else{
                    $info='查询异常!';
                }
            }else{
                $info='您没有平安通道,或者通道状态不正常!';
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
            $info=$error.$line;
        }
        return json_encode([
            'success'=>0,
            'msg'=>$info
        ]);
    }
}