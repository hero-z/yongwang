<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/29
 * Time: 12:14
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Http\Controllers\Controller;
use App\Models\AlipayAppOauthUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AlipayBranchController extends Controller
{

    //分店列表
    public function AlipayBranchIndex(Request $request)
    {
        $auth = Auth::user()->can('oauthlistBranch');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $pid = $request->get('pid');
        $datapage = AlipayAppOauthUsers::where('pid', $pid)->where('is_delete',0)->paginate(8);
        $store_name = AlipayAppOauthUsers::where('id', $pid)->first()->auth_shop_name;
        return view('admin.alipayopen.alipaybranch.index', compact('datapage', 'store_name','pid'));
    }

    //添加分店
    public function AlipayBranchAdd(Request $request)
    {
        return view('admin.alipayopen.alipaybranch.add');


    }

    //
    public function AlipayBranchAddPost(Request $request)
    {
        $data = $request->all();
        $rules = [
            'auth_shop_name' => 'required|unique:alipay_app_oauth_users',
            'auth_phone'=> 'required|min:11|max:11',
        ];
        $messages = [
            'required' => '不能为空',
            'unique' => '店名已经存在',
            'min'=>'手机号码长度不够'
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator);  //返回一次性错误
        }
        try {
            $store = AlipayAppOauthUsers::where('id', $request->get('pid'))->first();
            $store = $store->toArray();
            $store['store_id'] = $data['store_id'];
            $store['pid'] = $data['pid'];
            $store['auth_shop_name'] = $data['auth_shop_name'];
            $store['auth_phone'] = $data['auth_phone'];
            $s = AlipayAppOauthUsers::where('store_id', $data['store_id'])->first();
            if ($s) {
                AlipayAppOauthUsers::where('store_id', $data['store_id'])->updated($store);
            } else {
                AlipayAppOauthUsers::create($store);
            }
        } catch (\Exception $exception) {
            return back()->with('errors', '添加失败');
        }
        return redirect('/admin/alipayopen/AlipayBranchIndex?pid=' . $data['pid']);
    }

}