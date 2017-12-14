<?php
/*
*  浦发服务商后台管理
*/
namespace App\Http\Controllers\PuFa;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PufaConfig;
use App\Http\Controllers\PuFa\Verify;
use App\Http\Controllers\PuFa\Tools;
use App\Models\PufaStores;
use App\Models\PufacqrLsitsinfo;

use Illuminate\Support\Facades\Validator;
use App\Merchant;

use Illuminate\Support\Facades\Log;


class StoreController extends Controller
{
    static function log($data,$file='')
    {
        // return;
        $file=$file ? $file : (storage_path().'/logs/pufa_error_log_store_jinjian.txt');
        file_put_contents($file, "\n\n\n".date('Y-m-d H:i:s')."\n".var_export($data,TRUE),FILE_APPEND);
    }


    public function pufaConfig(Request $request)
    {

        // $this->testpaytype();die;
        if($request->isMethod('get')){
            $data=DB::table('pufa_configs')->where('id','=','1')->first();
            
            return view('pufa.config', compact('data'));
        }

        if($request->isMethod('post')){
            $data=[
                'partner'=>trim($request->partner),
                'security_key'=>trim($request->security_key),
                'payurl'=>trim($request->payurl),
                'infourl'=>trim($request->infourl),
                'rate'=>trim($request->rate),
                'wx_app_id'=>trim($request->wx_app_id),
                'wx_secret'=>trim($request->wx_secret),
                'trans_partner'=>trim($request->trans_partner),
            ];
            $result=DB::table('pufa_configs')->where('id','=','1')->update($data);
            if($result)
            {
                return json_encode(['status'=>1,'message'=>'修改成功！']);
            }
            return json_encode(['status'=>0,'message'=>'修改失败，请重试！']);

        }

    }


    /*
        扫空码注册门店
    */

// http://isv.umxnt.com/api/pufa/autoStore?user_id=6&code_number=pf_14908416105710
    //商户提交进件资料到浦发银行
    public function autoStore(Request $request)
    {
/*        $pay_type = "other";
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
        }*/

        // 如果是付款码---则跳转到二码合一的付款码那边
        $code_number=trim($request->get('code_number'));
        $info = PufacqrLsitsinfo::where('code_number', $code_number)->first();
        if(empty($info))
        {
            echo '<h1>该二维码不存在！</h1>';die;
        }
        if($info->code_type==1)
        {
            return redirect(url('api/pufa/payway?code_number='.$code_number));
        }

        // 如果是空码---则去注册并绑定到merchant_shops
        $merchant_id=auth()->guard('merchant')->user()->id;//merchant表的id
        $info=DB::table('merchant_shops')->where('merchant_id','=',$merchant_id)->where('store_type','=','pufa')->first();
        if(!empty($info))
        {
            echo '<h1>您已经入驻店铺了，无需重复入驻</h1>';
            die;
        }

        $result=DB::table('pufa_stores')->count();
        if($result>1)
        {
            $_config=DB::table('pufa_configs')->where('id','=','1')->first();
            if(empty(trim($_config->trans_partner)))
            {
                echo '<h1>服务商交易识别号没有配置！</h1>';
                die;
            }
        }

        //显示推广员信息
        $recommender_id=trim($request->get('user_id'));
        $recommender_info=User::where('id', $recommender_id)->first();
        if($recommender_info)
        {
            $recommender=[
                'user_name'=>$recommender_info->name,
                'user_id'=>$recommender_info->id
            ];
        }
        else
        {
            $recommender=[
                'user_name'=>'',
                'user_id'=>''
            ];
        }

        return view('pufa.store.autostore', compact('recommender'));
    }

