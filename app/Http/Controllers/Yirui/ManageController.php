<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2018/1/4
 * Time: 16:58
 */

namespace App\Http\Controllers\Yirui;


use App\Merchant;
use App\Models\Paipai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManageController
{
    public function index()
    {
        if(Auth::user()->hasRole('admin')){
            $lists=DB::table('paipai')->join('merchants','merchants.id','paipai.m_id')
                ->select('paipai.*','merchants.name as merchant_name','merchants.phone as merchant_phone')
                ->paginate(8);
            return view('yirui.index',compact('lists'));
        }else{
            die('没有权限!');
        }
    }

    public function del(Request $request)
    {
        $info='';
        if(Auth::user()->hasRole('admin')){
            $id=$request->id;
            $paipai=Paipai::find($id);
            if($paipai){
                $res=$paipai->delete();
                if($res){
                    return json_encode([
                        'success'=>1,
                        'msg'=>'操作成功!',
                    ]);
                }else{
                    $info='数据库操作失败';
                }
            }else{
                $info='查询出错';
            }
        }else{
            $info='没有权限!';
        }
        return json_encode([
            'success'=>0,
            'msg'=>$info,
        ]);
    }

    public function add(Request $request)
    {
        $info='';
        try{
            if($request->isMethod('GET')){
                return view('yirui.add');
            }elseif($request->isMethod('POST')){
                $data['m_id']=trim($request->m_id);
                $data['device_no']=trim($request->device_no);
                $data['name']=trim($request->name);
                $data['device_pwd']=trim($request->device_pwd);
                $paipai=Paipai::/*where('m_id',$data['m_id'])->or*/where('device_no',$data['device_no'])->first();
                $merchant=Merchant::find($data['m_id']);
                if($merchant){
                    if(!$paipai){
                        $res=Paipai::create($data);
                        if($res){
                            return json_encode([
                                'success'=>1,
                                'msg'=>'操作成功!',
                            ]);
                        }else{
                            $info='数据库操作失败';
                        }
                    }else{
                        /*if($paipai->m_id==$data['m_id']){
                            $info='该商户已经绑定了设备!';
                        }else*/if($paipai->device_no==$data['device_no']){
                            $info='该设备已经被绑定!';
                        }else{
                            $info='未知错误!';
                        }
                    }
                }else{
                    $info='商户账号id不存在!';
                }
                return json_encode([
                    "success"=>0,
                    "msg"=>"操作失败!".$info
                ]);
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
            $info=$error.$line;
            if($request->isMethod('POST')){
                return json_encode([
                    "success"=>0,
                    "msg"=>"查询失败!".$info
                ]);
            }
        }
        return view('admin.webank.error',compact('info'));
    }
}