<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/29
 * Time: 16:05
 */

namespace App\Http\Controllers\PingAn;


use App\Models\PingancqrLsitsinfo;
use App\Models\PinganStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PinanBranchController extends BaseController
{

    public function BranchIndex(Request $request)
    {
        $auth = Auth::user()->can('pinganBranch');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $pid = $request->get('pid');
        $datapage = PinganStore::where('pid', $pid)->where('is_delete',0)->paginate(8);
        $store_name = PinganStore::where('id', $pid)->first()->alias_name;
        return view('admin.pingan.branch.index', compact('datapage', 'store_name','pid'));
    }

    public function BranchAdd()
    {
        return view('admin.pingan.branch.add');
    }

    public function BranchAddPost(Request $request)
    {
        $data = $request->all();
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
            $store = PinganStore::where('id', $request->get('pid'))->first();
            $store = $store->toArray();
            $store['external_id'] = $data['external_id'];
            $store['pid'] = $data['pid'];
            $store['alias_name'] = $data['alias_name'];
            $store['contact_name'] = $data['contact_name'];
            $store['service_phone'] = $data['service_phone'];
            $s = PinganStore::where('external_id', $data['external_id'])->first();
            if ($s) {
                PinganStore::where('external_id', $data['external_id'])->updated($store);
            } else {
                PinganStore::create($store);
                $code_number = time() . rand(10000, 99999);
                PingancqrLsitsinfo::create([
                    'user_id' => $store['user_id'],
                    'user_name' =>$store['user_name'],
                    'code_number' => $code_number,
                    'code_type' => 1,
                    'store_id' =>$data['external_id'],
                    'store_name' =>$data['alias_name'],
                    'from_info' => 'pingan',
                ]);
            }
        } catch (\Exception $exception) {
            return back()->with('errors', '添加失败');
        }
        return redirect('/admin/pingan/BranchIndex?pid=' . $data['pid']);
    }

}