    //自主提交到后台保存 第一步提交
    public function autoStorePost(Request $request)
    {
        $data=$request->all();
        // 判断二维码是否存在
        $code_number=trim($data['code_number']);
        if(empty($code_number))
        {
            return json_encode(['status'=>1,'message'=>'该二维码不存在！']);
        }

        $info = PufacqrLsitsinfo::where('code_number', $code_number)->first();
        if(empty($info))
        {
            return json_encode(['status'=>1,'message'=>'该二维码不是服务商的！']);
        }

        if ($info->code_type == 1) {
            return json_encode(['status'=>1,'message'=>'该二维码已经注册商铺了，请换个二维码试试！']);
        }
        // 生成商铺号
        $store_id='f'.date('YmdHis').mt_rand(100000,999999);//' pf年月日rand6位';

        // 走浦发接口并入库
        $apidata=$this->pufaapi($data,$store_id);
        try
        {
            // 将商铺号绑定到二维码
            if($apidata['status']==2)
            {
                // 进件成功后将二维码标记为已经使用
                PufacqrLsitsinfo::where('code_number', $code_number)->update(['store_id'=>$store_id,'code_type'=>'1','store_name'=>trim($data['merchantName'])]);

                // 将店员绑定到店铺
                $merchant_id=auth()->guard('merchant')->user()->id;//merchant表的id
                $merchantdata=['merchant_id'=>$merchant_id,'store_type'=>'pufa','store_id'=>$store_id,'desc_pay'=>'浦发通道','status'=>'1','created_at'=>date('Y-m-d H:i:s')];
                $insert=DB::table("merchant_shops")->insert($merchantdata);

            }

        }
        catch(\Exception $e)
        {

            return json_encode(['status'=>1,'message'=>'服务商数据库错误！']);
self::log($e->getMessage()."\n".$e->getLine()."\n".$e->getFile().'商铺号：'.$store_id.'二维码号：'.$code_number);
        }

// 设置支付通道---start
        $paytypemsg='';
        if($apidata['status']==2)
        {

            try
            {
                // 获取服务商配置
                $config = DB::table("alipay_isv_configs")->where("id", '1')->first();
                if(empty($config))
                {
                    throw new \Exception('服务商主要配置不存在，请联系服务商！');
                }
                $_config=DB::table('pufa_configs')->where('id','=','1')->first();
                if(empty($_config))
                {
                    throw new \Exception('服务商配置不存在，请联系服务商！');
                }
                // $store_id = $request->get("store_id");//服务商生成的商户识别号    pufa_stores    store_id
                $store = PufaStores::where("store_id", $store_id)->first();
                if(empty($store))
                {
                    throw new \Exception('没有这个商户！');
                }

                $data=[
                            'trans_partner'=>$_config->trans_partner,//'商户在浦发的商户号',
                    'merchant_id'=>$store->merchant_id,//'商户在浦发的商户号',
                    'rate'=>($_config->rate)*1000,//'服务商设置商户的费率',
                    'partner'=>$_config->partner,//'服务商合作号',
                    'key'=>$_config->security_key,//'服务商秘钥',
                    'pid'=>$config->pid,//'合作商支付宝pid号',
            'request_url'=>$_config->infourl,
                    'store_id'=>$store_id,//'商户在服务商的id号',
                    // 'idnum'=>$k,//'浦发支付类型数字代号',----主要判断这个
                ];
                $allpaytype=Pufa::start()->paytype;
                foreach($allpaytype as $k=>$v)
                {
                    if(empty($v))
                        continue;
                    $data['idnum']=$k;
                    $return[]=$this->payType($data);
                }
                unset($k);unset($v);
// self::log($return);
                $paytype=false;
                foreach($return as $v)
                {
                    if($v['status']==2||$v['status']==3)
                    {
                        $paytype=true;
                        break;
                    }
                }
                $paytypemsg=$paytype?'':'支付方式设置不成功，请联系服务商处理！';
            }
            catch(\Exception $e)
            {
                // return json_encode(['status'=>'1','message'=>$e->getMessage().$e->getLine()]);
            }
     
        }
// 设置支付通道---end
        $apidata['message'].=$paytypemsg;

        return json_encode($apidata);
    }


