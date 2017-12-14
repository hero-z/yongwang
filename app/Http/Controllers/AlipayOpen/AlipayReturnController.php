<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/2/14
 * Time: 18:29
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayShopLists;
use App\Models\AlipayStoreInfo;
use App\Models\WeixinPayNotify;
use Illuminate\Http\Request;

class AlipayReturnController extends AlipayOpenController
{


    public function shopNotify(Request $request)
    {
        $data = [];
        $info = AlipayStoreInfo::where('store_id', $request->get('store_id'))->first();
        if ($info) {
            $data = $info->toArray();
        }
        return json_encode($data);

    }

    public function setWxNotify(Request $request)
    {
        $set_type = $request->get('set_type');
        $id = $request->get('id');
        //当面付
        if ($set_type == "oskm") {
            $store = AlipayAppOauthUsers::where('id', $id)->first();
            if (!$store->auth_shop_name) {
                $store_name = $store->user_id . '_店铺';
                $store_id = 'o' . $store->user_id;
            } else {
                $store_name = $store->auth_shop_name;
                $store_id = 'o' . $store->user_id;
            }
        }
        //门店收款
        if ($set_type == 'skm') {

            $store = AlipayShopLists::where('id', $id)->first();
            if (!$store->main_shop_name) {
                $store_name = $store->store_id . '_店铺';
                $store_id = $store->store_id;
            } else {
                $store_name = $store->main_shop_name;
                $store_id = $store->store_id;
            }
        }

        $WxPayNotify = WeixinPayNotify::where('store_id', $store_id)->first();
        return view('admin.alipayopen.config.setWxNotify', compact('set_type', 'store_id', 'store_name', 'WxPayNotify'));

    }

    public function setWxNotifyPost(Request $request)
    {
        $store_id = $request->get('store_id');
        $re = WeixinPayNotify::where('store_id', $store_id)->first();
        $data = $request->except(['_token']);
        try {
            if ($re) {
                WeixinPayNotify::where('store_id',$store_id)->update($data);
            } else {
                WeixinPayNotify::create($data);
            }
        } catch (\Exception $exception) {
            $status = [
                'status' => 0,
            ];
            return $exception;

        }
        $status = [
            'status' => 1
        ];
        return json_encode($status);
    }

}