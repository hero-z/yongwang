<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/31
 * Time: 15:41
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Http\Controllers\Controller;
use App\Merchant;
use App\Models\AlipayAppOauthUsers;
use App\Models\MerchantShops;
use App\Models\PingancqrLsitsinfo;
use App\Models\PinganStore;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CashierController extends Controller
{
    //收营员列表
    public function CashierIndex(Request $request)
    {
        $id=$request->get("id");
        $store_id = $request->get('store_id');
        $store_name = $request->get('store_name');
        $data = DB::table('merchant_shops')
            ->join('merchants', 'merchants.id', '=', 'merchant_shops.merchant_id')
            ->where('merchant_shops.store_id', $store_id)
            ->select('merchants.*', 'merchant_shops.store_id', 'merchant_shops.store_type')
            ->get();
        if ($data->isEmpty()) {
            $paginator = "";
            $datapage = "";
        } else {
            $data = $data->toArray();
            //非数据库模型自定义分页
            $perPage = 9;//每页数量
            if ($request->has('page')) {
                $current_page = $request->input('page');
                $current_page = $current_page <= 0 ? 1 : $current_page;
            } else {
                $current_page = 1;
            }
            $item = array_slice($data, ($current_page - 1) * $perPage, $perPage); //注释1
            $total = count($data);
            $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
            $datapage = $paginator->toArray()['data'];
        }
        return view('admin.alipayopen.Cashier.index', compact('datapage','paginator','store_id','store_name',"id"));

    }

    //添加收银员
    public function CashierAdd(Request $request)
    {
        $id=$request->get("id");
        return view('admin.alipayopen.Cashier.add',compact("id"));

    }

    //保存收银员
    public function CashierAddPost(Request $request)
    {

        $store_id = $request->get('store_id');
        $store_name = $request->get('store_name');
        $data = $request->all();
        $type = substr($store_id, 0, 1);
        $dataIn = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
        ];
        if ($type == 'p') {
            $store_type = 'pingan';
            $desc_pay = "平安通道";
        }
        if ($type == 'o') {
            $store_type = 'oalipay';
            $desc_pay = "支付宝当面付";
        }
        if ($type == 's') {
            $store_type = 'salipay';
            $desc_pay = "支付宝口碑";
        }
        if ($type == 'w') {
            $store_type = 'weixin';
            $desc_pay = "微信支付";
        }
        if ($type == 'f') {
            $store_type = 'pufa';
            $desc_pay = "浦发通道";
        }
        if ($type == 'u') {
            $store_type = 'unionpay';
            $desc_pay = "银联";
        }

        if($type=='m'){
        $store_type = 'minsheng';
        $desc_pay = "民生";
    }
        if ($type == 'b') {
            $store_type = 'webank';
            $desc_pay = "微众通道";
        }

        $m = MerchantShops::where('store_id', $store_id)->first();//如果绑定了说明有店长了
        if ($m) {
            $dataIn['type'] = 1;
            $dataIn['pid'] = $m->merchant_id;//收银员的pid 是店长的ID
        }
        $rules = [
            'name' => 'required|max:255|unique:merchants',
            'email' => 'required|email|max:255|unique:merchants',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|min:11|max:11|unique:merchants',
        ];
        $messages = [
            'required' => '密码不能为空',
            'between' => '密码必须是6~20位之间',
            'confirmed' => '新密码和确认密码不匹配',
            'unique' => '系统已经存在'
        ];
        $cn = [
            'name' => '店铺名称',
            'phone' => '手机号码',
            'password' => '密码',
            'password_confirmation' => '确认密码'
        ];
        $validator = Validator::make($data, $rules, $messages, $cn);
        if ($validator->fails()) {
            return back()->withErrors($validator);  //返回一次性错误
        }
        $m = Merchant::create($dataIn);
        $mid = $m->id;
        MerchantShops::create([
            'merchant_id' => $mid,
            'store_id' => $store_id,
            'store_name' => $store_name,
            'store_type' => $store_type,
            'desc_pay' => $desc_pay,
        ]);
        $id=$request->get("id");
        return redirect(url('/admin/alipayopen/CashierIndex?store_id=' . $store_id . '&store_name=' . $store_name."&id=".$id));
    }

    //绑定收银员
    public function CashierBind()
    {

    }

    public function CashierBindPost()
    {

    }

    //平安收银员的二维码
    public function pinganCashierQr(Request $request)
    {
        $store_id = $request->get('store_id');
        $merchant_id = $request->get('merchant_id');
        $PingancqrLsitsinfo = PingancqrLsitsinfo::where('store_id', $store_id)->first();
        if ($PingancqrLsitsinfo) {
            $code_number = $PingancqrLsitsinfo->code_number;
            if (!$code_number){
                dd('收款码不存在！请检查商户是否入驻成功');
            };
        } else {
            dd('收款码有误！请检查商户是否入驻成功');
        }
        $store_name = PinganStore::where('external_id', $store_id)->first()->alias_name;
        $merchant_name=Merchant::where('id',$merchant_id)->first()->name;
        $code_url = url('/Qrcode?code_number=' . $code_number . '&merchant_id=' . $merchant_id);
        return view('admin.pingan.page.qr', compact('code_url', 'merchant_name', 'store_name'));
    }
}