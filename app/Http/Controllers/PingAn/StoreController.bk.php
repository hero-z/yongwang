<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/1/17
 * Time: 23:08
 */

namespace App\Http\Controllers\PingAn;

use Alipayopen\Sdk\Request\AlipayOfflineMarketShopCreateRequest;
use App\Models\MerchantShops;
use App\Models\PingancqrLsits;
use App\Models\PingancqrLsitsinfo;
use App\Models\PinganStore;
use App\Models\PinganStoreInfos;
use App\Models\ProvinceCity;
use App\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreController extends BaseController

{

    public function index(Request $request)
    {

        $auth = Auth::user()->can('pinganstore');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $data = DB::table("pingan_stores")
            ->join("users", "pingan_stores.user_id", "=", "users.id")
            ->select("pingan_stores.*", "users.name")
            ->where('user_id', Auth::user()->id)
            ->where('pingan_stores.is_delete', 0)
            ->where('pingan_stores.pid', 0)
            ->orderBy('created_at', 'desc')
            ->get();
        if (Auth::user()->hasRole('admin')) {
            $data = DB::table("pingan_stores")
                ->join("users", "pingan_stores.user_id", "=", "users.id")
                ->select("pingan_stores.*", "users.name")
                ->where('pingan_stores.is_delete', 0)
                ->where('pingan_stores.pid', 0)
                ->orderBy('created_at', 'desc')
                ->get();
        }
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
        return view('admin.pingan.store.index', compact('datapage', 'paginator'));

    }

    //商户列表搜索
    public function pinganSearch(Request $request)
    {
        $sp = $request->input("shopname");
        $auth = Auth::user()->can('pinganstore');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }


        $data = DB::table("pingan_stores")
            ->join("users", "pingan_stores.user_id", "=", "users.id")
            ->select("pingan_stores.*", "users.name")
            ->where('user_id', Auth::user()->id)
            ->where("pingan_stores.alias_name", "like", "%" . $sp . "%")
            ->where('pingan_stores.is_delete', 0)
            ->where('pingan_stores.pid', 0)
            ->orderBy('created_at', 'desc')
            ->get();
        if (Auth::user()->hasRole('admin')) {
            $data = DB::table("pingan_stores")
                ->join("users", "pingan_stores.user_id", "=", "users.id")
                ->select("pingan_stores.*", "users.name")
                ->where('pingan_stores.is_delete', 0)
                ->where('pingan_stores.pid', 0)
                ->where("pingan_stores.alias_name", "like", "%" . $sp . "%")
                ->orderBy('created_at', 'desc')
                ->get();
        }
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

        return view('admin.pingan.store.index', compact('datapage', 'paginator'));
    }

    //加载还原页
    public function pinganRestore()
    {
        $auth = Auth::user()->can('pinganRestore');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $data = PinganStore::where('user_id', Auth::user()->id)->where('is_delete', 1)->orderBy('created_at', 'desc')->paginate(8);
        if (Auth::user()->hasRole('admin')) {
            $data = PinganStore::where('is_delete', 1)->orderBy('created_at', 'desc')->paginate(8);
        }
        //dd($data);
        return view("admin.pingan.store.pinganRestore", ['data' => $data]);
    }

    //搜索还原
    public function pinganRestoreSearch(Request $request)
    {
        $sp = $request->input("shopname");
        $data = PinganStore::where('user_id', Auth::user()->id)->where('is_delete', 1)->where("pingan_stores.alias_name", "like", "%" . $sp . "%")->orderBy('created_at', 'desc')->paginate(8);
        //dd($data);
        return view("admin.pingan.store.pinganRestore", ['data' => $data]);
    }

