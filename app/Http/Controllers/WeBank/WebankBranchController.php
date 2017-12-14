<?php
/**
 * Created by PhpStorm.
 * User: hero
 * Date: 2017/6/22
 * Time: 11:38
 */

namespace App\Http\Controllers\WeBank;


use App\Models\WeBankStore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Request;

class WebankBranchController extends BaseController
{
    public function branchlist(Request $request){
        $auth = Auth::user()->can('webankBranch');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $alias_name=$request->alias_name;
        $store_name='';
        $wheresql=[];
        if($alias_name){
            $wheresql[]=['a.alias_name','like','%'.$alias_name.'%'];
        }
        $pid=$request->pid;
        $parent=WeBankStore::where('id',$pid)->first();
        if($parent){
            $store_name=$parent->store_name;
        }
        $lists=DB::table('we_bank_stores as a')
            ->join('users','users.id','a.user_id')
            ->where('a.pid',$pid)
            ->where('a.is_delete',0)
            ->where($wheresql)
            ->orderBy('a.created_at','desc')
            ->select('a.id','a.store_id','a.pid','a.alias_name','a.pay_status','a.contact_name','a.contact_phone_no','users.name as name')
            ->paginate(9);

        return view('admin.webank.Branch.branchlist',compact('lists','alias_name','pid','store_name'));
    }
    public function branchadd(Request $request){
        return view('admin.webank.Branch.branchadd');
    }
    public function branchaddpost(Request $request){
        $pid=$request->pid;
        $parent=WeBankStore::where('id',$pid)->first();
        if(!$parent||$parent&&$parent->pid!=0){
            die('非法pid');
        }
        $data = $request->except('pid');
        $rules = [
            'alias_name' => 'required|unique:pingan_stores',
            'service_phone' => 'required|min:11|max:11',
        ];
        $messages = [
            'required' => '不能为空',
            'unique' => '店名已经存在',
            'min' => '手机号码长度不够'
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator);  //返回一次性错误
        }
        try {
            $store_id=AopClient::getStoreId('b');
            $store = array_except($parent->toArray(),['id']);
            $store['store_id'] =$store_id ;
            $store['pid'] = (int)$pid;
            $store['alias_name'] = $data['alias_name'];
            $store['contact_name'] = $data['contact_name'];
            $store['service_phone'] = $data['service_phone'];
            $s = WeBankStore::where('store_id', $store_id)->first();
            if ($s) {
                dd('出错了!请刷新重试');
            } else {
                $ist=DB::table('we_bank_stores')->insert($store);
                $code_number = time() . rand(10000, 99999);
                DB::table('wb_qr_list_infos')->insert([
                    'user_id' => $store['user_id'],
                    'code_number' => $code_number,
                    'code_type' => 1,
                    'store_id' =>$store_id,
                    'updated_at'=>date('YmdHis')
                ]);
                $storeunions=DB::table('we_bank_storeunion')->where('store_id',$parent->store_id)->get();
                if($storeunions){
                    foreach($storeunions as $v){
                        DB::table('we_bank_storeunion')->insert([
                            'store_id'=>$store_id,
                            'wb_merchant_id'=>$v->wb_merchant_id,
                            'product_type'=>$v->product_type,
                            'partner_mch_id'=>$v->partner_mch_id,
                            'category_id'=>$v->category_id,
                            'payment_type'=>$v->payment_type,
                            'settlement_type'=>$v->settlement_type
                        ]);
                    }
                }
            }

        } catch (\Exception $exception) {
            Log::info('微众添加分店___________');
            Log::info($exception);
            return back()->with('errors', '添加失败');
        }
        return redirect(route('webankbranchlist',['pid'=>$pid]));
    }
}