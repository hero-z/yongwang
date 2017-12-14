<?php
/*
*  浦发服务商后台管理
*/
namespace App\Http\Controllers\Yirui;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller; 


use App\Models\Paipai;
use App\Models\MerchantShops;
use App\Merchant;
class PaipaiController extends Controller
{ 

    public function lst(Request $request)
    {
      $all_store=\App\Common\AllStore::allStoreList();
      foreach($all_store as $v)
      {
        $store_lst[$v->store_id]=$v->store_name;
      }

      // var_dump($store_lst);die;



        $data=Paipai::paginate(12);
        return view('yirui.paipailst',compact('data','store_lst'));
    }
    // ajax获取店铺店主id
    public function storeMerchant(Request $request)
    {
    	$store_id=$request->get('store_id');

    	if(empty($store_id))
    	{
            return response()->json([
                    'status'=>'2',
                    'message'=>'参数不正确！'
                ]);
    	}

    	$all_merchants=MerchantShops::where('store_id',$store_id)->get();
    	if($all_merchants->isEmpty())
    	{
            return response()->json([
                    'status'=>'2',
                    'message'=>'没有收银员！'
                ]);

    	}

    	foreach ($all_merchants as $key => $value) {
    		$merchants[]=$value->merchant_id;
    	}

    	$data=Merchant::whereIn('id',$merchants)->get();
        return response()->json([
                'status'=>'1',
                'data'=>$data->toArray()
        ]);
    }

    public function add(Request $request)
    {
    	$id=$request->get('id');


    	// 表单提交（包括修改和新增
        if($request->isMethod('post'))
        {
          // 添加
          if(empty($id))
          {
              return $this->_handleAdd($request);
          }
          // 修改
          else
          {
              return $this->_handleSave($request);
          }
        }

        $data=(object)null;
    	// 获取修改数据
    	if(!empty($id))
    	{
    		$data=Paipai::where('id',$id)->first();
    	}

    	$all_store=\App\Common\AllStore::get();
        return view('yirui.paipaiadd',['data'=>$data,'all_store'=>$all_store]);
    }



    protected function _handleAdd($request)
    {
    	$cin=$request->only([
'store_id',
'm_id',
'name',
'device_no',
'status',
'device_pwd',

    		]);

      $validate=\Validator::make($cin, [
                      'store_id'=>'required',
                      'm_id'=>'required',
                      'name'=>'required',
                      'device_no'=>'required',
                      'device_pwd'=>'required',
                      'status'=>'required',
                  ], [
                      'required' => ':attribute为必填项',
                      'min' => ':attribute长度不符合要求',
                      'max' => ':attribute长度不符合要求',
                      'unique' => ':attribute已经被人占用',
                      'exists' => ':attribute不存在！',
                  ], [
                      'status' => '盒子状态',
                      'store_id' => '店铺id',
                      'm_id' => '收银员id',
                      'name' => '设备名称',
                      'device_no' => '设备号',
                      'device_pwd' => '设备通信秘钥',
                  ]);
      

        // 前台提交数据验证失败
        if($validate->fails())
        {
            return response()->json([
                    'status'=>'2',
                    'message'=>$validate->getMessageBag()->first()
                ]);
        }

        $ok=Paipai::create($cin);
        if($ok)
        {
            return response()->json([
                    'status'=>'1',
                    'message'=>'创建成功！',
                    'url'=>route('paipailst')
                ]);

        }
        else
        {

            return response()->json([
                    'status'=>'2',
                    'message'=>'创建失败！'
                ]);
        }






    }



    protected function _handleSave($request)
    {
    	$id=$request->get('id');
    	$data=Paipai::where('id',$id)->first();
    	if(empty($data))
    	{

            return response()->json([
                    'status'=>'2',
                    'message'=>'要修改的信息不存在！'
                ]);

    	}



      $cin=$request->only([
'store_id',
'm_id',
'name',
'device_no',
'status',
'device_pwd',

        ]);

      $validate=\Validator::make($cin, [
                      'store_id'=>'required',
                      'm_id'=>'required',
                      'name'=>'required',
                      'device_no'=>'required',
                      'device_pwd'=>'required',
                      'status'=>'required',
                  ], [
                      'required' => ':attribute为必填项',
                      'min' => ':attribute长度不符合要求',
                      'max' => ':attribute长度不符合要求',
                      'unique' => ':attribute已经被人占用',
                      'exists' => ':attribute不存在！',
                  ], [
                      'status' => '盒子状态',
                      'store_id' => '店铺id',
                      'm_id' => '收银员id',
                      'name' => '设备名称',
                      'device_no' => '设备号',
                      'device_pwd' => '设备通信秘钥',
                  ]);
      

        // 前台提交数据验证失败
        if($validate->fails())
        {
            return response()->json([
                    'status'=>'2',
                    'message'=>$validate->getMessageBag()->first()
                ]);
        }
        $data->updated_at=date('Y-m-d H:i:s');
        $ok=$data->update($cin);
        if($ok)
        {
            return response()->json([
                    'status'=>'1',
                    'message'=>'修改成功！',
                    'url'=>route('paipailst')
                ]);

        }
        else
        {

            return response()->json([
                    'status'=>'2',
                    'message'=>'修改失败！'
                ]);
        }






    }


}
