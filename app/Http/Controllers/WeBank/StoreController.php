<?php
/**
 * Created by PhpStorm.
 * User: hero
 * Date: 2017/5/15
 * Time: 11:54
 */

namespace App\Http\Controllers\WeBank;


use App\Http\Controllers\AlipayOpen\NewOrderManageController;
use App\Models\MerchantShops;
use App\Models\ProvinceCity;
use App\Models\QrListInfo;
use App\Models\WeBankConfig;
use App\Models\WeBankStore;
use App\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;
use Symfony\Component\HttpFoundation\Request;

class StoreController  extends BaseController
{
    const MERCHANT_REGISTER_PATH='/api/aap/server/wepay/merchantregister';
    const MERCHANT_UPDATE_PATH='/api/aap/server/wepay/updatemerchant';
    const MERCHANT_QUERY_PATH='/api/aap/server/wepay/querymerchant';

    /**
     * 微众后台列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request){
        $alias_name=$request->alias_name;
        $wheresql=[];
        if($alias_name){
            $wheresql[]=['a.alias_name','like','%'.$alias_name.'%'];
        }
        if (Auth::user()->hasRole('admin')) {
            $lists=DB::table('we_bank_stores as a')
                ->join('users','users.id','a.user_id')
                ->where('a.pid',0)
                ->where('a.is_delete',0)
                ->where($wheresql)
                ->orderBy('a.created_at','desc')
                ->select('a.id','a.store_id','a.pid','a.alias_name','a.pay_status','a.contact_name','a.contact_phone_no','users.name as name')
                ->get();
        } else {
            $lists=DB::table('we_bank_stores as a')
                ->join('users','users.id','a.user_id')
                ->where('a.pid',0)
                ->where('a.is_delete',0)
                ->where($wheresql)
                ->where('a.user_id',Auth::user()->id)
                ->orderBy('a.created_at','desc')
                ->select('a.id','a.store_id','a.pid','a.alias_name','a.pay_status','a.contact_name','a.contact_phone_no','users.name as name')
                ->get();
        }
        $dataPaginator=self::dataPaginator($request,$lists);
        $datapage=$dataPaginator['datapage'];
        $paginator=$dataPaginator['paginator'];
        return view('admin.webank.index',compact('datapage','paginator','alias_name'));
    }

    /**
     * 配置列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function config(){
        $wxid=1;
        $aliid=2;
        $wx=WeBankConfig::where('id',$wxid)->first();
        if(!$wx){
            $insert=[
                'id'=>$wxid,
                'code_no'=>'',
                'app_id'=>'',
                'secret'=>''
            ];
            $istw=DB::table('we_bank_configs')->insert($insert);
            if($istw){
                $wx=WeBankConfig::where('id',$wxid)->first();
            }else
                die('初始化微信配置失败!');
        }
        $ali=WeBankConfig::where('id',$aliid)->first();
        if(!$ali){
            $insert=[
                'id'=>$aliid,
                'code_no'=>'',
                'app_id'=>'',
                'secret'=>''
            ];
            $ista=DB::table('we_bank_configs')->insert($insert);
            if($ista){
                $ali=WeBankConfig::where('id',$aliid)->first();
            }else
                die('初始化支付宝配置失败!');
        }
        return view('admin.webank.config',compact('wx','ali'));
    }

    /**
     * 更新配置
     * @param Request $request
     * @return string
     */
    public function configpost(Request $request){
        $id=$request->id;
        $update=$request->except('_token','id');
        foreach ($update as $k=>$v){
            $update[$k]=trim($v);
        }
        $config=WeBankConfig::where('id',$id)->first();
        if($config){
            $res=WeBankConfig::where('id',$id)->update($update);
            if($res==1){
                return json_encode([
                    'status'=>1,
                ]);
            }else{
                $info='更新配置失败';
            }
        }else{
            $info='该配置不存在';
        }
        return json_encode([
            'status'=>0,
            'msg'=>$info
        ]);
    }

