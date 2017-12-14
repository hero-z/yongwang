<?php
/**
 * Created by PhpStorm.
 * User: hero
 * Date: 2017/6/6
 * Time: 10:21
 */

namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\Controller;
use App\Merchant;
use App\Models\MerchantShops;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;
use Symfony\Component\HttpFoundation\Request;

class CashierManageController extends Controller
{
    /**
     * @return string
     */
    public function index()
    {
        $info='没有权限操作!';
        $m_id = auth()->guard('merchant')->user()->id;
        $merchant=DB::table('merchants')->where('id',$m_id)->first();
        if($merchant->type==0){
            $list=Merchant::where('pid',$merchant->id)->paginate(9);
            return view('merchant.Cashier.cashierlist',['list'=>$list]);
        }
        return view('merchant.Cashier.cashierlist',['info'=>$info]);
    }

    /**
     * @return string
     */
    public function add()
    {
        $info='没有权限操作!';
        $m_id = auth()->guard('merchant')->user()->id;
        $merchant=DB::table('merchants')->where('id',$m_id)->first();
        if($merchant->type==0){
            return view('merchant.Cashier.cashieradd',['m_id'=>$m_id]);
        }
        return view('merchant.Cashier.cashierlist',['info'=>$info]);
    }
    public function doadd(Request $request){
        $data=$request->except('_token');
        $info='未知错误';
        try{
            $checkname=Merchant::where('name',$data['name'])->get();
            if(!$checkname->isEmpty()){
                $info='该名称已经被占用';
            }
            $checkphone=Merchant::where('phone',$data['phone'])->get();
            if(!$checkphone->isEmpty()){
                $info='该手机号已经被占用';
            }
            if($checkname->isEmpty()&&$checkphone->isEmpty()){
                $istdata['name']=$data['name'];
                $istdata['phone']=$data['phone'];
                $istdata['pid']=$data['pid'];
                $istdata['type']=1;
                $istdata['password']=bcrypt($data['password']);
                $istres=Merchant::create($istdata)->id;
                if($istres){
                    $stores=MerchantShops::where('merchant_id',$data['pid'])->get();
                    foreach($stores as $v){
                        $insertdata=[];
                        $insertdata['merchant_id']=$istres;
                        $insertdata['store_id']=$v->store_id;
                        $insertdata['store_name']=$v->store_name;
                        $insertdata['store_type']=$v->store_type;
                        $insertdata['desc_pay']=$v->desc_pay;
                        $insertdata['status']=$v->status;
                        MerchantShops::create($insertdata);
                    }
                    return json_encode([
                        'status'=>1,
                        'msg'=>'添加成功'
                    ]);
                }
            }
            return json_encode([
                'status'=>0,
                'msg'=>$info
            ]);
        }catch (Exception $e){
            Log::info($e);
        }

    }
    public function update(){
        $info='没有权限操作!';
        $m_id = auth()->guard('merchant')->user()->id;
        $merchant=DB::table('merchants')->where('id',$m_id)->first();
        try{
            if($merchant->type==0){
                $cashiers=Merchant::where('pid',$merchant->id)->pluck('id')->toArray();
                $merchantshops=MerchantShops::where('merchant_id',$m_id)->get();
                foreach($cashiers as $v){
                    $clear=DB::table('merchant_shops')->where('merchant_id',$v)->delete();
//                    $clear=MerchantShops::where('merchant_id',$v->id)->delete();
                    foreach($merchantshops as $vv){
                        $insertdata=[];
                        $insertdata['merchant_id']=$v;
                        $insertdata['store_id']=$vv->store_id;
                        $insertdata['store_name']=$vv->store_name;
                        $insertdata['store_type']=$vv->store_type;
                        $insertdata['desc_pay']=$vv->desc_pay;
                        $insertdata['status']=$vv->status;
                        MerchantShops::create($insertdata);
                    }
                }
                return json_encode([
                    'status'=>1,
                    'msg'=>'更新成功'
                ]);
            }
            return json_encode([
                'status'=>0,
                'msg'=>$info
            ]);

        }catch (Exception $e){
            Log::info($e);
            return json_encode([
                'status'=>0,
                'msg'=>'更新失败!'
            ]);
        }
    }

    /**
     * @param Request $request->id
     * @return string
     */
    public function del(Request $request){
        $info='没有权限操作!';
        $m_id = auth()->guard('merchant')->user()->id;
        $merchant=DB::table('merchants')->where('id',$m_id)->first();
        $requestid=$request->id;
        try{
            if($merchant->type==0){
                $list=Merchant::where('pid',$merchant->id)->pluck('id')->toArray();
                if(is_array($list)){
                    if(in_array($requestid,$list)){
                        $res=Merchant::where('id',$requestid)->delete();
                        $resshop=DB::table('merchant_shops')->where('merchant_id',$requestid )->delete();
//                        $resshop=MerchantShops::where('merchant_id',$requestid)->delete();
                        if($res>0&&$resshop>0){
                            return json_encode([
                                'status'=>1,
                                'msg'=>'成功'
                            ]);
                        }
                        return json_encode([
                            'status'=>0,
                            'msg'=>'删除失败'
                        ]);
                    }else{
                        $info='名下无该收银员!';
                    }
                }else{
                    $info='名下无收银员';
                }
            }
            return json_encode([
                'status'=>0,
                'msg'=>$info
            ]);

        }catch (Exception $e){
            Log::info($e);
        }
    }
}