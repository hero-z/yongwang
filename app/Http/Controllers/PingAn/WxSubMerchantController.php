<?php
namespace App\Http\Controllers\PingAn;
use App\Models\BusinessCate;
use App\Models\PinganConfig;
use App\Models\PinganStore;
use Illuminate\Http\Request;
class WxSubMerchantController extends BaseController{
    public function getBusiness(Request $request)
    {
        try{
            $id=$request->id;
            $where=[];
            if($id){
                $where[]=['pid','=',$id];
            }else{
                $where[]=['pid','=',0];
            }
            $businessInfo=BusinessCate::where($where)->get()->toArray();
            if($businessInfo){
                return json_encode([
                    'data'=>$businessInfo,
                    "success"=>1
                ]);
            }else{
                return json_encode([
                    'msg'=>'获取行业信息失败',
                    "success"=>0
                ]);
            }
        }catch (\Exception $e){
            return json_encode([
                'msg'=>'获取行业信息失败'.$e->getMessage(),
                "success"=>0
            ]);
        }

    }

    public function createSubMerchant(Request $request)
    {
       try{
           $aop = $this->AopClient();
           $aop->method = "fshows.liquidation.wx.submerchant.create.supplement";
           $store_id=$request->external_id;
           $storeInfo=PinganStore::where('external_id',$store_id)->first();
           $data['business']=$request->business;
           if($storeInfo){
               $data['merchant_name']=$storeInfo->name;
               $data['merchant_shortname']=$storeInfo->alias_name;
               $data['service_phone']=$storeInfo->service_phone;
               $data['contact']=$storeInfo->contact_name;
               $data['contact_phone']=$storeInfo->contact_phone;
               $data['contact_email']=$storeInfo->contact_email;
               $data['pay_type']=3;
               $data['store_id']=$storeInfo->sub_merchant_id;
           }
           $data = array('content' => json_encode($data));

           $response = $aop->execute($data);
           $responseArray = json_decode($response, true);
           if ($responseArray['success']) {
                $datas['sub_mch_id']=$responseArray['return_value']['sub_mch_id'];
                if(PinganStore::where('external_id',$store_id)->update($datas)){
                    return json_encode([
                        'data'=>'微信子商户创建成功',
                        "success"=>1
                    ]);
                }else{
                    return json_encode([
                        'msg'=>'微信子商户创建成功,但是更新数据库失败',
                        "success"=>0
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
               'msg'=>'提交信息失败'.$e->getMessage(),
               "success"=>0
           ]);
       }
    }

    public function getSubAppid()
    {
        try{
            $info=PinganConfig::first();
            if($info){
                return json_encode([
                    'data'=>$info->wx_app_id,
                    "success"=>1
                ]);
            }else{
                return json_encode([
                    'msg'=>'获取sub_appid失败',
                    "success"=>0
                ]);
            }
        }catch (\Exception $e){
            return json_encode([
                'msg'=>'获取sub_appid失败'.$e->getMessage(),
                "success"=>0
            ]);
        }
    }

    public function SubMerchantSet(Request $request)
    {
        try{
            $data['sub_appid']=$request->sub_appid;
            $data['subscribe_appid']=$request->subscribe_appid;
//            $data['jsapi_path']=url('admin/pingan/orderview')."/";
            $data['jsapi_path']="https://openapi-liquidation.51fubei.com/payPage/";
            $data['pay_type']=3;
            $data['store_id']=$request->sub_merchant_id;
            $data1 = array('content' => json_encode(array_except($data, array('subscribe_appid', 'jsapi_path'))));
            $store=PinganStore::where('sub_merchant_id',$data['store_id']);
            $error='';
            $aop = $this->AopClient();
            $aop->method = "fshows.liquidation.wx.submerchant.config.create.supplement";
            if(!$store->first()->sub_appid_status){
                $response = $aop->execute($data1);
                $responseArray = json_decode($response, true);
                if ($responseArray['success']) {
                    $store->update([
                        'sub_appid_status'=>1
                    ]);
                }else{
                    $error='设置sub_appid出错'.$responseArray['error_message'].";";
                }
            }
            if(!$store->first()->jsapi_path_status){
                $data2 = array('content' => json_encode(array_except($data, array('subscribe_appid', 'sub_appid'))));
                $response = $aop->execute($data2);
                $responseArray = json_decode($response, true);
                if ($responseArray['success']) {
                    PinganStore::where('sub_merchant_id',$data['store_id'])->update([
                        'jsapi_path_status'=>1
                    ]);
                }else{
                    $error=$error."设置jsapi_path出错".$responseArray['error_message'].";";
                }
            }
            if($data['subscribe_appid']){
                $data3 = array('content' => json_encode(array_except($data, array('jsapi_path', 'sub_appid'))));
                $response = $aop->execute($data3);
                $responseArray = json_decode($response, true);
                if ($responseArray['success']) {
                    PinganStore::where('sub_merchant_id',$data['store_id'])->update([
                        'subscribe_appid_status'=>1
                    ]);
                }else{
                    $error=$error."设置subscribe_appid出错".$responseArray['error_message'];
                }
            }
            if($error){
                return json_encode([
                    'msg'=>$error,
                    "success"=>0
                ]);
            }else{
                return json_encode([
                    'data'=>"设置成功",
                    "success"=>1
                ]);
            }
        }catch (\Exception $e){
            return json_encode([
                'msg'=>'设置失败'.$e->getMessage(),
                "success"=>0
            ]);
        }
    }
}