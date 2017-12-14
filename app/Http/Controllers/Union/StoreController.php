<?php
/**
 * Date: 2017-04-25
 * Time: 11:10
 * 银联测试方法
 */
namespace App\Http\Controllers\Union;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;




use App\Common\Union\AppConfig;
use App\Common\Union\AppUtil;

use App\Models\UnionStore;

use Illuminate\Support\Facades\Auth;


class StoreController extends \App\Http\Controllers\Controller
{
    static function log($data,$file='')
    {
        $file=$file ? $file : (storage_path().'/logs/union_error_log_store_alipay.txt');
        file_put_contents($file, "\n\n\n".date('Y-m-d H:i:s')."\n".var_export($data,TRUE),FILE_APPEND);
    }


    public function lst(Request $request,UnionStore $store)
    {
        $condition=$request->all();

        isset($condition['store_name']) && (!empty(trim($condition['store_name']))) && ($store=$store->where('store_name','like','%'.trim($condition['store_name']).'%'));



        $user_id=Auth::user()->id;
        $listdata=$store->where('user_id',$user_id)->paginate(12);
        return view('union.storelst',['data'=>$listdata,'condition'=>$condition]);
    }


    /*
        店铺资料修改和添加

    */
    public function edit(Request $request)
    {
      
        $id=$request->get('id');

        if($request->isMethod('post'))
        {
            // 修改
            if(!empty($id))
            {
                // return $this->storeAdd($request);

              return $this->storeSave($request);
            }
            // 添加
            else
            {
                return $this->storeAdd($request);
            }
        }
        $store=UnionStore::where('id',$id)->first();

        return view('union.storeedit',['data'=>$store]);

    }



    protected function storeAdd($request)
    {
        $user_id=Auth::user()->id;
        // $user_id=auth()->guard('merchant')->user()->id;//merchant表的id
        $cin=[
            'pid'=>'0',
            'store_id'=>UnionStore::makeStoreId(),
            'store_name'=>$request->get('store_name'),
            'merchant_id'=>$request->get('merchant_id'),
            'app_id'=>$request->get('app_id'),
            'app_key'=>$request->get('app_key'),
            'mobile'=>$request->get('mobile'),
            'shop_user'=>$request->get('shop_user'),
            'status'=>$request->get('status'),
            'province'=>$request->get('province',''),
            'city'=>$request->get('city',''),
            'district'=>$request->get('district',''),
            'preaddress'=>$request->get('preaddress',''),
            'endaddress'=>$request->get('endaddress',''),
            'user_id'=>$user_id,
        ];


          $validate=\Validator::make($cin, [
              'store_name' => 'required|max:50|min:3|unique:union_store,store_name',
              'store_id' => 'required|unique:union_store,store_id',
              // 'store_name' => 'required',
              'merchant_id' => 'required',
              'app_id' => 'required',
              'app_key' => 'required',
              'mobile' => 'required',
              'shop_user' => 'required',
              'status' => 'required',
              // 'province' => 'required',
              // 'city' => 'required',
              // 'district' => 'required',
              // 'preaddress' => 'required',
              // 'endaddress' => 'required',
              'user_id' => 'required',

          ], [
              'required' => ':attribute为必填项！',
              'min' => ':attribute长度不符合要求！',
              'max' => ':attribute长度不符合要求！',
              'unique' => ':attribute已经被人占用！',
              'exists' => ':attribute不存在！'
          ], [
              'store_id' => '系统商户号',
              'store_name' => '店铺名称',
              'merchant_id' => '三方商户号',
              'app_id' => '三方APPID',
              'app_key' => '三方APPKEY',
              'mobile' => '店铺负责人电话',
              'shop_user' => '店铺负责人',
              'status' => '店铺状态',
              // 'province' => '店铺全称',
              // 'city' => '店铺全称',
              // 'district' => '店铺全称',
              // 'preaddress' => '店铺全称',
              // 'endaddress' => '店铺全称',
              'user_id' => '当前登陆者',

          ]);

          if($validate->fails())
          {
            return response()->json(['status'=>'2','message'=>$validate->getMessageBag()->first()]);
          }


          $ok=UnionStore::create($cin);

          if(!$ok)
          {
            return response()->json([
                            'status'=>'2',
                            'message'=>'商户创建失败，请重试！'
                        ]);
          }
          else
          {
            return response()->json([
                            'status'=>'1',
                            'message'=>'店铺创建成功！',
                            'url'=>route('upstorelst')
                        ]);

          }

    }






