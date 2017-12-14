<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/4/15
 * Time: 11:26
 */

namespace App\Http\Controllers\UnionPay;


use App\Models\MerchantShops;
use App\Models\UnionPayStore;
use App\Models\UnionPayStoreCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreController extends BaseController
{

    public function index(Request $request)
    {
        $shopname=$request->get("shopname");
        $where=[];
        if($shopname){
            $where[]=['union_pay_stores.alias_name','like',"%".$shopname."%"];
        }
        if (Auth::user()->hasRole("admin")) {

            $store = DB::table("union_pay_stores")
                ->join("users", "union_pay_stores.user_id", "=", "users.id")
                ->where("union_pay_stores.is_delete",0)
                ->where($where)
                ->where('union_pay_stores.pid',0)
                ->select("union_pay_stores.*", "users.name")
                ->orderBy("union_pay_stores.created_at", "desc")
                ->paginate(8);
 
        } else {
            $store = DB::table("union_pay_stores")
                ->join("users", "union_pay_stores.user_id", "=", "users.id")
                ->select("union_pay_stores.*", "users.name")
                ->where("user_id", Auth::user()->id)
                ->where('union_pay_stores.pid',0)
                ->where("union_pay_stores.is_delete",0)
                ->where($where)
                ->orderBy("union_pay_stores.created_at", "desc")
                ->paginate(8);
        }
        return view("admin.UnionPay.store.index", compact("store"));
    }



    public function create()
    {

        return view("admin.UnionPay.store.create");

    }

//店铺提交
    public function store(Request $request)
    {
        $aop = $this->AopClient();
        $aop->method = "fshows.paycompany.liquidation.merchant.unionpay.create";
        $store = [
            "merchant_name" => $request->get('merchant_name'),// string 是 32 商户名称
            "alias_name" => $request->get('alias_name'),// string 是 8 商户简称
            "province_code" => $request->get('province_code'),// string 是 32 省份代码（由服务商根据附录中的省市代码自行匹配）
            "city_code" => $request->get('city_code'), //是 32 城市代码（由服务商根据附录中的省市代码自行匹配）
            "is_t0" => (int)$request->get('is_t0'),// int 否 1 1是T0商户， 0或不传为不是T0商户
            "address" => $request->get('address'),// string 是 120 商户详细地址
            "telephone" => $request->get('telephone'), //string 是 20 商户电话
            "email" => $request->get('email'), //string 是 30 商户邮箱
            "manager" => $request->get('manager'),// string 是 20 商户负责人
            "manager_phone" => $request->get('manager_phone'),// string 是 20 商户负责人手机号
            "manager_id_card" => $request->get('manager_id_card'), //string 是 20 商户负责人身份证号
            "manager_id_card_img" => $request->get('manager_id_card_img'), //string 是 - 商户负责人手持身份证照片地址
            "store_img" => $request->get('store_img'), //string 是 - 商户门头照照片地址
            //   "legal_man" => "赵云", //string 否 20 商户法人
            //   "service_telephone" => "02568046800",// string 否 20 商户客服电话
            "business_licence_img" => $request->get('business_licence_img')// string 否 - 营业执照照片地址
        ];
        $insert = $store;
        $insert['store_id'] = $request->get('out_merchant_id');
        $insert['user_id'] = $request->get('user_id', 6);
        $insert['district_code'] = $request->get('district_code');
        $insert['local_image'] = ''.$request->get('manager_id_card_img_local').';'.$request->get('store_img_local').';'.$request->get('business_licence_img_local');
        $store['out_merchant_id'] = $request->get('out_merchant_id');
        $data = array("content" => json_encode($store));
        try {
            $response = $aop->execute($data);
            $responseArray = json_decode($response, true);
            if ($responseArray['success']) {
                //添加店铺
                $store = UnionPayStore::where('store_id', $request->get('out_merchant_id'))->first();
                if ($store) {
                    UnionPayStore::where('store_id', $request->get('out_merchant_id'))->update($insert);
                } else {
                    //收银员绑定店铺
                    $m_id = auth()->guard('merchant')->user()->id;
                    MerchantShops::create([
                        'merchant_id' => $m_id,
                        'store_id' => $request->get('out_merchant_id'),
                        'store_type' => 'unionpay',
                        'desc_pay' => '银联',
                    ]);
                    UnionPayStore::create($insert);
                }
                $data = [
                    'status' => 1,
                    'msg' => '创建成功',
                ];
            } else {
                $data = [
                    'status' => $responseArray['error_code'],
                    'msg' => $responseArray['error_message'],
                ];
            }
        } catch (\Exception $exception) {
            Log::info($exception);
            $data = [
                'status' => 0,
                'msg' => '系统升级！请稍后再试',
            ];

        }
        return json_encode($data);
    }

    //商户绑卡
    public function bindCard()
    {

        return view("admin.UnionPay.store.bindCard");

    }

    public function bindCardPost(Request $request)
    {
        $out_merchant_id = $request->get('out_merchant_id');
        $bank_card_no = $request->get('bank_card_no');
        $data = $request->except(['_token', 'type', 'out_merchant_id']);
        $data['store_id'] = $out_merchant_id;
        if ($out_merchant_id && $out_merchant_id) {
            if ($request->get('type') == 0) {
                $card = UnionPayStoreCart::where('store_id', $out_merchant_id)->first();
                if ($card) {
                    UnionPayStoreCart::where('store_id', $out_merchant_id)->update($data);
                } else {
                    UnionPayStoreCart::create($data);
                }
                $Rdata = [
                    'status' => 1,
                    'msg' => '添加成功'
                ];

            } else {
                $aop = $this->AopClient();
                $aop->method = "fshows.paycompany.liquidation.merchant.bindcard";
                $pay = [
                    'out_merchant_id' => $out_merchant_id,
                    'bank_card_no' => $bank_card_no,
                ];
                $data = array('content' => json_encode($pay));
                $response = $aop->execute($data);
                dd($response);
            }
        } else {
            $Rdata = [
                'status' => 0,
                'msg' => '信息不完整'
            ];
        }

        return json_encode($Rdata);
    }

    public function UnionPayStoreSuccess()
    {
        
        return view('admin.UnionPay.store.success');
    }

}