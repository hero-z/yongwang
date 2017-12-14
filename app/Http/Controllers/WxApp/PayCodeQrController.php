<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/10
 * Time: 18:57
 */

namespace App\Http\Controllers\WxApp;




use App\Merchant;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayIsvConfig;
use App\Models\MerchantShops;
use App\Models\PufacqrLsitsinfo;
use App\Models\PufaStores;
use App\Models\QrListInfo;
use App\Models\WeBankStore;
use App\Models\WeixinShopList;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Mockery\CountValidator\Exception;
use Symfony\Component\HttpFoundation\Request;

class PayCodeQrController extends BaseController
{


    public function paycodeqr(Request $request)
    {
        $type=$request->type;
        $m_id = auth()->guard('merchant')->user()->id;
        $storeCollect= MerchantShops::where('merchant_id',$m_id)->where('store_type',$type);
        $info='-101未知错误,请联系服务商!';
        if($storeCollect->count()>0){
            try{
                $storeinfo=$storeCollect->first();
                switch($type){
                    case 'oalipay':
                        $store_id= $storeinfo->store_id;//授权的user_id
                        //存收银员信息
                        $merchant_id=$m_id;
                        $merchant_name="";
                        if ($merchant_id){
                            $merchant_name=Merchant::where('id',$merchant_id)->first()->name;
                        }
                        $config = AlipayIsvConfig::where('id', 1)->first();
                        if ($config) {
                            $config = $config->toArray();
                        }
                        $usersInfo = AlipayAppOauthUsers::where('store_id', $store_id)->first();
                        if ($usersInfo) {
                            $auth_shop_name = $usersInfo->toArray()['auth_shop_name'];
                        } else {
                            $auth_shop_name = "无效商户二维码";
                        }
                        $config['app_auth_url'] = Config::get('alipayopen.app_auth_url');
                        $code_url = $config['app_auth_url'] . '?app_id=' . $config['app_id'] . "&redirect_uri=" . $config['callback'] . '&scope=auth_base&state=OSK_' . $store_id.'_'.$merchant_id;
                        return view('admin.weixin.menu.oalipaycode', compact('code_url', 'auth_shop_name','merchant_name'));
                        break;
                    case 'weixin':
                        $store_id = $storeinfo->store_id;
                        $merchant_id=$m_id;
                        $merchant_name='';
                        if ($merchant_id){
                            $merchant_name=Merchant::where('id',$merchant_id)->first()->name;
                        }
                        $shop = WeixinShopList::where('store_id', $store_id)->first();
                        if($shop){
                            $shop_name=$shop->store_name;
                            $code_url = url('admin/weixin/oauth?sub_info=pay_' . $store_id.'_'.$merchant_id);
                            return view('admin.weixin.menu.wxpaycode', compact('code_url', 'shop_name','merchant_name'));
                        }else{
                            $info='201不存在该店铺信息,请联系服务商!';
                        }
                        break;
                    case 'pufa':
                        $store_id = $storeinfo->store_id;
                        $cashier_id = $m_id;
                        $pufaqrinfo = PufacqrLsitsinfo::where('store_id', $store_id)->first();

                        if ($pufaqrinfo) {
                            $code_number = $pufaqrinfo->code_number;
                            if (!$code_number){
                                $info='301收款码不存在！请联系服务商,确认是否入驻成功';
                            }else{
                                $store_name = PufaStores::where('store_id', $store_id)->first()->merchant_short_name;
                                $cashier_name=Merchant::where('id',$cashier_id)->first()->name;

                                $code_url = url('api/pufa/payway?code_number=' . $code_number . '&cashier_id=' . $cashier_id);
                                return view('admin.weixin.menu.pufacode', compact('code_url', 'cashier_name', 'store_name'));
                            }
                        } else {
                            $info='302用户信息不存在！请联系服务商,确认是否入驻成功';
                        }

                        break;
                    case 'zx':
                        $store_id = $storeinfo->store_id;
                        $cashier_id = $m_id;
                        $zxqrinfo = QrListInfo::where('store_id', $store_id)->first();
                        if ($zxqrinfo) {
                            $code_number = $zxqrinfo->code_number;
                            if (!$code_number){
                                $info='401收款码不存在！请联系服务商,确认是否入驻成功';
                            }else{
                                $store_name = DB::table('zx_stores')->where('store_id', $store_id)->first()->merchant_short_name;
                                $cashier_name=Merchant::where('id',$cashier_id)->first()->name;

                                $code_url = route('zxpayway',['code_number'=>$code_number ,'cashier_id' =>$cashier_id]);
                                return view('admin.weixin.menu.zxcode', compact('code_url', 'cashier_name', 'store_name'));
                            }
                        } else {
                            $info='402用户信息不存在！请联系服务商,确认是否入驻成功';
                        }

                        break;
                    case 'webank':
                        $store_id = $storeinfo->store_id;
                        $merchant_name='';
                        $wbqrinfo = DB::table('wb_qr_list_infos')->where('store_id', $store_id)->first();
                        if($wbqrinfo){
                            $code_number=$wbqrinfo->code_number;
                            if($code_number){
                                $store_name = WeBankStore::where('store_id', $store_id)->first()->alias_name;
                                $merchant_name=Merchant::where('id',$m_id)->first()->name;
                                $code_url = url('admin/webank/webankQrCode?code_number=' . $code_number."&merchant_id=".$m_id);
                                return view('admin.webank.myqr', compact('code_url', 'store_name','store_id','merchant_name'));
                            }
                        }else{
                            $info='收款信息不存在或者已关闭';
                        }
                        break;
                }
            }catch(Exception $e){
                $info=$e->getMessage();
            }
        }else{
            $info='-102该通道信息不存在!请联系服务商';
            if($type=='pufa'){
                $code_number='f'.time().rand(10000,99999);
                PufacqrLsitsinfo::create([
                    'code_number' => $code_number,
                    'from_info' => 'pufa',
                    'code_type'=>0
                ]);
                return redirect(route('pfautoStore',['code_number'=>$code_number]));
            }
            if($type=='webank'){
                return redirect(route('webankregister',['type'=>2]));
            }
            if($type=='zx'){
                $code_number=date('YmdHis').rand(1000,9999);
                QrListInfo::create([
                    'code_number' => $code_number,
                    'code_type'=>0
                ]);
                return redirect(route('zxautoStore',['code_number'=>$code_number]));
            }
        }
        return view('admin.weixin.menu.showCodeError', compact('info'));
    }

}