    protected function storeSave($request)
    {
      $store=UnionStore::where('id',$request->get('id'))->first();
      if(empty($store))
      {
            return response()->json([
                            'status'=>'2',
                            'message'=>'您要修改的店铺不存在！'
                        ]);

      }
        $user_id=Auth::user()->id;
        // $user_id=auth()->guard('merchant')->user()->id;//merchant表的id

      if($store->user_id!=$user_id)
      {

            return response()->json([
                            'status'=>'2',
                            'message'=>'您无权修改！'
                        ]);
      }

        $cin=[
            'pid'=>'0',
            // 'store_id'=>UnionStore::makeStoreId(),
            'store_name'=>$request->get('store_name'),
            'merchant_id'=>$request->get('merchant_id'),
            'app_id'=>$request->get('app_id'),
            'app_key'=>$request->get('app_key'),
            'mobile'=>$request->get('mobile'),
            'shop_user'=>$request->get('shop_user'),
            'status'=>$request->get('status'),
            'province'=>$request->get('province',''),
            'city'=>$request->get('city',''),
            'district'=>$request->get('district',''),
            'preaddress'=>$request->get('preaddress',''),
            'endaddress'=>$request->get('endaddress',''),
            // 'user_id'=>$user_id,
        ];


          $validate=\Validator::make($cin, [
              'store_name' => 'required|max:50|min:3',
              // 'store_id' => 'required|unique:union_store,store_id',
              // 'store_name' => 'required',
              'merchant_id' => 'required',
              'app_id' => 'required',
              'app_key' => 'required',
              'mobile' => 'required',
              'shop_user' => 'required',
              'status' => 'required',
              // 'province' => 'required',
              // 'city' => 'required',
              // 'district' => 'required',
              // 'preaddress' => 'required',
              // 'endaddress' => 'required',
              // 'user_id' => 'required',

          ], [
              'required' => ':attribute为必填项！',
              'min' => ':attribute长度不符合要求！',
              'max' => ':attribute长度不符合要求！',
              'unique' => ':attribute已经被人占用！',
              'exists' => ':attribute不存在！'
          ], [
              'store_id' => '系统商户号',
              'store_name' => '店铺名称',
              'merchant_id' => '三方商户号',
              'app_id' => '三方APPID',
              'app_key' => '三方APPKEY',
              'mobile' => '店铺负责人电话',
              'shop_user' => '店铺负责人',
              'status' => '店铺状态',
              // 'province' => '店铺全称',
              // 'city' => '店铺全称',
              // 'district' => '店铺全称',
              // 'preaddress' => '店铺全称',
              // 'endaddress' => '店铺全称',
              // 'user_id' => '当前登陆者',

          ]);

          if($validate->fails())
          {
            return response()->json(['status'=>'2','message'=>$validate->getMessageBag()->first()]);
          }


          $have=UnionStore::where('id','!=',$request->get('id'))->where('store_name',$cin['store_name'])->first();
          if(!empty($have))
          {
            return response()->json([
                            'status'=>'2',
                            'message'=>'店铺名称已被占用！'
                        ]);

          }

          $cin=array_filter($cin);
          $store->updated_at=date('Y-m-d H:i:s');

          $ok=$store->update($cin);



          if(!$ok)
          {
            return response()->json([
                            'status'=>'2',
                            'message'=>'商户修改失败，请重试！'
                        ]);
          }
          else
          {
            return response()->json([
                            'status'=>'1',
                            'message'=>'店铺修改成功！',
                            'url'=>route('upstorelst')
                        ]);

          }

    }






}