//还原选中
    public function pinganRestoree(Request $request)
    {
        $s = $request->get("data");
        //dd($s);
        $data['is_delete'] = 0;
        if ($s) {
            foreach ($s as $v) {
                DB::table("pingan_stores")->where("id", $v)->update($data);
            }
        }

        return redirect("/admin/pingan/pinganRestore");
    }

    //单个还原
    public function pinganRestoreee(Request $request)
    {
        $id = $request->id;
        $data['is_delete'] = 0;
        if (DB::table("pingan_stores")->where("id", $id)->update($data)) {
            return redirect("/admin/pingan/pinganRestore");
        }
    }

    //彻底删除
    public function pinganDelete(Request $request)
    {
        $auth = Auth::user()->can('pinganDelete');
        if (!$auth) {
            return json_encode(['success' => 0]);
        } else {
            $id = $request->get("id");
            try {
                $data = PinganStore::where('id', $id)->first();
                if (PinganStore::where("id", $id)->delete()) {
                    return json_encode(['success' => 1]);
                }
            } catch (\Exception $exception) {
                return json_encode(['success' => 0]);
            }
        }

    }

    public function editPingan(Request $request)
    {
        $id = $request->get("id");
        $list = PinganStore::where("id", $id)->first();
        $info = DB::table("pingancqr_lsitsinfos")->where("store_id", $list->external_id)->first();
        // dd($info);
        return view("admin.pingan.store.editPingan", ['list' => $list, "info" => $info]);
    }

    public function upPingan(Request $request)
    {
        $info = '保存失败!';
        try {
            $id = $request->get("id");
            $list = DB::table("pingancqr_lsitsinfos")->where("id", $id)->first();
            $codes = $request->get("codes");
            $name = $request->get("name");
            $wxappid = $request->get("wx_app_id");
            $wxsecret = $request->get("wx_secret");
            $data['alias_name'] = $name;
            $data['wx_app_id'] = $wxappid;
            $data['wx_secret'] = $wxsecret;
            $datas['store_name'] = $name;
            $datas['updated_at'] = date('Y-m-d H:i:s');


            if (PinganStore::where("external_id", $list->store_id)->update($data)) {
                if ($list->code_number == $codes) {
                    if (DB::table("pingancqr_lsitsinfos")->where("id", $id)->update($datas))
                        return redirect("/admin/pingan/index");
                    else
                        $info .= '-222';
                } else {
                    if (DB::table("pingancqr_lsitsinfos")->where("code_number", $codes)->where("code_type", 0)->first()) {
                        $datass['store_id'] = $list->store_id;
                        $datass['store_name'] = $name;
                        $datass['from_info'] = 'pingan';
                        $datass['code_type'] = 1;
                        if (DB::table("pingancqr_lsitsinfos")->where("code_number", $codes)->update($datass)) {
                            if (DB::table("pingancqr_lsitsinfos")->where("id", $id)->delete()) {
                                return redirect("/admin/pingan/index");
                            } else {
                                $info .= '原code删除失败';
                            }
                        } else {
                            $info .= '-333';
                        }
                    } else {
                        $info .= '收款码编号不存在或已经被占用';
                    }
                }
            } else {
                $info .= '-111';
            }
            return back()->with("warnning", $info);
            /* if ($list->code_number == $codes) {
                 if (PinganStore::where("external_id", $list->store_id)->update($data) && DB::table("pingancqr_lsitsinfos")->where("id", $id)->update($datas)) {
                     return redirect("/admin/pingan/index");
                 } else {
                     return back()->with("warnning", "保存失败");
                 }
             } else {
                 if (DB::table("pingancqr_lsitsinfos")->where("code_number", $codes)->where("code_type", 0)->first()) {
                     $datass['store_id'] = $list->store_id;
                     $datass['store_name'] = $name;
                     $datass['code_type'] = 1;
                     if (DB::table("pingancqr_lsitsinfos")->where("code_number", $codes)->update($datass)) {
                         if (DB::table("pingancqr_lsitsinfos")->where("id", $id)->delete()) {
                             return redirect("/admin/pingan/index");
                         }
                     }
                 } else {
                     return back()->with("warnning", "保存失败,收款码编号不存在或已经被占用");
                 }
             }*/
        } catch (\Exception $e) {
            Log::info($e);
            return back()->with("warnning", $info);
        }


    }

    public function add()
    {
        $auth = Auth::user()->can('pinganstore');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        return view('admin.pingan.store.add');
    }

    public function addPost(Request $request)
    {
        $auth = Auth::user()->can('pinganstore');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $store = $request->except('_token');
        $aop = $this->AopClient();
        $aop->method = "fshows.liquidation.submerchant.create.with.auth";
        $data = array('content' => json_encode($store));
        $response = $aop->execute($data);
        $responseArray = json_decode($response, true);
        if ($responseArray['success']) {

            $updateData['external_id'] = $request->external_id;
            $updateData['sfzname'] = $request->id_card_name;
            $updateData['sfzno'] = $request->id_card_num;
            $updateData['address'] = $request->store_address;
            $updateData['sfz3'] = $request->id_card_hand_img_url;
            $updateData['main_image'] = $request->store_front_img_url;
            $updateData['sub_status'] = 1;
            $storeinfos = PinganStoreInfos::where('external_id', $store['external_id'])->first();
            if ($storeinfos) {
                if (!$storeinfos->sub_status) {
                    PinganStoreInfos::where('external_id', $store['external_id'])->update($updateData);
                } else {
                    return json_encode([
                        'success' => false,
                        'error_message' => '请勿重复提交！'
                    ]);
                }
            } else {
                PinganStoreInfos::create($updateData);
                PinganStoreInfos::where('external_id', $store['external_id'])->update($updateData);
            }
            $storeUpdateData = $request->except('_token', 'id_card_name', 'id_card_num', 'store_address', 'id_card_hand_img_url', 'store_front_img_url');
            $storeUpdateData['user_id'] = Auth::user()->id;
            $storeUpdateData['user_name'] = Auth::user()->name;
            $storeUpdateData['sub_merchant_id'] = $responseArray['return_value']['sub_merchant_id'];
            $storeinfo = PinganStore::where('external_id', $store['external_id'])->first();
            if ($storeinfo) {
                PinganStore::where('external_id', $store['external_id'])->update($storeUpdateData);
            } else {
                PinganStore::create($storeUpdateData);
                PinganStore::where('external_id', $store['external_id'])->update($storeUpdateData);
            }
        }
        return $response;
    }

    public function DelPinanStore(Request $request)
    {
        $auth = Auth::user()->can('DelPingan');
        if (!$auth) {
            return json_encode([
                'success' => 0,
                "erro_message" => "您没有权限操作!"
            ]);
        }
        $id = $request->get('id');
//        try {
//            PinganStore::where('external_id', $external_id)->update($content);//修改商户信息
//        } catch (\Exception $exception) {
//            return json_encode([
//                'success' => 0,
//                'error_message' => '修改商户信息保存失败！'
//            ]);
//        }
        $list = PinganStore::where("id", $id)->first();
        if (DB::table("merchant_shops")->where("store_id", $list->external_id)->first()) {
            return json_encode([
                'success' => 0,
                "erro_message" => "请先解除店铺绑定!"
            ]);
        } else {
            PinganStore::where('id', $id)->update(['is_delete' => 1]);
            return json_encode(['success' => 1]);
        }
    }

    public function SetStore(Request $request)
    {
        $auth = Auth::user()->can('setStore');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $id = $request->get('id');
        $store = PinganStore::where('id', $id)->first();
        return view('admin.pingan.store.set', compact('store'));
    }

    public function SetStorePost(Request $request)
    {
        $aop = $this->AopClient();
        $aop->method = 'fshows.liquidation.submerchant.bank.bind';
        $store = PinganStore::where('id', $request->get('id'))->first();
        if ($request->get('is_public_account') == 1) {
            $content = $request->except(['_token', 'id', 'merchant_rate']);
        } else {
            $content = $request->except(['_token', 'id', 'merchant_rate', 'is_public_account', 'open_bank']);
        }
        $content['sub_merchant_id'] = $store->sub_merchant_id;
        $data = array('content' => json_encode($content));
        $response = $aop->execute($data);
        $responseArray = json_decode($response, true);
        if ($responseArray['success']) {//绑卡成功
            PinganStore::where('id', $request->get('id'))->update($content);
        }
        return $response;

    }

    public function setMerchantRate(Request $request)
    {
        $id = $request->get('id');
        $store = PinganStore::where('id', $id)->first();

        $aop = $this->AopClient();
        $aop->method = 'fshows.liquidation.submerchant.rate.query';
        $con = [
            'external_id' => $store->external_id,

        ];
        $data = array('content' => json_encode($con));
        $response = $aop->execute($data);
        $responseArray = json_decode($response, true);
        if ($responseArray['success']) {
            $m = $responseArray['return_value']['merchant_rate'];
        } else {
            $m = $responseArray['error_message'];
        }
        return view('admin.pingan.store.setM', compact('store', 'm'));

    }

    public function setMerchantRatePost(Request $request)
    {
        //设置费率
        $merchant_rate = $request->get('merchant_rate');
        $id = $request->get('id');
        $store = PinganStore::where('id', $id)->first();
        $aop = $this->AopClient();
        $aop->method = 'fshows.liquidation.submerchant.rate.set';
       //设置所有的费率
        for ($i = 5; $i > 0; $i--) {
            $con = [
                'sub_merchant_id' => $store->sub_merchant_id,
                'merchant_rate' => $merchant_rate,
                'type' => $i,
            ];
            $data = array('content' => json_encode($con));
            $response = $aop->execute($data);
            $responseArray = json_decode($response, true);
        }
        if ($responseArray['success']) {//绑卡成功
            $con1 = [
                'sub_merchant_id' => $store->sub_merchant_id,
                'merchant_rate' => $merchant_rate,
            ];
            PinganStore::where('id', $request->get('id'))->update($con1);
        }
        return $response;
    }

    public function PingAnStoreQR()
    {
        $code_url = url('admin/pingan/autoStore?user_id=' . Auth::user()->id);
        return view('admin.pingan.store.myqr', compact('code_url'));
    }

    //提交店铺信息
    public function autoStore(Request $request)
    {
        $merchant_id = auth()->guard('merchant')->user()->id;
        $merchantShop = MerchantShops::where('merchant_id', $merchant_id)->where('store_type', 'pingan')->first();
        if ($merchantShop) {
            return view('admin.pingan.store.merchantError');
        }

        $pay_type = "other";
        //判断是不是微信
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $pay_type = 'weixin';
        }
        //判断是不是支付宝
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $pay_type = 'alipay';
        }

        if ($pay_type == "other") {

            return '请用支付宝或者微信扫描二维码';

        }
        //显示推广员信息
        $user_id = User::where('id', $request->get('user_id'))->first();
        if ($user_id) {
            $user_name = $user_id->name;
        } else {
            $user_name = '';
        }
        //获取省份
        $provincelists = ProvinceCity::where('areaParentId', 1)->select('areaCode', 'areaName')->get();
        return view('admin.pingan.store.autostore', compact('user_name', 'provincelists'));
    }

    //自主提交到后台保存 第一步提交

    public function autoStorePost(Request $request)
    {
        $store = $request->except(['_token', 'user_id', 'code_number', 'sfz1', 'sfz2', 'orther1', 'province_code', 'city_code', 'district_code']);
        //检查系统店铺是否存在
        $s = PinganStore::where('alias_name', $store['alias_name'])->first();
        if ($s) {
            return json_encode([
                'success' => false,
                'error_message' => '店铺已经存在！请联系服务商'
            ]);
        }
        if ($request->input('user_id', 1)) {
            //检查用户是否存在
            $u = User::where('id', $request->input('user_id'))->first();
            if (!$u) {
                return json_encode([
                    'success' => false,
                    'error_message' => '推广员不存在'
                ]);
            }
        }
        $store['contact_name'] = $request->id_card_name;
        $store['contact_phone'] = $request->service_phone;
        $store['contact_mobile'] = $request->service_phone;
        $store['contact_email'] = $request->service_phone . '@163.com';
        $aop = $this->AopClient();
        $aop->method = "fshows.liquidation.submerchant.create.with.auth";
        $data = array('content' => json_encode($store));
        try {
            $response = $aop->execute($data);
            $responseArray = json_decode($response, true);
        } catch (\Exception $exception) {
            return '系统超时！请刷新再试';
        }
        if ($responseArray['success']) {

            $updateData['external_id'] = $request->external_id;
            $updateData['sfzname'] = $request->id_card_name;
            $updateData['sfzno'] = $request->id_card_num;
            $updateData['address'] = $request->store_address;
            $updateData['sfz1'] = $request->sfz1;
            $updateData['sfz2'] = $request->sfz2;
            $updateData['sfz3'] = $request->id_card_hand_img_url;
            $updateData['main_image'] = $request->store_front_img_url;
            $updateData['licence'] = $request->business_license_img_url;
            $updateData['orther1'] = $request->orther1;
            $updateData['province_code'] = $request->province_code;
            $updateData['city_code'] = $request->city_code;
            $updateData['district_code'] = $request->district_code;
            $updateData['sub_status'] = $responseArray['return_value']['status'];
            $storeinfos = PinganStoreInfos::where('external_id', $store['external_id'])->first();
            if ($storeinfos) {
                if (!$storeinfos->sub_status) {
                    PinganStoreInfos::where('external_id', $store['external_id'])->update($updateData);
                } else {
                    return json_encode([
                        'success' => false,
                        'error_message' => '请勿重复提交！'
                    ]);
                }
            } else {
                PinganStoreInfos::create($updateData);
                PinganStoreInfos::where('external_id', $store['external_id'])->update($updateData);
            }
            $storeUpdateData = $request->only('external_id', 'name', 'alias_name', 'service_phone', 'category_id');
            $storeUpdateData['user_id'] = $request->get('user_id', 1);
            $storeUpdateData['user_name'] = User::where('id', $request->get('user_id', 1))->first()->name;;
            $storeUpdateData['sub_merchant_id'] = $responseArray['return_value']['sub_merchant_id'];
            $storeUpdateData['contact_name'] = $request->id_card_name;
            $storeUpdateData['contact_phone'] = $request->service_phone;
            $storeUpdateData['contact_mobile'] = $request->service_phone;
            $storeUpdateData['contact_email'] = $request->service_phone . '@163.com';
            $storeinfo = PinganStore::where('external_id', $store['external_id'])->first();
            if ($storeinfo) {
                PinganStore::where('external_id', $store['external_id'])->update($storeUpdateData);
            } else {
                PinganStore::create($storeUpdateData);
                PinganStore::where('external_id', $store['external_id'])->update($storeUpdateData);
            }
            try {
                //修改二维码为商户收款码
                PingancqrLsitsinfo::where('code_number', $request->code_number)->update([
                    'store_id' => $request->external_id,
                    'code_type' => 1,
                    'store_name' => $request->alias_name
                ]);
            } catch (\Exception $exception) {
                return json_encode([
                    'success' => 0,
                    'error_message' => '修改二维码状态失败！'
                ]);
            }
            try {
                //关联商户id
                $m_id = auth()->guard('merchant')->user()->id;
                MerchantShops::create([
                    'merchant_id' => $m_id,
                    'store_id' => $request->external_id,
                    'store_name' => $request->alias_name,
                    'store_type' => 'pingan',
                    'desc_pay' => '平安通道',
                    'status' => 1
                ]);
            } catch (\Exception $exception) {
                return json_encode([
                    'success' => 0,
                    'error_message' => '关联商户id保存失败！'
                ]);
            }
        }


        return $response;
    }

