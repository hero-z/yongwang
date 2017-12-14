<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/7
 * Time: 13:29
 */

namespace App\Http\Controllers\Merchant;


use App\Models\MerchantPayWay;
use App\Models\MerchantShops;
use Illuminate\Http\Request;

class PayWaysController extends BaseController
{
    //设置通道
    public function setWays()
    {
        $m_id = auth()->guard('merchant')->user()->id;
        $merchant = '';
        $m="";
        try {
            $merchant = MerchantShops::where('merchant_id', $m_id)->get();//商户信息
            if ($merchant) {
                $merchant = $merchant->toArray();
            }
        } catch (\Exception $exception) {

        }
        try {
            $m = MerchantPayWay::where('merchant_id', $m_id)->get();
            if ($m) {
                $m = $m->toArray();
            }
        } catch (\Exception $exception) {

        }

        return view('merchant.setWays.set', compact('merchant','m'));

    }

    //设置通道提交
    public function setWaysPost(Request $request)
    {
        $data = $request->except(['_token']);
        if ($data['alipay'] == "" && $data['weixin'] == "" && $data['jd'] == "") {
            return json_encode(['status' => 0, 'msg' => '保存失败']);
        }
        $data['merchant_id'] = auth()->guard('merchant')->user()->id;
        try {
            $m = MerchantPayWay::where('merchant_id', $data['merchant_id'])->first();
            if ($m) {
                MerchantPayWay::where('merchant_id', $data['merchant_id'])->update($data);
            } else {
                MerchantPayWay::create($data);
            }
        } catch (\Exception $exception) {
            return json_encode(['status' => 0, 'msg' => '保存失败']);
        }
        return json_encode(['status' => 1, 'msg' => '保存成功']);
    }
}