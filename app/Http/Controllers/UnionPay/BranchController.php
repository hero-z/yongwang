<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/29
 * Time: 16:05
 */

namespace App\Http\Controllers\UnionPay;


use App\Models\PingancqrLsitsinfo;
use App\Models\PinganStore;
use App\Models\UnionPayStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BranchController extends BaseController
{

    public function BranchIndex(Request $request)
    {
        $auth = Auth::user()->can('UnionPayBranch');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $pid = $request->get('pid');
        $datapage = DB::table('union_pay_stores')
            ->join('users', 'union_pay_stores.user_id', '=', 'users.id')
            ->select('union_pay_stores.*', 'users.name')
            ->where('pid', $pid)
            ->where('union_pay_stores.is_delete', 0)
            ->paginate(8);

        $store_name = UnionPayStore::where('id', $pid)->first()->alias_name;
        return view('admin.UnionPay.branch.index', compact('datapage', 'store_name', 'pid'));
    }

    public function BranchAdd()
    {
        return view('admin.UnionPay.branch.add');
    }

    public function BranchAddPost(Request $request)
    {
        $data = $request->all();
        $rules = [
            'alias_name' => 'required|unique:pingan_stores',
            'manager_phone' => 'required|min:11|max:11',
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
            $store = UnionPayStore::where('id', $request->get('pid'))->first();
            $store = $store->toArray();
            $store['store_id'] = $data['store_id'];
            $store['pid'] = $data['pid'];
            $store['alias_name'] = $data['alias_name'];
            $store['manager'] = $data['manager'];
            $store['manager_phone'] = $data['manager_phone'];
            $s = UnionPayStore::where('store_id', $data['store_id'])->first();
            if ($s) {
                PinganStore::where('store_id', $data['store_id'])->updated($store);
            } else {
                UnionPayStore::create($store);
            }
        } catch (\Exception $exception) {
            return back()->with('error', '添加失败');
        }
        return redirect('/admin/UnionPay/BranchIndex?pid=' . $data['pid']);
    }

}