//用户自主绑定银行卡 第二步 页面
    public function autom()
    {
        $pay_type = "other";
        //判断是不是微信
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $pay_type = 'weixin';
        }
        //判断是不是支付宝
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $pay_type = 'alipay';
        }

        if ($pay_type == "other") {

            return '请用支付宝或者微信扫描二维码';

        }

        return view('admin.pingan.store.autom');
    }

    //提交绑定银行卡
    public function automPost(Request $request)
    {
        $external_id = $request->get('external_id');
        $code_number = $request->get('$code_number');
        $aop = $this->AopClient();
        $aop->method = 'fshows.liquidation.submerchant.bank.bind';
        if ($request->get('is_public_account') == 1) {
            $content = [
                'is_public_account' => 1,
                'open_bank' => $request->get('open_bank')
            ];
        }
        $store = PinganStore::where('external_id', $external_id)->first();
        $content['sub_merchant_id'] = $store->sub_merchant_id;
        $content['bank_card_no'] = $request->get('bank_card_no');
        $content['card_holder'] = $request->get('card_holder');
        $data = array('content' => json_encode($content));
        $response = $aop->execute($data);
        $responseArray = json_decode($response, true);
        if ($responseArray['success']) {//绑卡成功
            try {
                PinganStore::where('external_id', $external_id)->update($content);//修改商户信息
            } catch (\Exception $exception) {
                return json_encode([
                    'success' => 0,
                    'error_message' => '修改商户信息保存失败！'
                ]);
            }
        }

        return $response;
    }

    //第三步上传资质文件

    public function autoFile(Request $request)
    {
        $pay_type = "other";
        //判断是不是微信
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $pay_type = 'weixin';
        }
        //判断是不是支付宝
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $pay_type = 'alipay';
        }

        if ($pay_type == "other") {

//            return '请用支付宝或者微信扫描二维码';


        }
        try {
            $StoreInfos = PinganStoreInfos::where('external_id', $request->get('external_id'))->first();
            if ($StoreInfos) {
                $StoreInfos = $StoreInfos->toArray();
                //获取省份
                $provincelists = ProvinceCity::where('areaParentId', 1)->select('areaCode', 'areaName')->get();
                return view('admin.pingan.store.autoFileTwo', compact('StoreInfos', 'provincelists'));
            } else {
                return view('admin.pingan.store.autoFile');
            }
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    public function autoFilePost(Request $request)
    {
        $external_id = $request->get('external_id');
        $code_number = $request->get('code_number');
        $sfzno = $request->get('sfzno');
        $sfzname = $request->get('sfzname');
        $sfz3 = $request->get('sfz3');
        $address = $request->get('address');
        $main_image = $request->get('main_image');
        $licence = $request->get('licence');
        $province = $request->get('province');
        $city = $request->get('city');
        $district = $request->get('district');
        $PinganStore = PinganStore::where('external_id', $external_id)->first();
        if ($PinganStore) {
            $pInfo = PinganStoreInfos::where('external_id', $external_id)->first();
            if (isset($pInfo->sub_status) && $pInfo->sub_status == 1) {
                if (!$pInfo->province_code || !$pInfo->city_code || !$pInfo->district_code || !$pInfo->licence) {
                } else {
                    return json_encode([
                        'success' => 0,
                        'error_message' => '资料已存在,无需补交'
                    ]);
                }
            }
            $sub_merchant_id = $PinganStore->sub_merchant_id;
            $aop = $this->AopClient();
            $aop->method = "fshows.liquidation.submerchant.authInfo.submit";
            $authinfo = [
                'sub_merchant_id' => $sub_merchant_id,
                'external_id' => $external_id,
                'id_card_name' => $sfzname,
                'id_card_num' => $sfzno,
                'store_address' => $address,
                'id_card_hand_img_url' => $sfz3,
                'store_front_img_url' => $main_image,
                'province' => $province,
                'city' => $city,
                'district' => $district
            ];
            if ($licence) {
                $authinfo['business_license_img_url'] = $licence;
            }
            $data = array('content' => json_encode($authinfo));
            try {
                //商户材料补交到平安
                $response = $aop->execute($data);
                $responseArray = json_decode($response, true);
//                dd($responseArray);
                //保存数据库
                if ($responseArray['success']) {
                    $updatedata = $request->except('_token', 'code_number', 'province', 'city', 'district');
                    if ($responseArray['return_value']['result_code'] == 'SUCCESS') {
                        $updatedata['sub_status'] = 1;
                    }
                    try {
                        $pInfo = PinganStoreInfos::where('external_id', $external_id)->first();
                        if ($pInfo) {
                            PinganStoreInfos::where('external_id', $external_id)->update($updatedata);
                        } else {
                            PinganStoreInfos::create($updatedata);
                            PinganStoreInfos::where('external_id', $external_id)->update($updatedata);
                        }
                    } catch (\Exception $exception) {
                        Log::info($exception);
                        return json_encode([
                            'success' => 0,
                            'error_message' => '200信息保存失败！'
                        ]);
                    }
                } else {
                    return json_encode([
                        'success' => 0,
                        'error_message' => $responseArray['error_code'] . ':' . $responseArray['error_message']
                    ]);
                }

            } catch (\Exception $exception) {
                Log::info($exception);
                return json_encode([
                    'success' => 0,
                    'error_message' => '补交至平安银行失败！' . info($exception)
                ]);
            }

            try {
                //修改二维码为商户收款码
                PingancqrLsitsinfo::where('code_number', $code_number)->update([
                    'store_id' => $external_id,
                    'code_type' => 1,
                    'store_name' => $PinganStore->alias_name
                ]);
            } catch (\Exception $exception) {
                return json_encode([
                    'success' => 0,
                    'error_message' => '2001保存失败！'
                ]);
            }
            /*  try {
                  //修改已使用数量
                  $s_sum = DB::table('pingancqr_lsitsinfos')->where('cno', $pInfo->cno)->where('code_type', 1)->count();
                  PingancqrLsits::where('cno', $pInfo->cno)->update([
                      's_num' => $s_sum,
                  ]);

              } catch (\Exception $exception) {
                  return json_encode([
                      'success' => 0,
                      'error_message' => '2002保存失败！'
                  ]);
              }*/
        }
        return json_encode([
            'success' => 1,
        ]);

    }

    public function success()
    {
        return view('admin.pingan.store.success');
    }

    public function OrderQuery(Request $request)
    {
        //所有收银员
        $cashier = DB::table("merchants")->select("name", "id")->get();
        foreach ($cashier as $v) {
            $cashier[$v->id] = $v->name;
        }
        $array = [301, 302, 303, 304, 305, 306, 307];
        if (Auth::user()->hasRole('admin')) {

            //平安扫码枪
            $data = DB::table("orders")
                ->join("pingan_stores", "orders.store_id", "=", "pingan_stores.external_id")
                ->whereIn("orders.type", $array)
                ->select("orders.remark", 'orders.out_trade_no', 'orders.trade_no', "orders.store_id", 'pingan_stores.alias_name', "orders.created_at", "orders.updated_at", "orders.total_amount", "orders.type", "orders.pay_status", "orders.merchant_id")
                ->orderBy("orders.created_at", "desc")
                ->get()
                ->toArray();
        } else {
            //平安扫码枪
            $data = DB::table("orders")
                ->join("pingan_stores", "orders.store_id", "=", "pingan_stores.external_id")
                ->whereIn("orders.type", $array)
                ->where("pingan_stores.user_id", auth()->user()->id)
                ->select("orders.remark", 'orders.out_trade_no', 'orders.trade_no', "orders.store_id", 'pingan_stores.alias_name', "orders.created_at", "orders.updated_at", "orders.total_amount", "orders.type", "orders.pay_status", "orders.merchant_id")
                ->orderBy("orders.created_at", "desc")
                ->get()
                ->toArray();

        }
        //非数据库模型自定义分页
        $perPage = 8;//每页数量
        if ($request->has('page')) {
            $current_page = $request->input('page');
            $current_page = $current_page <= 0 ? 1 : $current_page;
        } else {
            $current_page = 1;
        }
        $item = array_slice($data, ($current_page - 1) * $perPage, $perPage); //注释1
        $total = count($data);
        //dd($total);
        $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
        $datapage = $paginator->toArray()['data'];
        //  dd($paginator);
        return view('admin.pingan.store.order', compact('datapage', 'paginator', "cashier"));
    }

    //店铺收款状态
    public function PayStatus(Request $request)
    {

        $auth = Auth::user()->can('payStatus');
        if (!$auth) {
            return json_encode([
                'success' => 0,
            ]);
        }
        $type = $request->get('type');
        try {
            PinganStore::where('id', $request->get('id'))->update([
                'pay_status' => $type
            ]);
        } catch (\Exception $exception) {
            return json_encode([
                'success' => 0,
            ]);
        }
        return json_encode([
            'success' => 1,
        ]);

    }

    //商户资料
    public function MerchantFile(Request $request)
    {
        $id = $request->get('id');//平安店铺id
        try {
            $files = PinganStoreInfos::where('external_id', $id)->first();//店铺资料
            if ($files) {
                $files->toArray();
            } else {
                $files = '';
            }
        } catch (\Exception $exception) {
            $files = '';
        }
        try {
            if ($p = PingancqrLsitsinfo::where('store_id', $id)->first()) {
                $code_number = $p->code_number;
            } else {
                $store = PinganStore::where('external_id', $id)->first();
                $user_id = $store->user_id;
                $user_name = $store->user_name;
                $code_number = time() . rand(10000, 99999);
                PingancqrLsitsinfo::create([
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'code_number' => $code_number,
                    'code_type' => 1,
                    'store_id' => $id,
                    'store_name' => $store->alias_name,
                    'from_info' => 'pingan',
                ]);
            }
        } catch (\Exception $exception) {
            return $exception;
        }
        $code_url = url('admin/pingan/autoFile?external_id=' . $id . '&code_number=' . $code_number);

        return view('admin.pingan.store.merchantFile', compact('id', 'files', 'code_url'));


    }

    //平安费率实时查询
    public function PingAnRate(Request $request)
    {
        $store_id = $request->get('store_id');
        $aop = $this->AopClient();
        $aop->method = 'fshows.liquidation.submerchant.rate.query';
        $con = [
            'external_id' => $store_id,
            'type'=>1,

        ];
        $data = array('content' => json_encode($con));
        $response = $aop->execute($data);
        return $response;
    }

    //获取城市区,乡县数据
    public function getcitycountydata(Request $request)
    {
        $result = [];
        $parentid = $request->id;
        if ($parentid)
            $result = ProvinceCity::where('areaParentid', $parentid)->select('areaCode', 'areaName')->get();
        return json_encode($result);
    }

}