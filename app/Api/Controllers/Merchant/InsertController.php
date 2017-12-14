<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/5/23
 * Time: 13:20
 */

namespace App\Api\Controllers\Merchant;


use App\Merchant;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayShopLists;
use App\Models\MerchantShops;
use App\Models\MsStore;
use App\Models\PinganStore;
use App\Models\PufaStores;
use App\Models\UnionPayStore;
use App\Models\WeixinShopList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InsertController extends BaseController
{
//新增收营业员
    public function addMerchant(Request $request)
    {
        $user = $this->getMerchantInfo();
        if ($user['pid'] != 0) {
            return json_encode([
                'status' => 0,
                'msg' => '你没有权限添加收银员'
            ]);
        }
        $phone = $request->get('phone');
        $password = $request->get('password');
        $name = $request->get('name');
        $store_id = $request->get('store_id');
        //校验参数
        if ($phone == "" || $password == "" || $store_id == "" || $name == "") {
            return json_encode([
                'status' => 0,
                'msg' => '参数不能为空'
            ]);
        }
        //验证手机号
        if (!preg_match("/^1[34578]{1}\d{9}$/", $phone)) {
            return json_encode([
                'status' => 0,
                'msg' => '手机号码不正确'
            ]);
        }

        $data = $request->all();
        $rules = [
            'phone' => 'required|min:11|max:11|unique:merchants',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return json_encode([
                'status' => 0,
                'msg' => '账号已注册'
            ]);
        }
        //验证密码
        if (strlen($password) < 6) {
            return json_encode([
                'status' => 0,
                'msg' => '密码长度不符合要求'
            ]);
        }
        $type = substr($store_id, 0, 1);
        $dataIn = [
            'name' => $name,
            'email' => '',
            'phone' => $phone,
            'password' => bcrypt($password),
        ];
        $store = "";
        if ($type == 'p') {
            $store_type = 'pingan';
            $desc_pay = "平安通道";
            $store = PinganStore::where('external_id', $store_id)->first();
        }
        if ($type == 'o') {
            $store_type = 'oalipay';
            $desc_pay = "支付宝当面付";
            $store = AlipayAppOauthUsers::where('store_id', $store_id)->first();

        }
        if ($type == 's') {
            $store_type = 'salipay';
            $desc_pay = "支付宝口碑";
            $store = AlipayShopLists::where('store_id', $store_id)->first();

        }
        if ($type == 'w') {
            $store_type = 'weixin';
            $desc_pay = "微信支付";
            $store = WeixinShopList::where('store_id', $store_id)->first();

        }
        if ($type == 'f') {
            $store_type = 'pufa';
            $desc_pay = "浦发通道";
            $store = PufaStores::where('store_id', $store_id)->first();
        }
        if ($type == 'u') {
            $store_type = 'unionpay';
            $desc_pay = "银联";
            $store = UnionPayStore::where('store_id', $store_id)->first();
        }
        if ($type == 'm') {
            $store_type = 'minsheng';
            $desc_pay = "民生";
            $store = MsStore::where('store_id', $store_id)->first();
        }

        //判断store_id是否存在
        if (!$store) {
            return json_encode([
                'status' => 0,
                'msg' => '店铺store_id不存在'
            ]);
        }
        $m = MerchantShops::where('store_id', $store_id)->first();//如果绑定了说明有店长了
        if ($m) {
            $dataIn['type'] = 1;
            $dataIn['pid'] = $m->merchant_id;//收银员的pid 是店长的ID
        }
        try {
            $m = Merchant::create($dataIn);
            $mid = $m->id;
            MerchantShops::create([
                'merchant_id' => $mid,
                'store_id' => $store_id,
                'store_type' => $store_type,
                'desc_pay' => $desc_pay,
            ]);
            return json_encode([
                'status' => 1,
                'msg' => '收银员添加成功'
            ]);
        } catch (\Exception $exception) {
            Log::info($exception);
            return json_encode([
                'status' => 0,
                'msg' => '系统异常'
            ]);
        }
    }
}