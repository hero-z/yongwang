<?php
namespace App\Http\Controllers\AlipayOpen;
use App\Http\Controllers\Controller;
use App\Models\AlipayShopLists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class SalipayBranchController extends Controller{
    //口碑分店列表
    public function salipayBranchIndex(Request $request){

        $auth = Auth::user()->can('salipayBranch');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $pid = $request->get('pid');
        $datapage =DB::table("alipay_shop_lists")->where('pid', $pid)->where('is_delete',0)->paginate(8);
        $store_name = DB::table("alipay_shop_lists")->where('id', $pid)->first()->main_shop_name;
        return view('admin.alipayopen.salipayBranch.salipayBranchIndex', compact('datapage', 'store_name','pid'));
    }
    //添加口碑分店
    public function addSalipayBranch(Request $request){
        return view('admin.alipayopen.salipayBranch.addAlipayBranch');
    }
    //执行口碑分店添加
    public function createSalipayBranch(Request $request){
        $data = $request->all();
        $rules = [
            'main_shop_name' => 'required|unique:alipay_shop_lists',
            'contact_number'=> 'required|min:11|max:11',
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
            $store =AlipayShopLists::where('id', $request->get('pid'))->first();
            $store=$store->toArray();
            $store['store_id'] = $data['store_id'];
            $store['pid'] = $data['pid'];
            $store['main_shop_name'] = $data['main_shop_name'];
            $store['contact_number'] = $data['contact_number'];

            $s =AlipayShopLists::where('store_id', $data['store_id'])->first();
            if ($s) {
                AlipayShopLists::where('store_id', $data['store_id'])->updated($store);
            } else {
                AlipayShopLists::create($store);
            }
        } catch (\Exception $exception) {
            return back()->with('errors', '添加失败');
        }
        return redirect('/admin/alipayopen/salipayBranchIndex?pid=' . $data['pid']);
    }
}
?>