    /**
     * 上传证书
     * @return string
     */
    public static function sendfile(){
        $file = Input::file('file');
        $data = [
            'error' => '上传文件失败',
            'status' => 0,
        ];
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
            if($entension=='crt'||$entension=='key'){
                $clientName = $file -> getClientOriginalName();
                $file->move(public_path() . '/webank/', $clientName);
                $data = [
                    'path' => '/webank/' . $clientName,
                    'status' => 1,
                ];
            }else{
                $data = [
                    'error' => '上传文件格式不正确',
                    'status' => 0,
                ];
            }
        }
        return json_encode($data);
    }

    public function register(Request $request){
        $code_from=$request->code_from;
        $code_number=$request->code_number;
        $user_id=$request->user_id;
        $store_id= AopClient::getStoreId('b');
        $info='出错了';
        //判断是不是微信
        $agent_type='other';
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $agent_type = 'weixin';
        }
        //判断是不是支付宝
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $agent_type = 'alipay';
        }
        if($agent_type=='other'){
            $info='请使用支付宝或者微信客户端操作';
        }else{
            if($code_from=='1'){
                $merchant_id = auth()->guard('merchant')->user()->id;
                $merchantShop = MerchantShops::where('merchant_id', $merchant_id)->where('store_type', 'webank')->first();
                if ($merchantShop) {
                    return view('admin.webank.error',['info'=>'您已存在微众店铺无需注册!']);
                }
            }
            $name='';
            if($user_id){
                $user=User::where('id',$user_id)->select('name')->first();
                if($user){
                    $name=$user->name;
                }
            }
            //获取省市列表
            $provincelists=ProvinceCity::where('areaParentId',1)->get();
            //获取类目
            $cates=DB::table('we_bank_category')->where('pid',0)->select('id','name')->get();
            //MCC类目
            $mcc=DB::table('we_bank_mcc')->where('pid',0)->select('id','code','name')->get();
            return view('admin.webank.register',compact('code_from','user_id','code_number','store_id','name','provincelists','cates','mcc'));
        }
        //扫码入驻携带code_number参数