    /*
        return   1  进件资料提交失败，开店失败
                 2    进件资料提交浦发成功，开店成功
    */
    private function pufaapi($request,$store_id)
    {
        try
        {
            // 验证参数
            if(Verify::isEmpty(trim($request['user_id'])))
            {
                return [
                    'status'=>1,
                    'message'=>'推广员信息不能为空！'
                ];
            }
            $recommender_info=User::where('id', trim($request['user_id']))->first();
            if(empty($recommender_info))
            {
                return [
                    'status'=>1,
                    'message'=>'推广员不存在！'
                ];

            }

$country_id=trim($request['district']);
$district=DB::table('pufa_region')->where('id',$country_id)->first();            
if(empty($district))
{
    return [
        'status'=>1,
        'message'=>'门店所在的省市区选择错误！'
    ];
}
$city=DB::table('pufa_region')->where('id',$district->pid)->first();            
$province=DB::table('pufa_region')->where('id',$city->pid)->first();            

            $feeType='CNY';
            // Verify::
            $data=array(
                'merchant'=>[
                    'merchantName'=>trim($request['merchantName']),
                    'outMerchantId'=>$store_id,
                    'feeType'=>$feeType,
                    'mchDealType'=>trim($request['mchDealType']),
                    'remark'=>'',
                    'chPayAuth'=>trim($request['chPayAuth']),//渠道模式，使用服务商信息完成交易
               
                    'merchantDetail'=>[
                        'merchantShortName'=>trim($request['merchantShortName']),
                        'industrId'=>trim($request['industrId']),
'province'=>$province->code,
'city'=>$city->code,
'county'=>$district->code,
                        'address'=>trim($request['address']),
                        'tel'=>trim($request['tel']),
                        'email'=>trim($request['email']),
                    'legalPerson'=>trim($request['principal']),
                'customerPhone'=>trim($request['tel']),
                        'idCode'=>trim($request['idCode']),
                        'indentityPhoto'=>trim($request['indentityPhoto_a_pf']).';'.trim($request['indentityPhoto_b_pf']),
                    'principal'=>trim($request['principal']),
                'principalMobile'=>trim($request['tel']),

                        'licensePhoto'=>trim($request['license_pf']),
'businessLicense'=>trim($request['business_license']),
                    ],
                    'bankAccount'=>[
                        'bankId'=>trim($request['bankId']),
                        'accountCode'=>trim($request['accountCode']),
                    'accountName'=>trim($request['principal']),
                        'accountType'=>trim($request['accountType']),
                        'bankName'=>trim($request['bankName']),
            'province'=>trim($request['province']),
            'city'=>trim($request['city']),
        'idCardType'=>1,//身份证
        'idCard'=>trim($request['idCode']),
                        'tel'=>trim($request['tel2']),
                        'contactLine'=>trim($request['contactLine']),
                        'address'=>trim($request['address']),
                    ],
                ],
            );


            // 验证参数

            if(Verify::isEmpty($data['merchant']['merchantDetail']['industrId']))
            {
                return [
                    'status'=>1,
                    'message'=>'行业类别不能为空！'
                ];
            }


            if(Verify::isEmpty($data['merchant']['merchantDetail']['province']))
            {
                return [
                    'status'=>1,
                    'message'=>'请选择商铺所在省！'
                ];
            }

            if(Verify::isEmpty($data['merchant']['merchantDetail']['city']))
            {
                return [
                    'status'=>1,
                    'message'=>'请选择商铺所在城市！'
                ];
            }


            if(Verify::isEmpty($data['merchant']['mchDealType']))
            {
                return [
                    'status'=>1,
                    'message'=>'商户经营类型不能为空！'
                ];
            }
             

            if(!Verify::length($data['merchant']['merchantName'],3,20))
            {
                return [
                    'status'=>1,
                    'message'=>'商户名称长度在3到20个字符！'
                ];
            }


            $haveshop = PufaStores::where('store_name', $data['merchant']['merchantName'])->first();
            if($haveshop)
            {
                return [
                    'status'=>1,
                    'message'=>'该店名已被注册，请更换店名再试！'
                ];
            }



            if(!Verify::length($data['merchant']['merchantDetail']['merchantShortName'],3,15))
            {
                return [
                    'status'=>1,
                    'message'=>'商户简称长度在3到15个字符！'
                ];
            }

            if(!Verify::length($data['merchant']['merchantDetail']['principal'],2,10))
            {
                return [
                    'status'=>1,
                    'message'=>'商铺营运人长度在2个以上字符！'
                ];
            }

            if(!Verify::isMobile($data['merchant']['merchantDetail']['tel']))
            {
                return [
                    'status'=>1,
                    'message'=>'手机号码格式不正确！'
                ];
            }


            if(!Verify::isEmail($data['merchant']['merchantDetail']['email']))
            {
                return [
                    'status'=>1,
                    'message'=>'邮箱格式不正确！'
                ];
            }

            $email = PufaStores::where('email', $data['merchant']['merchantDetail']['email'])->first();
            if(!empty($email))
            {
                return [
                    'status'=>1,
                    'message'=>'邮箱已经被占用，请使用其他邮箱！'
                ];
            }

            if(Verify::isEmpty($data['merchant']['merchantDetail']['address']))
            {
                return [
                    'status'=>1,
                    'message'=>'请填写商铺所在地址'
                ];
            }

            if(!Verify::isIdcard($data['merchant']['merchantDetail']['idCode']))
            {
                return [
                    'status'=>1,
                    'message'=>'身份证号码不正确！'
                ];
            }

            if(Verify::isEmpty($data['merchant']['bankAccount']['bankId']))
            {
                return [
                    'status'=>1,
                    'message'=>'请选择开户银行！'
                ];

            }

            if(Verify::isEmpty($data['merchant']['bankAccount']['contactLine']))
            {
                return [
                    'status'=>1,
                    'message'=>'请选择联行号！'
                ];

            }

            if(Verify::isEmpty($data['merchant']['bankAccount']['accountType']))
            {
                return [
                    'status'=>1,
                    'message'=>'请选择账户类型！'
                ];

            }


            if(Verify::isEmpty($data['merchant']['bankAccount']['bankName']))
            {
                return [
                    'status'=>1,
                    'message'=>'请输入开户支行名称！'
                ];

            }

            if(!Verify::length($data['merchant']['bankAccount']['accountCode'],7,25))
            {
                return [
                    'status'=>1,
                    'message'=>'请输入7位以上的银行卡号！'
                ];
            }
     

            if(!Verify::isMobile($data['merchant']['bankAccount']['tel']))
            {
                return [
                    'status'=>1,
                    'message'=>'银行卡预留手机号码格式不正确！'
                ];
            }



            if(Verify::isEmpty($data['merchant']['merchantDetail']['licensePhoto']))
            {
                return [
                    'status'=>1,
                    'message'=>'请上传营业执照图片！'
                ];

            }


            if(Verify::isEmpty($data['merchant']['merchantDetail']['businessLicense']))
            {
                return [
                    'status'=>1,
                    'message'=>'请填写营业执照编号！'
                ];

            }


            $idphoto=explode(';', $data['merchant']['merchantDetail']['indentityPhoto']);

            if(Verify::isEmpty($idphoto[0])||Verify::isEmpty($idphoto[1]))
            {
                return [
                    'status'=>1,
                    'message'=>'请上传身份证图片！'
                ];

            }

      

            if(Verify::isEmpty($request['indentityPhoto_c']))
            {
                return [
                    'status'=>1,
                    'message'=>'请上传手持身份证照片！'
                ];

            }



        }
        catch(\Exception $e)
        {

/*                return [
                    'status'=>1,
                    'message'=>$e->getMessage()."\n".$e->getLine()."\n".$e->getFile()
                ];*/
self::log($e->getMessage()."\n".$e->getLine()."\n".$e->getFile());
            return [
                'status'=>'1',
                'messages'=>'意外发生了，请联系服务商！'
            ];

        }






// 向浦发接口发送参数
                //获取浦发 进件资料 接口配置
                try
                {
                    $pufaconfig = PufaConfig::where("id", '1')->first();
                    $request_url =($pufaconfig->infourl);
                    $key=($pufaconfig->security_key);
                    $partner=($pufaconfig->partner);
                }
                catch(\Exception $e)
                {

self::log($e->getMessage().$e->getFile().$e->getLine(),'./chencai_jinjiandata.txt');

        $return['status']=1;
        $return['message']='服务商数据库异常，请联系服务商！';
        return json_encode($return); 

                }



                try
                {

// 拼接全地址
$data['merchant']['merchantDetail']['address']=trim($request['preaddress']).$data['merchant']['merchantDetail']['address'];
$data['merchant']['bankAccount']['address']=trim($request['preaddress']).$data['merchant']['bankAccount']['address'];
// 补充0
$data['merchant']['bankAccount']['province']=str_pad($data['merchant']['bankAccount']['province'],6,'0',STR_PAD_LEFT);
$data['merchant']['bankAccount']['city']=str_pad($data['merchant']['bankAccount']['city'],6,'0',STR_PAD_LEFT);




                    $commondata=[
                        'partner'=>$partner, 
                        'serviceName'=>'normal_mch_add',
                        'dataType'=>'xml',
                        'charset'=>'UTF-8',
                        'data'=>Tools::toXmlTwo($data),
                        'dataSign'=>'',
                    ];

                    // 生成签名、生成xml数据
                    $signdata = Tools::createjjSign($commondata, $key);

self::log($signdata);
                    // 向浦发接口发送xml下单数据
                    $xmlresult = Tools::curl($signdata,$request_url);//获取银行xml数据
                    
                    //浦发接口返回数据     
                    if($xmlresult)
                    {
self::log($xmlresult);
                        $apiresult=Tools::xml_to_array($xmlresult);

                        if(isset($apiresult)&&isset($apiresult['response'])&&isset($apiresult['response']['isSuccess'])&&$apiresult['response']['isSuccess']=='T')
                        {
                            // 拿到浦发颁发的商户号，准备数据入库
                            $merchant_id=$apiresult['response']['merchant']['merchantId'];
                        }
                        else
                        {
                            $return['status']=1;
                            $pufamsg=isset($apiresult['response']['errorMsg'])?$apiresult['response']['errorMsg']:'请重试！';
                            $return['message']='进件失败：'.$pufamsg;
                            return $return;
                        }
                    }
                    else
                    {
                        $return['status']=1;
                        $return['message']='浦发接口调用失败！';
                        return $return;                            
                    }
                }
                catch(\Exception $e)
                {
self::log($e->getMessage()."\n".$e->getLine()."\n".$e->getFile());

                    $return['status']=1;
                    $return['message']='进件资料上传失败，服务商接口异常！';
                    return $return;

                }
                
// 将接口数据入库
                try
                {
                    $indata=[
                        'pay_status'=>1,//1表示不可收款
                        'user_id'=>trim($request['user_id']),
                        // 'user_name'=>trim($request['user_name'])?$request['user_name']:'0',
                        'store_id'=>$store_id,
                        'merchant_id'=>$merchant_id,
                        'store_name'=>$data['merchant']['merchantName'],
                        'merchant_short_name'=>$data['merchant']['merchantDetail']['merchantShortName'],
                        'fee_type'=>$feeType,
                        'mch_deal_type'=>$data['merchant']['mchDealType'],
                        'remark'=>$data['merchant']['remark'],
                        'shop_user'=>$data['merchant']['merchantDetail']['principal'],

                        'industr_id'=>$data['merchant']['merchantDetail']['industrId'],
                        'province'=>$province->code,
                        // 'province'=>$data['merchant']['merchantDetail']['province'],
                        // 'city'=>$data['merchant']['merchantDetail']['city'],
                        'city'=>$city->code,

                        'address'=>$data['merchant']['merchantDetail']['address'],
                        'tel'=>$data['merchant']['merchantDetail']['tel'],
                        'ch_pay_auth'=>$data['merchant']['chPayAuth'],
                        'email'=>$data['merchant']['merchantDetail']['email'],
                        'id_code'=>$data['merchant']['merchantDetail']['idCode'],
                        'indentity_photo_a'=>trim($request['indentityPhoto_a']),
                        'indentity_photo_b'=>trim($request['indentityPhoto_b']),
                        'indentity_photo_c'=>trim($request['indentityPhoto_c']),
                        'license_photo'=>trim($request['license']),

                        'district'=>$district->code,
                        'license_no'=>trim($request['business_license']),


                        'bank_id'=>$data['merchant']['bankAccount']['bankId'],
                        'account_code'=>$data['merchant']['bankAccount']['accountCode'],
                        'account_type'=>$data['merchant']['bankAccount']['accountType'],
                        'bank_name'=>$data['merchant']['bankAccount']['bankName'],
                        'bank_tel'=>$data['merchant']['bankAccount']['tel'],
                        'contact_line'=>$data['merchant']['bankAccount']['contactLine'],
                        'created_at'=>date('Y-m-d H:i:s'),
                        'rate'=>$pufaconfig->rate
                    ];
                    $lastid=PufaStores::insertGetId($indata);

                    if(!$lastid)
                    {

self::log('浦发开店成功，数据库插入失败：数据如下'.var_export($indata));

                        $return['status']=2;
                        $return['message']='开店失败，服务商数据库错误！';
                        return $return;

                    }

                $return['status']=2;
                $return['message']='恭喜您，开店成功！';
                return $return;

                }
                catch(\Exception $e)
                {
self::log($e->getMessage()."\n".$e->getLine()."\n".$e->getFile()."\n".$merchant_id."\n".var_export($indata,true));
                    $return['status']=1;
                    $return['message']='开店失败，服务商数据库错误！'.$e->getLine().$e->getFile().$e->getMessage();
                    return $return;
                }

    }
/*
    开店离成功差一步页面
*/
    public function storeSuccess(Request $request)
    {
        
        return view('pufa.store.success',['type'=>$request->type]);
        // return view('admin.pingan.store.index', compact('datapage', 'paginator'));
    }



/*

    签约浦发的商铺列表

*/
    public function storelist(Request $request)
    {

        $where = $request->all();
        $db=DB::table('pufa_stores')->where('pid','=','0');

        if(isset($where['store_name'])&&trim($where['store_name']))
        {
            $db->where('store_name','like',"%{$where['store_name']}%");
        }
        if (!Auth::user()->hasRole('admin')) {
            $db->where('user_id', Auth::user()->id);
        }
        $data=$db->get();


        // 所有推广员
        $allrecommender=[];
        $alluser=DB::table("users")->select(['name','id'])->get();
        if(!$alluser->isEmpty())
        {
            foreach($alluser as $user)
            {
                $allrecommender[$user->id]=$user->name;
            }
        }

        if (empty($data)) {
            $paginator = "";
            $datapage = "";
        } else {
            $data=$data->toArray();
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
        return view('pufa.store.storelist', compact('datapage', 'paginator','where','allrecommender'));
    }






/*
    修改店铺信息
*/
    public function storeEdit(Request $request)
    {

        $auth = Auth::user()->can('pufaStoreInfo');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        // 显示要修改的店铺信息
        if($request->isMethod('get'))
        {
            $store_id = $request->get("store_id");
            $store = PufaStores::where("store_id", $store_id)->first();
            //获取开通的支付类型 ---start
            $paytype = DB::table("pufa_pay_road")->where("store_id", $store_id)->where('status','2')->get();
            if(!empty($paytype))
            {
                $alltype=Pufa::start()->paytype;
                //循环出在用的支付通道
                foreach($alltype as $k=>&$v)
                {
                    foreach($paytype as $vv)
                    {
                        if($vv->idnum==$k)
                        {
                            $v['status']=1;
                            break;
                        }
                    }
                }
            }
            //获取开通的支付类型---end
            // 分店
            if($store->pid!='0')
            {
                $branchstore = PufaStores::where("id", $store->pid)->first();
                $info = DB::table("pufacqr_lsitsinfos")->where("store_id", $branchstore->store_id)->first();

            }
            else
            $info = DB::table("pufacqr_lsitsinfos")->where("store_id", $store->store_id)->first();

        // var_dump($info);die;
            return view("pufa.store.editStore", ['store' => $store, "info" => $info,'paytype'=>$alltype]);
        }


        // 处理表单提交的店铺修改内容
        if($request->isMethod('post'))
        {
            try{
                $id = $request->get("id");
                $list = DB::table("pufacqr_lsitsinfos")->where("id", $id)->first();
                $codes = $request->get("codes");
                if ($list->code_number !== $codes) {
                    if (DB::table("pufacqr_lsitsinfos")->where("code_number", $codes)->where("code_type", 0)->first()) {
                        $datass['store_id'] = $list->store_id;
                        $datass['code_type'] = 1;
                        $datass['store_name']=$request->get('merchant_short_name');
                        if (DB::table("pufacqr_lsitsinfos")->where("code_number", $codes)->update($datass)) {
                         DB::table("pufacqr_lsitsinfos")->where("id", $id)->delete();
                        }
                    } else {
                        return json_encode([
                        "message"=>"收款码编号不存在或已经被占用",
                        "status"=>1
                    ]);
                    }
                }
            }catch(\Exception $e){

            }
            
            try
            {
                $store_id=trim($request->get('store_id'));
                if(empty($store_id))
                {
                    return [
                        'status'=>1,
                        'message'=>'请选择正确的商户操作！'
                    ]; 
                }

                $store = PufaStores::where("store_id", $store_id)->first();
                if(empty($store))
                {
                    return ['message'=>'商铺不存在！','status'=>1];
                }

                DB::table("pufacqr_lsitsinfos")->where("store_id", $store_id)->update(['code_type'=>1]);

                $data_2=[
                    'merchant_short_name'=>trim($request->get('merchant_short_name')),
                    'merchant_pwd'=>trim($request->get('merchant_pwd')),
                    'wx_app_id'=>trim($request->get('wx_app_id')),
                    'wx_secret'=>trim($request->get('wx_secret')),
                    'pay_status'=>trim($request->get('pay_status')),
                ];
                $msg=$data_2['pay_status']==2?'已有支付类型后就可以收款了！':'<span style="color:red">该店铺已关闭收款功能！</span>';
                PufaStores::where("store_id", $store_id)->update($data_2);

// 处理支付方式接口------------start---

                $formrate=trim($request->get('rate'));//0.00668

                $rate=$formrate*1000;

                try
                {
                    $paytype=trim($request->get('paytype'));
                    $choose=explode(',',$paytype);//idnum 
                    $choose=array_filter($choose);

                $successidnum=[];//处理成功的支付类型的idnum数字代号
                    if(($choose))
                    {
                        // 获取服务商配置
                        $config = DB::table("alipay_isv_configs")->where("id", '1')->first();
                        if(empty($config))
                        {
                            return ['message'=>'服务商配置不能为空！','status'=>1];
                        }
                        $_config=DB::table('pufa_configs')->where('id','=','1')->first();
                        if(empty($_config))
                        {
                            return ['message'=>'服务商配置不能为空！','status'=>1];
                        }

                        $data=[
                            'trans_partner'=>$_config->trans_partner,//'商户在浦发的商户号',
                            'merchant_id'=>$store->merchant_id,//'商户在浦发的商户号',
                            'rate'=>$rate,//'服务商设置商户的费率',
                            'partner'=>$_config->partner,//'服务商合作号',
                            'key'=>$_config->security_key,//'服务商秘钥',
                            'pid'=>$config->pid,//'合作商支付宝pid号',
                            'store_id'=>$store_id,//'商户在服务商的id号',
            'request_url'=>$_config->infourl,
                            // 'idnum'=>$k,//'浦发支付类型数字代号',----主要判断这个
                        ];

                        $return=[];
                        foreach($choose as $v)
                        {
                            if(empty($v))
                                continue;
                            $data['idnum']=$v;
                            $return[$v]=$this->payType($data);
                        }


                        //修改失败的信息   
                        foreach($return as $kk=>$vv)
                        {
                            // 调用接口成功的支付方式
                            if($vv['status']==1)
                            {
                                $successidnum[]=$kk;
                                continue;
                            }
                            // 之前请求成功的支付方式
                            if($vv['status']==3)
                            {
                                $successidnum[]=$kk;
                                continue;
                            }

                            // 请求支付方式失败
                            $msg.='<br/>'.current(Pufa::start()->paytype[$kk]).'--<span style="color:red">'.$vv['message'].'</span><br/>';
                        }
                    }

                    // 将数据库支付类型设置为开启2
                    if(!empty($successidnum))
                    {
                        $idnumstr=implode(',', $successidnum);
                        DB::update('update pufa_pay_road set status = "2" where store_id="'.$store_id.'" and idnum in ('.$idnumstr.')');                        
                     
                        DB::update('update pufa_pay_road set status = "1" where store_id="'.$store_id.'" and idnum not in ('.$idnumstr.')');                        
                    }
                    else
                    {
                        // 将数据库其他没有开通的支付类型设置为关闭1
                        DB::update('update pufa_pay_road set status = "1" where store_id="'.$store_id.'" ');                        
                    }


                    // 把费率修改掉---start---------只有对开启的支付类型才会去修改费率，没有开启的支付类型是不会去修改的！
           
                    if(!empty($successidnum))
                    {
                           foreach($successidnum as $vvv)
                            {
                                $pufa=Pufa::start();
                                $pufa->partner=$_config->partner;
                                $pufa->request_url=$_config->infourl;
                                $pufa->key=$_config->security_key;

                                // $store_id = $request->get("store_id");
                                // $store = PufaStores::where("store_id", $store_id)->first();
                                $apiCode=key($pufa->paytype[$vvv]);
                                // $rate=9;//如果费率是  千分之6  ，此处填写  6
                                $result[$vvv]=$pufa->saveRate($store->merchant_id,$apiCode,$rate);
                            }

                            $setrate=false;
                            foreach($result as $idnumkey=>$re)
                            {
                                if($re['status']==2)
                                {
                                    $setrate=true;
                                }
                                else
                                {
                                    $msg.='<br/>'.current(Pufa::start()->paytype[$idnumkey]).'--<span style="color:orange">'.$re['message'].'</span><br/>';
                                    // $ratestr.=$re['message'];
                                }

                            }
                            //只要有一个成功，就去修改费率
                            if($setrate)
                            {
                                PufaStores::where("store_id", $store_id)->update(['rate'=>$formrate]);
                            }
                    }

                    // 把费率修改掉---end


                }
                catch(\Exception $e)
                {
                    return ['status'=>1,'message'=>'商铺普通信息修改成功，支付方式修改失败！'.$e->getMessage().$e->getLine()];
                }

// 处理支付方式接口------------end---

                
                return ['status'=>'1','message'=>'基本信息修改成功！'.$msg];


            }
            catch(\Exception $e)
            {
                return [
                    'status'=>1,
                    'message'=>'系统异常，请重试！'.$e->getLine().$e->getMessage()
                ];
            }
        }

    }
 
/*
    测试费率修改
*/

    public function testrate()
    {
            $_config=DB::table('pufa_configs')->where('id','=','1')->first();
        $pufa=Pufa::start();
        $pufa->partner=$_config->partner;
        $pufa->request_url=$_config->infourl;
        $pufa->key=$_config->security_key;


        $store_id = $request->get("store_id");
        $store = PufaStores::where("store_id", $store_id)->first();
        $apiCode='pay.weixin.micropay';
        $rate=9;//如果费率是  千分之6  ，此处填写  6
        $pufa->saveRate($store->merchant_id,$apiCode,$rate);



        die;
    }

 /*
    测试开通支付类型接口
     */
    public function testpaytype()
    {

        // 获取服务商配置
        $config = DB::table("alipay_isv_configs")->where("id", '1')->first();
        if(empty($config))
        {
            echo '服务商配置不能为空！';die;
        }
        $_config=DB::table('pufa_configs')->where('id','=','1')->first();
        if(empty($_config))
        {
            echo '服务商配置不能为空！';die;
        }
        // $store_id = $request->get("store_id",'f20170406161742789895');//服务商生成的商户识别号    pufa_stores    store_id
        $store_id='f20170406161742789895';
        $store = PufaStores::where("store_id", $store_id)->first();
        if(empty($store))
        {
            echo '没有这个商户！';die;
        }

        $data=[
            'merchant_id'=>$store->merchant_id,//'商户在浦发的商户号',
            'rate'=>'7',//'服务商设置商户的费率',
            'partner'=>$_config->partner,//'服务商合作号',
            'key'=>$_config->security_key,//'服务商秘钥',
            'pid'=>$config->pid,//'合作商支付宝pid号',
            'store_id'=>$store_id,//'商户在服务商的id号',
            'request_url'=>$_config->infourl,
            // 'idnum'=>$k,//'浦发支付类型数字代号',----主要判断这个


                            'trans_partner'=>$_config->trans_partner,//'商户在浦发的商户号',


        ];
        $pufa=Pufa::start();

        foreach($pufa->paytype as $k=>$v)
        {
            if(empty($v))
                continue;
            $data['idnum']=$k;
            $return[]=$this->payType($data);
        }




        var_dump($return);
        die;
    }

/*

    public $paytype=[
        '221'=>'浦发广州-微信三通道线下扫码|pay.weixin.native',      有这个类型
        '222'=>'浦发广州-微信三通道线下小额|pay.weixin.micropay',      有这个类型
        '223'=>'浦发广州-微信三通道公众账号|pay.weixin.jspay',
        '10000164'=>'浦发广州-支付宝三通道服务窗支付|pay.alipay.jspayv3',
        '10000165'=>'浦发广州-支付宝三通道扫码支付|pay.alipay.nativev3',
        '10000166'=>'浦发广州-支付宝三通道小额支付|pay.alipay.micropayv3',   不对，没有这个类型
    ];
*/

/*    public $paytype=[
        '221'=>['pay.weixin.native'=>'浦发广州-微信三通道线下扫码'],//'浦发广州-微信三通道线下扫码|pay.weixin.native',
        '222'=>['pay.weixin.micropay'=>'浦发广州-微信三通道线下小额'],//'浦发广州-微信三通道线下小额|pay.weixin.micropay',
        '223'=>['pay.weixin.jspay'=>'浦发广州-微信三通道公众账号'],//'浦发广州-微信三通道公众账号|pay.weixin.jspay',
        '10000164'=>['pay.alipay.jspayv3'=>'浦发广州-支付宝三通道服务窗支付'],//'浦发广州-支付宝三通道服务窗支付|pay.alipay.jspayv3',
        '10000165'=>['pay.alipay.nativev3'=>'浦发广州-支付宝三通道扫码支付'],//'浦发广州-支付宝三通道扫码支付|pay.alipay.nativev3',
        '10000166'=>['pay.alipay.micropayv3'=>'浦发广州-支付宝三通道小额支付'],//'浦发广州-支付宝三通道小额支付|pay.alipay.micropayv3',
    ];
*/
     /*
        支付类型接口调用
        入参：
        $data=[
            'merchant_id'=>'商户在浦发的商户号',
            'rate'=>'服务商设置商户的费率',
            'partner'=>'服务商合作号',
            'key'=>'服务商秘钥',
            'pid'=>'合作商支付宝pid号',
            'store_id'=>'商户在服务商的id号',
            'idnum'=>'浦发支付类型数字代号',
        ];

        返回：
            [1  失败]
            [2  成功]
            [3  已经设置过]
        成功开通后将直接入数据表pay_road   关键字段   store_id    idnum  status
    */
    public function payType($data)
    {

        $info = DB::table("pufa_pay_road")->where("store_id", $data['store_id'])->where("idnum", $data['idnum'])->first();

        if($info)
        {
            return ['status'=>3,'message'=>'已经设置过了！'];
        }

        // 调用浦发数据
        $pufa=Pufa::start();
        $pufa->request_url=$data['request_url'];
        $pufa->partner=$data['partner'];
        $pufa->key=$data['key'];
 
        $return=$pufa->payType($data['merchant_id'],$data['idnum'],$data['rate'],$data['pid'],$data['trans_partner']);

        if($return['status']!=2)
        {
            return $return;
        }
        $paytype=$return['data'];

        try
        {
            // 将支付通道入库  pufa_pay_road
            $indata=[
                'store_id'=>$data['store_id'],
                'name'=>current($pufa->paytype[$data['idnum']]),//中文描述
                'code'=>$paytype,//英文字符描述---使用接口的英文描述
                'idnum'=>$data['idnum'],//数字代号
                'status'=>'2',//开启
                // 'remark'=>$data['name'],
                'created_at'=>date('Y-m-d H:i:s')
            ];
            $insert=DB::table('pufa_pay_road')->insert($indata);
            if($insert)
            {
                return ['status'=>2,'message'=>'支付类型开启成功！'];
            }
        }
        catch(\Exception $e)
        {
            return ['status'=>1,'message'=>'支付类型开启失败，数据库异常！'.$e->getMessage()];
        }

        return ['status'=>1,'message'=>'支付类型开启失败！'];
    }


/*
    所有商铺流水

*/
    public function orderList(Request $request)
    {
// DB::enableQueryLog();

        if(Auth::user()->hasRole('admin'))
        {
            $sql='select a.*,b.store_name from orders a left join pufa_stores b on a.store_id=b.store_id where type="601" or type="602" or type="603" or type="604" order by created_at desc,updated_at desc';

        }
        else
        {
            $sql='select a.*,b.store_name from orders a left join pufa_stores b on a.store_id=b.store_id where (type="601" or type="602" or type="603" or type="604") and user_id="'.Auth::user()->id.'" order by created_at desc,updated_at desc';

        }
        $data = DB::select($sql);
 
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
        return view('pufa.store.orderlist', compact('datapage', 'paginator'));

    }





/*
    浦发分店列表

*/

    public function branchStore(Request $request)
    {
        $pid=$request->get('pid');

        $sql="select * from pufa_stores where pid={$pid}";
        $data = DB::select($sql);
        
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
        return view('pufa.branch.index', compact('datapage', 'paginator'));

    }

/*
    浦发分店添加

    code_number  二维码唯一
    store_id  唯一

*/

    public function BranchAdd(Request $request)
    {
        if($request->isMethod('get')){
            $pid=$request->get('pid');
            return view('pufa.branch.add');
        }

        if($request->isMethod('post')){
            $data = $request->all();
            $mainstore = PufaStores::where('id', $request->get('pid'))->first();
            $newstore=$mainstore->toArray();
            try {
                $newstore['merchant_short_name']=trim($data['merchant_short_name']);
                $newstore['shop_user']=trim($data['shop_user']);
                $newstore['bank_tel']=trim($data['bank_tel']);
                $newstore['store_id']='f' . date('Ymdhis', time()) . rand(100000, 999999);
                $newstore['pid']=trim($data['pid']);
                $newstore['created_at']=date('Y-m-d H:i:s');
                $newstore['updated_at']=date('Y-m-d H:i:s');
                unset($newstore['id']);

                if(empty($newstore['merchant_short_name']))
                    return json_encode(['status'=>'2','message'=>'商户简称不能为空']);

                $havestore = PufaStores::where('merchant_short_name', $newstore['merchant_short_name'])->first();
                if($havestore)
                    return json_encode(['status'=>'2','message'=>'商户简称不能相同！']);

                if(empty($newstore['shop_user']))
                    return json_encode(['status'=>'2','message'=>'商铺联系人不能为空！']);


                if(empty($newstore['bank_tel']))
                    return json_encode(['status'=>'2','message'=>'商铺联系人号码不能为空！']);

                $lastid=PufaStores::insertGetId($newstore);

                if($lastid)
                {
                    return json_encode(['status'=>'1','message'=>'分店添加成功！','url'=>url('/admin/pufa/branchStore?pid=' . $data['pid'])]);
                    return redirect('/admin/pufa/branchStore?pid=' . $data['pid']);
                }

            } catch (\Exception $e) {
                return json_encode(['status'=>'2','message'=>'系统异常！'.$e->getMessage().$e->getLine()]);
            }
            return json_encode(['status'=>'2','message'=>'系统异常！']);
        }



    }


/*
    浦发收银员收款码
    
    商户二维码
    收银员id


*/
    public function cashierQr(Request $request)
    {
        $store_id = $request->get('store_id');
        $cashier_id = $request->get('cashier_id');

        $pufaqrinfo = PufacqrLsitsinfo::where('store_id', $store_id)->first();

        if ($pufaqrinfo) {
            $code_number = $pufaqrinfo->code_number;
            if (!$code_number){
                dd('收款码不存在！请检查商户是否入驻成功');
            };
        } else {
            dd('收款码有误！请检查商户是否入驻成功');
        }
        $store_name = PufaStores::where('store_id', $store_id)->first()->store_name;
        $cashier_name=Merchant::where('id',$cashier_id)->first()->name;

        $code_url = url('api/pufa/payway?code_number=' . $code_number . '&cashier_id=' . $cashier_id);
        return view('pufa.qr.cashierQr', compact('code_url', 'cashier_name', 'store_name'));
    }



    public function add()
    {
        return view('pufa.store.add');
    }

    public function addPost(Request $request)
    {


        //获取浦发 进件资料 接口配置
        try
        {
            $pufaconfig = PufaConfig::where("id", '1')->first();
            $request_url =($pufaconfig->infourl);
            $key=($pufaconfig->security_key);
            $partner=($pufaconfig->partner);
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
            echo '存在数据库中的浦发进件资料配置找不到了';
            die;
        }

        try
        {

            $commondata=[
                'partner'=>$partner, 
                'serviceName'=>'pic_upload',
                'dataType'=>'xml',
                'charset'=>'UTF-8',
                'data'=>'<?xml version="1.0" encoding="UTF-8"?><picUpload><picType>1</picType></picUpload>',
                'dataSign'=>'',
            ];
            // 生成签名、生成xml数据
            $data = Tools::createjjSign($commondata, $key);

            $filepath=public_path().'/img/p3.jpg';//磁盘绝对路径

            // 向浦发接口发送xml下单数据
            $xmlresult = Tools::curl2($request_url,$data,[$filepath]);//获取银行xml数据

            if($xmlresult)
            {
                $restoarr=Tools::setContent($xmlresult);

                if($restoarr['isSuccess']=='T')
                {
                    echo '图片上传成功','<br>',$restoarr['pic'];
                    die;
                }

            }

            echo '图片上传失败，请尝试上传jpg的图片';
             

        }
        catch(\Exception $e)
        {
            echo '图片上传失败';die;
        }
 


        /*
            图片上传失败
array(4) {
  ["errorCode"]=>
  string(5) "S0008"
  ["errorMsg"]=>
  string(21) "图片格式不合法"
  ["isSuccess"]=>
  string(1) "F"
  ["pic"]=>
  string(0) ""
}
        
            图片上传成功
array(2) {
  ["isSuccess"]=>
  string(1) "T"
  ["pic"]=>
  string(40) "f5d33b68-cc88-4fbf-aae3-04c9650bdba6.jpg"
}
            

        */


    }

}