//        if($code_number){
//            if(!$type){
//                return view('admin.webank.choose','code_number');
//            }elseif($type==1||$type==2){
//                $mark=[1=>'alipay',2=>'weixin'];
//                return view('admin.webank');
//            }else{
//                $info='商户类型不合法';
//            }
//        }else{
//            $m_id = auth()->guard('merchant')->user()->id;
//            //已经注册
//            $webankstore = MerchantShops::where('merchant_id', $m_id)->where('store_type', 'webank_'.$agent_type)->first();
//            if ($webankstore) {
//                $store_id = $webankstore->store_id;
//                $qrcodeinfo = QrListInfo::where('store_id', $store_id)->first();
//                if ($qrcodeinfo) {
//                    $code_url = url('Qrcode?code_number=' . $qrcodeinfo->code_number);
//                    $store_name = $webankstore->alias_name;
//                    return view('merchant.pinganstore.myqr', compact('code_url', 'store_name','store_id'));
//                } else {
//                    $info='收款信息不存在请联系服务商';
//                }
//                //如果有推广员就是加一个USer_id
//            } else {
//                //未注册
//                //$user_id='';
//                return view('merchant.pinganstore.autostore',compact('user_id','provincelists'));
//            }
//        }
        return view('admin.webank.error',compact('info'));
    }
    public function bindcard(Request $request){
        $product_type = $request->get('product_type');
        $id_type = $request->get('id_type');
        $id_no= $request->get('id_no');
        $merchant_name = $request->get('merchant_name');
        $merchant_type_code= $request->get('merchant_type_code');
        $licence_no= $request->get('licence_no');
        $category_id = $request->get('category_id');
        $alias_name = $request->get('alias_name');
        $address = $request->get('address');
        $contact_name = $request->get('contact_name');
        $contact_phone = $request->get('contact_phone');
        $service_phone = $request->get('service_phone');
        $user_id = $request->get('user_id');
        $province_code = $request->get('province');
        $city_code= $request->get('city');
        $district_code= $request->get('county');
        $district = $request->get('district');
        $store_id = $request->get('store_id');
        $code_number = $request->get('code_number');
        $code_from = $request->get('code_from');
        //检查系统店铺是否存在
        $s = WeBankStore::where('alias_name', $alias_name)->first();
        $ck=true;
        $info='出错了';
        if ($s) {
            $info='该简称店铺已经存在！请联系服务商';
            $ck=false;
        }
        //检查用户是否存在
        $u = User::where('id', $user_id)->first();
        if (!$u) {
            $info='推广员不存在';
            $ck=false;
        }
        if($ck){
            $banks=DB::table('we_bank_opbanks')->select('bank_no','bank_name')->get();
            return view('admin.webank.bindcard',compact('product_type','id_type','id_no', 'merchant_name','merchant_type_code','licence_no','category_id', 'alias_name','address','contact_name','contact_phone','service_phone','user_id','province_code','city_code','district_code','district','store_id','code_number','banks','code_from'));
        }
        return back()->with('info',$info)->withInput();
    }
    public function bindcardpost(Request $request){
        $data=$request->except('_token');

    }
    public function uploadfile(Request $request){
        $store_id=$request->store_id;
        $code_number=$request->code_number;
        $code_from=$request->code_from;
        return view('admin.webank.uploadfile',compact('store_id','code_number','code_from'));
    }
    public function douploadfile(Request $request){
        $file = Input::file('image');
        $data = [
            'error' => '上传文件失败',
            'status' => 0,
        ];
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
            $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
            $filepath='/uploads/webank';
            $path = $file->move(public_path() . $filepath, $newName);
            $data = [
                'path' =>  $filepath ."/". $newName,
                'img_url' =>  url($filepath ."/". $newName),
                'status' => 1,
            ];
        }
        return json_encode($data);
    }
    public function douploadfiles(Request $request){
        $store_id=$request->store_id;
        $code_number=$request->code_number;
        $update=$request->except('_token','store_id','code_number');
        $up=WeBankStore::where('store_id',$store_id)->update($update);
        if($up){
            return json_encode([
                'success'=>1
            ]);
        }
        return json_encode([
            'success'=>0,
            'msg'=>'更新信息出错'
        ]);
    }
    public function merchantregister(Request $request){
        $postdata=$request->except('product_type','_token','code_number','store_id','account_info','category_id');
        $store_id=$request->store_id;
        $account_info=$request->account_info;
        $category_id=$request->category_id;
        $code_num=$request->code_number;
        foreach ($postdata as $k=>$v){
            $postdata[$k]=trim($v);
        }
        $account_info=explode('**',$account_info);
        $postdata['account_opbank_no']=$account_info[0];
        $postdata['account_opbank']=$account_info[1];
        $category_id=explode('**',$category_id);
        $postdata['wx_category_id']=$category_id[0];
        $postdata['ali_category_id']=$category_id[1];
        if(!$code_num){
            $code_num=date('YmdHis',time()).rand(1000,9999);
        }
        $m_id=auth()->guard('merchant')->user()->id;
        $merchant_level='2';//默认一户一报
//        $settlement_type=$type=='003'?'11':'01';//默认结算方式11-T1,01-D1
        $commission_rate='0.6';//默认返佣
        $district='0755';//对应地区码表
        $postdata['district']=$district;//对应地区码表
        $district=DB::table('we_bank_district')->where('district',$postdata['district'])->first();
        if($district){
            $postdata['district']=$district->district_code;
        }
        try{
            $storeinfos=WeBankStore::where('store_id',$store_id)->first();
            $storeunioninfos=DB::table('we_bank_storeunion')->where('store_id',$store_id)->count();
            if ($storeinfos||$storeunioninfos>=2) {
                return json_encode([
                    'code' => 0,
                    'msg' => '请勿重复提交！'
                ]);
            }
            $wxpartid='wx'.date('YmdHis').rand(100,999);
            $postdata['store_id']=$wxpartid;
            $wxres=self::registerapi($postdata,1);
            $alipartid='ali'.date('YmdHis').rand(100,999);
            $postdata['store_id']=$alipartid;
            $alires=self::registerapi($postdata,2);
            //更新库wb_merchant_id
            if ($wxres['code'] == 0&&$wxres['success']||$alires['code'] == 0&&$alires['success']) {

                DB::table('we_bank_storeunion')->insert([
                    'store_id'=>$store_id,
                    'product_type'=>'004',
                    'partner_mch_id'=>$wxpartid,
                    'category_id'=>$postdata['wx_category_id'],
                    'payment_type'=>$postdata['payment_type']==1?'25':'26',
                    'settlement_type'=>'11'
                ]);
                DB::table('we_bank_storeunion')->insert([
                    'store_id'=>$store_id,
                    'product_type'=>'003',
                    'partner_mch_id'=>$alipartid,
                    'category_id'=>$postdata['ali_category_id'],
                    'payment_type'=>$postdata['payment_type']==1?'23':'24',
                    'settlement_type'=>'01',
                ]);
                if(isset($wxres['wbMerchantId'])){
                    DB::table('we_bank_storeunion')->where('partner_mch_id',$wxpartid)->update(['wb_merchant_id'=>$wxres['wbMerchantId']]);
                }else{
                    Log::info($wxres);
                }
                if(isset($alires['wbMerchantId'])){
                    DB::table('we_bank_storeunion')->where('partner_mch_id',$alipartid)->update(['wb_merchant_id'=>$alires['wbMerchantId']]);
                }else{
                    Log::info($alires);
                }

                $istdata = array_except($postdata, ['district','product_type','merchant_name','contact_phone','wx_category_id','ali_category_id']);
                $istdata['merchant_level']=$merchant_level;
                $istdata['settlement_type']='01';
                $istdata['commission_rate']=$commission_rate;
                $istdata['store_id']=$store_id;
//                $istdata['store_type']=$postdata['product_type'];
                $istdata['store_name']=$postdata['merchant_name'];
                $istdata['contact_phone_no']=$postdata['contact_phone'];
//                $istdata['wb_merchant_id']=$wxres['wbMerchantId'];
                $istdata['merchant_id']=$m_id;
                $istdata['created_at']=date('Y-m-d H:i:s',time());
                $ist=DB::table('we_bank_stores')->insertGetId($istdata);
                if($ist>0){
                    //激活收款吗
                    $qrinfo=DB::table('wb_qr_list_infos')->where('code_number',$code_num)->first();
                    if($qrinfo){
                        DB::table('wb_qr_list_infos')->where('code_number',$code_num)->update([
                            'user_id' => $postdata['user_id'],
                            'code_number' => $code_num,
                            'code_type'=>1,
                            'store_id'=>$store_id,
                            'updated_at'=>date('YmdHis')
                        ]);
                    }else{
                        DB::table('wb_qr_list_infos')->insert([
                            'user_id' => $postdata['user_id'],
                            'code_number' => $code_num,
                            'code_type'=>1,
                            'store_id'=>$store_id,
                            'updated_at'=>date('YmdHis')
                        ]);
                    }

                    /*QrListInfo::create([
                        'user_id'=>$postdata['user_id'],
                        'code_number'=>$code_num,
                        'code_type'=>1,
                        'store_id'=>$store_id
//                        'store_type'=>'webank_'.$type
                    ]);*/
                    //关联商户
                    MerchantShops::create([
                        'merchant_id'=>$m_id,
                        'store_id'=>$store_id,
                        'store_name'=>$postdata['merchant_name'],
                        'store_type'=>'webank',
                        'desc_pay'=>'微众通道',
                        'status'=>1,
                    ]);
                    return json_encode([
                        'code'=>1,
                        'store_id'=>$store_id,
                        'code_number'=>$code_num
                    ]);
                }else{
                    return json_encode([
                        'code'=>0,
                        'msg'=>'数据库创建记录失败'
                    ]);
                }
            }else{
                Log::info($request);
                Log::info($wxres);
                Log::info($alires);
                $msg='';
                if(isset($wxres['msg'])){
                    $msg.='微信:'.$wxres['msg'];
                }
                if(isset($alires['msg'])){
                    $msg.='支付宝'.$alires['msg'];
                }
                return json_encode([
                    'code'=>0,
                    'msg'=>$msg
                ]);
            }
        }catch (Exception $e){
            Log::info($e);
            throw $e;
        }
    }
    public function registerapi($postdata,$id){
        $webank=$this->WebankHelper($id);
        $app_id = $webank->appId;
        $version = $webank->version;
        $nonce = $webank->getNonce();
//        $timestamp = $webank->getTimeStamp();
        //id 1微信 2支付宝
        $postdata['product_type']=$id==1?'004':'003';
        $postdata['merchant_level']='2';//默认一户一报
        $postdata['settlement_type']=$id==2?'11':'01';//默认结算方式11-T1,01-D1
        $postdata['commission_rate']='0.6';//默认返佣
//        $postdata['district']='0755';//对应地区码表
        $postdata['category_id']=$id==1?$postdata['wx_category_id']:$postdata['ali_category_id'];
        if($postdata['payment_type']=='1'){
            $postdata['payment_type']=$id==1?'25':'23';
        }else{
            $postdata['payment_type']=$id==1?'26':'24';
        }
        $data=[
            'productType'=>$postdata['product_type'],
            'merchantInfo'=>[
                'agencyId'=>$webank->agency_id,
                'partnerMchId'=>$postdata['store_id'],
                'appId'=>$app_id,
                'idType'=>$postdata['id_type'],
                'idNo'=>$postdata['id_no'],
                'merchantName'=>$postdata['merchant_name'],
                'aliasName'=>$postdata['alias_name'],
                'licenceNo'=>$postdata['licence_no'],
                'contactName'=>$postdata['contact_name'],
                'legalRepresent'=>$postdata['contact_name'],
                'contactPhoneNo'=>$postdata['contact_phone'],
                'merchantTypeCode'=>$postdata['merchant_type_code'],
                'merchantLevel'=>$postdata['merchant_level'],
                'categoryId'=>$postdata['category_id'],
            ],
            'merchantAccount'=>[
                'accountNo'=>$postdata['account_no'],
                'accountOpbankNo'=>$postdata['account_opbank_no'],
                'accountName'=>$postdata['account_name'],
                'accountOpbank'=>$postdata['account_opbank'],
                'acctType'=>$postdata['acct_type'],
            ],
            'paymentType'=>$postdata['payment_type'],
            'settlementType'=>$postdata['settlement_type'],
            'commissionRate'=>$postdata['commission_rate'],
            'servicePhone'=>$postdata['service_phone'],
            'district'=>$postdata['district']
        ];
        $jsonData=json_encode($data,true);
        $params = array($app_id, $version, $nonce, $jsonData);
        $sign = $webank->getSign($params);
        if (!$sign) {
            Log::error("Sign is empty!");
            return array(
                'code' => '-2',
                'msg' => '签名计算失败！'
            );
        }
        $url_params = sprintf(self::COMMON_SIGN_FORMAT, $app_id, $nonce, $version, $sign);
        $header = ['Content-Type: application/json'];
        $request = array(
            'url' => $webank->headUrl.self::MERCHANT_REGISTER_PATH . $url_params,
            'method' => 'post',
            'timeout' => self::$timeout,
            'data' => $jsonData,
            'header' => $header,
        );
        $result = $webank->sendRequest($request);
        if($result['code'] != 0||$result['success']!=0){
            file_put_contents(storage_path().'/wb1.txt',var_export($request,TRUE),FILE_APPEND);
            file_put_contents(storage_path().'/wb2.txt',var_export($result,TRUE),FILE_APPEND);
        }
        return $result;
    }
    public function editmerchantfile(Request $request){
        $store_id=$request->store_id;
        $store=WeBankStore::where('store_id',$store_id)->where('is_delete',0)->first();
        if($store){
            $wxstore_union=DB::table('we_bank_storeunion')->where('store_id',$store_id)->where('product_type','004')->first();
            if($wxstore_union&&$wxstore_union->wb_merchant_id){
                $wxstatus=$this->checkStatus($wxstore_union->wb_merchant_id,1);
            }else{
                $wxstatus=9;
            }
            $alistore_union=DB::table('we_bank_storeunion')->where('store_id',$store_id)->where('product_type','003')->first();
            if($alistore_union&&$alistore_union->wb_merchant_id){
                $alistatus=$this->checkStatus($alistore_union->wb_merchant_id,2);
            }else{
                $alistatus=9;
            }
            return view('admin.webank.eidtmerchantfile',compact('store','wxstore_union','alistore_union','alistatus','wxstatus'));
        }
        return back();
    }
    public function editmerchantfilepost(Request $request){
        $data=$request->except('_token');
        try{
            $update['idType']=$data['id_type'];
            $update['idNo']=$data['id_no'];
            $update['idNo']=$data['id_no'];
            $update['merchantName']=$data['store_name'];
            $update['aliasName']=$data['alias_name'];
            $update['licenceNo']=$data['licence_no'];
            $update['contactName']=$data['contact_name'];
            $update['contactPhoneNo']=$data['contact_phone_no'];
            $update['merchantTypeCode']=$data['merchant_type_code'];
            $update['accountNo']=$data['account_no'];
            $update['accountOpbankNo']=$data['account_opbank_no'];
            $update['accountName']=$data['account_name'];
            $update['accountOpbank']=$data['account_opbank'];
            $update['commissionRate']=$data['commission_rate'];

            $update['wbMerchantId']=$data['wx_wb_merchant_id'];
            $update['categoryId']=$data['wx_category_id'];
            $wxres=$this->editstoreapi($update,1);
            $update['wbMerchantId']=$data['ali_wb_merchant_id'];
            $update['categoryId']=$data['ali_category_id'];
            $alires=$this->editstoreapi($update,2);
            if($wxres['code'] == 0&&$wxres['success']){
                DB::table('we_bank_storeunion')->where('store_id',$data['store_id'])->where('product_type','004')->update([
                    'category_id'=>$data['wx_category_id'],
                ]);
            }
            if($alires['code'] == 0&&$alires['success']){
                DB::table('we_bank_storeunion')->where('store_id',$data['store_id'])->where('product_type','003')->update([
                    'category_id'=>$data['ali_category_id'],
                ]);
            }
            if ($wxres['code'] == 0&&$wxres['success']||$alires['code'] == 0&&$alires['success']) {
                $updatedata = array_except($data, ['wx_wb_merchant_id','wx_category_id','ali_wb_merchant_id','ali_category_id']);
                $store=WeBankStore::where('store_id',$data['store_id'])->first();
                if($store){
                    WeBankStore::where('store_id',$data['store_id'])->update($updatedata);
                }
                return json_encode([
                    'code'=>1
                ]);
            }else{
                Log::info($request);
                Log::info($wxres);
                Log::info($alires);
                $msg='';
                if(isset($wxres['msg'])){
                    $msg.='微信:'.$wxres['msg'];
                }
                if(isset($alires['msg'])){
                    $msg.='支付宝'.$alires['msg'];
                }
                return json_encode([
                    'code'=>0,
                    'msg'=>$msg
                ]);
            }
        }catch (Exception $e){
            Log::info($e);
            throw $e;
        }
    }
    public function editstoreapi($postdata,$id){
        $webank=$this->WebankHelper($id);
        $app_id = $webank->appId;
        $version = $webank->version;
        $nonce = $webank->getNonce();
        $data=[
            'merchantInfo'=>[
                'wbMerchantId'=>$postdata['wbMerchantId'],
                'agencyId'=>$webank->agency_id,
                'appId'=>$app_id,
                'idType'=>$postdata['idType'],
                'idNo'=>$postdata['idNo'],
                'merchantName'=>$postdata['merchantName'],
                'aliasName'=>$postdata['aliasName'],
                'licenceNo'=>$postdata['licenceNo'],
                'contactName'=>$postdata['contactName'],
                'contactPhoneNo'=>$postdata['contactPhoneNo'],
                'merchantTypeCode'=>$postdata['merchantTypeCode'],
                'categoryId'=>$postdata['categoryId'],
            ],
            'merchantAccount'=>[
                'accountNo'=>$postdata['accountNo'],
                'accountOpbankNo'=>$postdata['accountOpbankNo'],
                'accountName'=>$postdata['accountName'],
                'accountOpbank'=>$postdata['accountOpbank'],
            ],
            'commissionRate'=>$postdata['commissionRate']
        ];
        $jsonData=json_encode($data,true);
        $params = array($app_id, $version, $nonce, $jsonData);
        $sign = $webank->getSign($params);
        if (!$sign) {
            Log::error("Sign is empty!");
            return array(
                'code' => '-2',
                'msg' => '签名计算失败！'
            );
        }
        $url_params = sprintf(self::COMMON_SIGN_FORMAT, $app_id, $nonce, $version, $sign);
        $header = ['Content-Type: application/json'];
        $request = array(
            'url' => $webank->headUrl.self::MERCHANT_UPDATE_PATH . $url_params,
            'method' => 'post',
            'timeout' => self::$timeout,
            'data' => $jsonData,
            'header' => $header,
        );
        $result = $webank->sendRequest($request);
        if($result['code'] == 0&&$result['success']){
            return $result;
        }else{
            Log::info($request);
            Log::info($result);
        }
        return $result;
    }
    public function checkStatus($wb_merchant_id,$id){
        $webank=$this->WebankHelper($id);
        $app_id = $webank->appId;
        $version = $webank->version;
        $nonce = $webank->getNonce();
        $data=[
            'wbMerchantId'=>$wb_merchant_id,
            'agencyId'=>$webank->agency_id
        ];
        $jsonData=json_encode($data,true);
        $params = array($app_id, $version, $nonce, $jsonData);
        $sign = $webank->getSign($params);
        if (!$sign) {
            Log::error("Sign is empty!");
            return array(
                'code' => '-2',
                'msg' => '签名计算失败！'
            );
        }
        $url_params = sprintf(self::COMMON_SIGN_FORMAT, $app_id, $nonce, $version, $sign);
        $header = ['Content-Type: application/json'];
        $request = array(
            'url' => $webank->headUrl.self::MERCHANT_QUERY_PATH . $url_params,
            'method' => 'post',
            'timeout' => self::$timeout,
            'data' => $jsonData,
            'header' => $header,
        );
        $result = $webank->sendRequest($request);
        if($result['code'] == 0&&$result['success']){
            return $result['checkStatus'];
        }else{
            Log::info($request);
            Log::info($result);
            die('查询状态出错');
        }
    }
    public function storesuccess()
    {
        return view('admin.webank.storesuccess');
    }
    public function editcode(Request $request){
        $store_id=$request->store_id;
        $name='';
        $code_number='';
        $wx_merchant_id='';
        $ali_merchant_id='';
        if($store_id){
            $store=WeBankStore::where('store_id',$store_id)->first();
            if($store){
                $name=$store->alias_name;
            }
            $qr=DB::table('wb_qr_list_infos')->where('store_id',$store_id)->first();
            if($qr){
                $code_number=$qr->code_number;
            }
            $storeunion=DB::table('we_bank_storeunion')->where('store_id',$store_id)->where('product_type','004')->first();
            if($storeunion){
                $wx_merchant_id=$storeunion->wb_merchant_id;
            }
            $storeunion=DB::table('we_bank_storeunion')->where('store_id',$store_id)->where('product_type','003')->first();
            if($storeunion){
                $ali_merchant_id=$storeunion->wb_merchant_id;
            }
        }
        return view('admin.webank.editCode',compact('name','code_number','store_id','wx_merchant_id','store','qr','ali_merchant_id'));
    }
    public function doeditcode(Request $request){
        $info='保存失败!';
        try {
            $id = $request->get("id");
            $store_id = $request->get("store_id");
            $list = DB::table('wb_qr_list_infos')->where("id", $id)->first();
            $codes = $request->get("code_number");
//            $name = $request->get("name");
            $wxappid = $request->get("wx_app_id");
            $wxsecret = $request->get("wx_secret");
//            $data['alias_name'] = $name;
            $data['wx_app_id'] = $wxappid;
            $data['wx_secret'] = $wxsecret;
//            $datas['store_name'] = $name;
//            $datas['updated_at'] = date('Y-m-d H:i:s');



            if ($list->code_number == $codes){
                if(DB::table("we_bank_stores")->where("store_id", $store_id)->update($data))
                    return redirect("/admin/webank/index");
                else
                    $info.='-222';
            }else{
                if(DB::table("qr_list_infos")->where("code_number", $codes)->where("code_type", 0)->first()){
                    $datass['store_id'] = $list->store_id;
                    $datass['code_type'] = 1;
                    if (DB::table("qr_list_infos")->where("code_number", $codes)->update($datass)) {
                        if (DB::table("qr_list_infos")->where("id", $id)->delete()) {
                            return redirect("/admin/webank/index");
                        }else{
                            $info.='原code删除失败';
                        }
                    }else{
                        $info.='-333';
                    }
                }else{
                    $info.='收款码编号不存在或已经被占用';
                }
            }
            return back()->with("warnning", $info);
        }catch(\Exception $e){
            Log::info($e);
            return back()->with("warnning", $info);
        }
    }
    public function getcate(Request $request){
        $result=[];
        $parentid=$request->id;
        if($parentid)
            $result=DB::table('we_bank_category')->where('pid',$parentid)->select('id','name','wx_category_id','ali_category_id')->get();
        if($result)
            $result=$result->toArray();
        return json_encode($result);
    }
    public function getmcc(Request $request){
        $result=[];
        $parentid=$request->id;
        if($parentid)
            $result=DB::table('we_bank_mcc')->where('pid',$parentid)->select('id','name','code')->get();
        if($result)
            $result=$result->toArray();
        return json_encode($result);
    }
    /**
     * 分页
     * @param Request $request
     * @param $list
     * @return array
     */
    public static function dataPaginator(Request $request,$list){
        try{
            if ($list==''||$list->isEmpty()) {
                $paginator = "";
                $datapage = "";
            } else {
                $data = $list->toArray();
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
            return compact('datapage', 'paginator');
        }catch (Exception $e){
            die('分页失败');
        }

    }}