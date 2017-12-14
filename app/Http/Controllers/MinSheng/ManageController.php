<?php

/*
	服务商管理民生后台

*/
namespace App\Http\Controllers\MinSheng;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\MinSheng\MinSheng;


use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

use App\Http\Controllers\Tools\Verify;

class ManageController extends Controller
{

    /*
        服务商配置民生
    */
    public function config(Request $request)
    {

        if($request->isMethod('get')){
            $data=DB::table('ms_configs')->where('id','=','1')->first();
            return view('minsheng.config', compact('data'));
        }

        if($request->isMethod('post')){
            $data=[
                'cooperator_t0'=>trim($request->cooperator_t0),
                'wx_app_id'=>trim($request->wx_app_id),
                'wx_secret'=>trim($request->wx_secret),
                'cooperator_t1'=>trim($request->cooperator_t1),
                'self_public_key'=>trim($request->self_public_key),
                'self_private_key'=>trim($request->self_private_key),
                'third_public_key'=>trim($request->third_public_key),
                'request_url'=>trim($request->request_url),
                'draw_fee'=>trim($request->draw_fee),
                'trade_rate'=>trim($request->trade_rate),
            ];
            $model=DB::table('ms_configs');
            $result=$model->where('id','=','1')->update($data);

            if($result)
            {
                return json_encode(['status'=>1,'message'=>'修改成功！']);
            }
            return json_encode(['status'=>0,'message'=>'修改失败，请重试！']);

        }
    }

    public function storeList(Request $request)
    {
        $pid=trim($request->get('pid','0'));
        $where=[];
        $db=DB::table('ms_stores');
        if($store_short_name=trim($request->get('store_short_name')))
        {
            $where['store_short_name']=$store_short_name;
            $db->where('ms_stores.store_short_name','like',"%{$store_short_name}%");
        }
        $sqlwhere=[];
        if(!Auth::user()->hasRole('admin')){
            $sqlwhere[]= ['user_id',Auth::user()->id];
        }

        $store_data = $db
            ->select(
                DB::raw("
                ms_stores.pid,
                ms_stores.id,
                ms_stores.store_id,
                if(ms_stores.status='2','门店开启收银','门店已关闭收银') status,
                ms_stores.cooperator,
                ms_stores.store_short_name,
                ms_stores.draw_fee,
                ms_stores.trade_rate,
                ms_stores.remark,
                ms_stores.created_at,

                users.name

                ")
            )
            ->leftJoin('users','ms_stores.user_id','=','users.id')
            ->groupBy(
                'ms_stores.store_id',
                'ms_stores.pid',
                'ms_stores.id',
                'ms_stores.cooperator',
                'ms_stores.store_short_name',
                'ms_stores.draw_fee',
                'ms_stores.trade_rate',
                'ms_stores.status',
                'ms_stores.remark',
                'ms_stores.created_at',

                'users.name'

            )
            // ->where('user_id', Auth::user()->id)
            ->where('ms_stores.pid', $pid)
            ->where($sqlwhere)
            ->orderby('created_at','desc')
            ->paginate(10);

        $pay_ways=[];
        if(!$store_data->isEmpty())
        {
            $store_ids=[];
            foreach($store_data as $store)
            {
                $store_ids[]=$store->store_id;
            }
            $pay_ways=DB::table('ms_pay_way')->whereIn('store_id',$store_ids)->get();
        }

        // var_dump($store_data);die;

        return view('minsheng.storelist', compact('store_data','where,','pay_ways'));
    }

    public function saveRate(Request $request)
    {
        if($request->isMethod('get')){

            $store_id=trim($request->get('store_id'));

            $store=DB::table('ms_stores')->where('store_id',$store_id)->first();
            $pay_way=DB::table('ms_pay_way')->where('store_id',$store_id)->first();


            if(empty($store))
                return view('system.notice',['message'=>'该通道所在店铺不存在！']);
            if(($store->pid!=0))
                return view('system.notice',['message'=>'分店无法修改费率！']);

            return view("minsheng.saverate", ['store' => $store,'pay_way'=>$pay_way]);
        }

        if($request->isMethod('post')){
            $store_id=trim($request->get('store_id'));
            $store=DB::table('ms_stores')->where('store_id',$store_id)->first();
            if(empty($store))
                return json_encode(['status'=>'2','message'=>'该通道所在店铺不存在！']);
            if(($store->pid!=0))
                return json_encode(['status'=>'2','message'=>'分店无法修改费率！']);
            $data=[
                'draw_fee'=>trim($request->get('draw_fee')),
                'trade_rate'=>trim($request->get('trade_rate')),
            ];


            $ms=$this->initms();//民生接口类
            $ms->drawFee=(float)$data['draw_fee'];//data['draw_fee'];
            $ms->tradeRate=(float)$data['trade_rate'];//$data['trade_rate'];
            $ms->request_url=$this->msconfig->request_url;



            $district=DB::table('ms_region')->where('code',$store->district_code)->where('level','4')->first();
            $city=DB::table('ms_region')->where('code',$district->pid)->where('level','3')->first();
            $area=[
                'province'=>$city->pid,
                'city'=>$district->pid,
                'district'=>$district->code,
            ];



            /*



                $save_return=$this->saveMSapi($ms,array_merge($data,
                    ['rand_id'=>$pay->rand_id,

                    'usertype'=>str_pad($pay->usertype,2,'0',STR_PAD_LEFT),
                    'id_card'=>$pay->id_card,
                    'category'=>$pay->category,
                    'province'=>$area['province'],
                    'city'=>$area['city'],
                    'district'=>$area['district'],
                    'store_user'=>$pay->store_user,
                    ]

                    ),$store->cooperator);

            */
            //修改支付宝费率
            $ali_status='2';
            $ali_message='';
            $pay_way='ZFBZF';
            $pay=DB::table('ms_pay_way')->where('store_id',$store_id)->where('pay_way',$pay_way)->first();
            if(!empty($pay)&&$pay->status=='2')
            {
                $cdata=[
                    'store_short_name'=>$pay->store_short_name,
                    'store_address'=>$pay->store_address,
                    'store_phone'=>$pay->store_phone,
                    'bank_type'=>$pay->bank_type,
                    'bank_name'=>$pay->bank_name,
                    'bank_no'=>$pay->bank_no,
                    // 'bank_type'=>$request->get('bank_type'),
                    // 'bank_name'=>$request->get('bank_name'),
                    // 'bank_no'=>$request->get('bank_no'),
                    'rand_id'=>$pay->rand_id,
                    'usertype'=>str_pad($pay->usertype,2,'0',STR_PAD_LEFT),
                    'id_card'=>$pay->id_card,
                    'category'=>$pay->category,
                    'province'=>$area['province'],
                    'city'=>$area['city'],
                    'district'=>$area['district'],
                    'store_user'=>$pay->store_user,
                ];
                if($pay->contact_name){
                    $cdata['contact_name']=$pay->contact_name;
                }
                $save_return=$this->saveMSapi($ms,$cdata,$store->cooperator);
                if($save_return['status']=='1')
                {
                    $ali_status='1';
                    $ali_message.='支付宝费率修改成功！<br/>';

                    DB::table('ms_pay_way')->where('store_id',$store_id)->where('pay_way',$pay_way)->update(['draw_fee'=>$ms->drawFee,'trade_rate'=>$ms->tradeRate,
                        'bank_type'=>$pay->bank_type,
                        'bank_name'=>$pay->bank_name,
                        'bank_no'=>$pay->bank_no,
                        // 'bank_name'=>$request->get('bank_name'),
                        // 'bank_no'=>$request->get('bank_no'),
                        'store_user'=>$pay->store_user]);
                }
                else
                {
                    $ali_status='2';
                    $ali_message.='支付宝费率设置失败：'.$save_return['message'].'<br/>';
                }
            }
            else
            {
                $ali_status='2';
                $ali_message.='支付宝费率目前不能修改！<br/>';
            }

            sleep(0.2);

            // 修改微信费率
            $wx_status='2';
            $wx_message='';
            $pay_way='WXZF';
            $pay=DB::table('ms_pay_way')->where('store_id',$store_id)->where('pay_way',$pay_way)->first();
            if(!empty($pay)&&$pay->status=='2')
            {
                $cdata=[
                    'store_short_name'=>$pay->store_short_name,
                    'store_address'=>$pay->store_address,
                    'store_phone'=>$pay->store_phone,
                    'bank_type'=>$pay->bank_type,
                    'bank_name'=>$pay->bank_name,
                    'bank_no'=>$pay->bank_no,
                    'rand_id'=>$pay->rand_id,
                    'usertype'=>str_pad($pay->usertype,2,'0',STR_PAD_LEFT),
                    'id_card'=>$pay->id_card,
                    'category'=>$pay->category,
                    'province'=>$area['province'],
                    'city'=>$area['city'],
                    'district'=>$area['district'],
                    'store_user'=>$pay->store_user,
                ];
                if($pay->contact_name){
                    $cdata['contact_name']=$pay->contact_name;
                }
                $save_return=$this->saveMSapi($ms,$cdata,$store->cooperator);
                if($save_return['status']=='1')
                {
                    $wx_status='1';
                    $wx_message.='微信费率修改成功！<br/>';
                    DB::table('ms_pay_way')->where('store_id',$store_id)->where('pay_way',$pay_way)->update(['draw_fee'=>$ms->drawFee,'trade_rate'=>$ms->tradeRate,
                        'bank_type'=>$pay->bank_type,
                        'bank_name'=>$pay->bank_name,
                        'bank_no'=>$pay->bank_no,
                        'store_user'=>$pay->store_user]);
                }
                else
                {
                    $wx_status='2';
                    $wx_message.='微信费率设置失败：'.$save_return['message'].'<br/>';
                }
            }
            else
            {
                $wx_status='2';
                $wx_message.='微信费率目前不能修改！<br/>';
            }

            if($wx_status=='1'&&$ali_status=='1')
            {
                DB::table('ms_stores')->where('store_id',$store_id)->update(['draw_fee'=>$data['draw_fee'],'trade_rate'=>$data['trade_rate']]);
                return json_encode(['status'=>'1','message'=>$ali_message.$wx_message]);
            }

            return json_encode(['status'=>'2','message'=>$ali_message.$wx_message]);


        }
    }


    /*
        普通信息修改：只能修改服务商数据库信息，ms_stores
    */
    public function normalEdit(Request $request)
    {
        if($request->isMethod('get')){
            $store_id=trim($request->get('store_id'));
            $store=DB::table('ms_stores')->where('store_id',$store_id)->first();
            if(empty($store))
                return view('system.notice',['message'=>'该店铺不存在！']);
            $main_store='';
            if($store->pid!='0')
                $main_store=DB::table('ms_stores')->where('store_id',$store->pid)->first();

            $users=DB::table('users')->get();
            $recommenders=[];
            foreach($users as $user)
            {
                $recommenders[$user->id]=$user->name;
            }

            // 读出二维码的店铺收款-------分店的话传主店的二维码号，同时传分店自己的store_id
            if($store->pid)
                $info=DB::table('mscqr_lsitsinfos')->where('store_id',$store->pid)->first();
            else
                $info=DB::table('mscqr_lsitsinfos')->where('store_id',$store_id)->first();

            return view("minsheng.normalEdit", ['store' => $store,'info'=>$info,'main_store'=>$main_store,'recommenders'=>$recommenders]);
        }

        if($request->isMethod('post')){
            $store_id=trim($request->get('store_id'));
            $store=DB::table('ms_stores')->where('store_id',$store_id)->first();

            if(empty($store))
                return json_encode(['status'=>'2','message'=>'商铺不存在！']);

            $data=[
                'store_short_name'=>trim($request->get('store_short_name')),
                'store_phone'=>trim($request->get('store_phone')),
                'store_user'=>trim($request->get('store_user')),
                'status'=>trim($request->get('status')),
            ];

            $have=DB::table('ms_stores')->where('store_short_name',$data['store_short_name'])->where('store_id','!=',$store_id)->count();
            if($have>0)
                return json_encode(['status'=>'2','message'=>'商户名已被占用！']);
            DB::table('ms_stores')->where('store_id',$store_id)->update($data);

            return json_encode(['status'=>'1','message'=>'修改成功！']);

        }


    }

    /*
        商户信息在民生的修改，只能一个一个的修改支付宝和微信-----成功入驻支付通道的才可以操作

        1成功   2失败
    */
    public function storeEdit(Request $request)
    {

        if($request->isMethod('get')){

            $pay_way=trim($request->get('pay_way'));
            $pay_way_id=trim($request->get('pay_way_id'));
            $pay=DB::table('ms_pay_way')->where('id',$pay_way_id)->first();
            if(empty($pay))
                return view('system.notice',['message'=>'支付通道不存在！']);

            $store=DB::table('ms_stores')->where('store_id',$pay->store_id)->first();
            if(empty($store))
                return view('system.notice',['message'=>'该通道所在店铺不存在！']);
            $msconfig=DB::table('ms_configs')->where('id','1')->first();
            return view("minsheng.editStore", ['store' => $store,'pay'=>$pay,'msconfig'=>$msconfig]);
        }

        if($request->isMethod('post')){

            try
            {

                $pay_id=trim($request->get('pay_id'));
                $pay=DB::table('ms_pay_way')->where('id',$pay_id)->first();
                if(empty($pay))
                    return json_encode(['status'=>'2','message'=>'该通道不存在！']);

                $store=DB::table('ms_stores')->where('store_id',$pay->store_id)->first();
                if(empty($store))
                    return json_encode(['status'=>'2','message'=>'该通道所在商铺不存在！']);
                $data=[
                    'store_short_name'=>trim($request->get('store_short_name')),
                    'store_address'=>trim($request->get('store_address')),
                    'store_phone'=>trim($request->get('store_phone')),
                    'bank_type'=>trim($request->get('bankName')),
                    'bank_no'=>trim($request->get('bank_no')),
                    'store_user'=>trim($request->get('store_user')),
                    'date'=>date('Ymdhis'),
                    'draw_fee'=>$pay->draw_fee,
                    'trade_rate'=>$pay->trade_rate,
                ];

                $verify=$this->verifyform2($data);
                if($verify['status']==2)
                    return json_encode($verify);

                $ms=$this->initms();//民生接口类
                $ms->drawFee=(float)$data['draw_fee'];//data['draw_fee'];
                $ms->tradeRate=(float)$data['trade_rate'];//$data['trade_rate'];
                $ms->request_url=$this->msconfig->request_url;


                if($this->msconfig->cooperator_t1==$store->cooperator)
                {
                    $bank=DB::table('ms_bank')->where('bank_type',$data['bank_type'])->first();
                    if(empty($bank))
                        return json_encode(['status'=>'2','message'=>'开户行不存在！']);

                    $data['bank_type']=$bank->bank_type;
                    $data['bank_name']=$bank->bank_name;
                }
                else
                {
                    $data['bank_type']='';
                    $data['bank_name']='';
                }



                $district=DB::table('ms_region')->where('code',$store->district_code)->where('level','4')->first();
                $city=DB::table('ms_region')->where('code',$district->pid)->where('level','3')->first();
                $area=[
                    'province'=>$city->pid,
                    'city'=>$district->pid,
                    'district'=>$district->code,
                ];




                $save_return=$this->saveMSapi($ms,array_merge($data,
                    ['rand_id'=>$pay->rand_id,'usertype'=>str_pad($pay->usertype,2,'0',STR_PAD_LEFT),'id_card'=>$pay->id_card,'category'=>$pay->category,'province'=>$area['province'],'city'=>$area['city'],'district'=>$area['district'],'store_user'=>$pay->store_user]

                ),$store->cooperator);

                if($save_return['status']=='1')
                {
                    unset($data['date']);
                    $k=DB::table('ms_pay_way')->where('id',$pay_id)->update($data);
                }
                return json_encode($save_return);
            }
            catch(\Exception $e)
            {
                return json_encode(['status'=>'2','message'=>$e->getMessage().$e->getLine()]);
            }

        }
    }


    protected function saveMSapi($ms,$data,$cooperator)
    {
        try
        {
            /////////修改已开通的支付方式的费率/////start/////////
            $cin=[
                'date'=>date('YmdHis'),
                'store_short_name'=>$data['store_short_name'],//$store->store_short_name,//流水号--服务商
                'store_address'=>$data['store_address'],//$store->store_address,//流水号--服务商
                'store_phone'=>$data['store_phone'],//$store->store_phone,//流水号--服务商
                // 'category'=>$way=='ZFBZF'?$ali_cate:$wx_cate,//$data['store_address'],//$pay_way_info->category,//流水号--服务商
                // 'id_card'=>$store->id_card,//流水号--服务商
                'bank_no'=>$data['bank_no'],//$store->bank_no,//流水号--服务商
                'store_user'=>$data['store_user'],//$store->store_user,//流水号--服务商
                'bank_type'=>$data['bank_type'],//$store->bank_type,//流水号--服务商
                'bank_name'=>$data['bank_name'],//$store->bank_name,//流水号--服务商
                'cooperator'=>$cooperator,//流水号--服务商
                'callBack'=>url('api/minsheng/savenotify'),//流水号--服务商
                'rand_id'=>$data['rand_id'],//流水号--服务商
            ];

            if(isset($data['usertype']))
            {
                $cin['usertype']=$data['usertype'];
            }

            if(isset($data['id_card']))
            {
                $cin['id_card']=$data['id_card'];
            }

            if(isset($data['category']))
            {
                $cin['category']=$data['category'];
            }

            if(isset($data['contact_name']))
            {
                $cin['contact_name']=$data['contact_name'];
            }

            if(isset($data['province']))
            {
                $cin['province']=$data['province'];
            }


            if(isset($data['store_user']))
            {
                $cin['store_user']=$data['store_user'];
            }



            if(isset($data['city']))
            {
                $cin['city']=$data['city'];
            }



            if(isset($data['district']))
            {
                $cin['district']=$data['district'];
            }




            // var_dump($cin);die;

            $api_return =$ms->saveInfo($cin);
            // return ['status'=>'2','message'=>'what happend'];

            switch($api_return['status'])
            {
                case 1:
                    return ['status'=>'2','message'=>$api_return['message']];
                    break;

                case 3:
                    return ['status'=>'1','message'=>'该通道资料修改成功！'];
                    break;
            }

            return ['status'=>'2','message'=>$api_return['message']];
        }
        catch(\Exception $e)
        {
            return ['status'=>'2','message'=>$e->getMessage().$e->getLine()];
        }
/////////修改已开通的支付方式的费率/////end/////////     
    }

    // 初始化民生接口类
    protected $msconfig;
    protected function initms()
    {
        // 接口工具参数准备
        $ms=MinSheng::start();
        $_config=DB::table('ms_configs')->where('id','=','1')->first();
        $this->msconfig=$_config;
        MinSheng::$rsa->self_public_key=MinSheng::$rsa->matePubKey($_config->self_public_key);
        MinSheng::$rsa->self_private_key=MinSheng::$rsa->matePriKey($_config->self_private_key);
        MinSheng::$rsa->third_public_key=MinSheng::$rsa->matePubKey($_config->third_public_key);
        return $ms;

    }

    /*
        对于失败的进件，更换流水号重新进件

        如果是因为审核失败的情况，换个请求流水号，但是合作方商户编号不变。
    */
    public function saveStoreAdd(Request $request)
    {

        if($request->isMethod('get')){

            $pay_way=trim($request->get('pay_way'));
            $pay_way_id=trim($request->get('pay_way_id'));
            $pay=DB::table('ms_pay_way')->where('id',$pay_way_id)->first();
            if(empty($pay))
            {
                return view('system.notice',['message'=>'支付通道不存在！']);
            }

            $store=DB::table('ms_stores')->where('store_id',$pay->store_id)->first();
            if(empty($store))
            {
                return view('system.notice',['message'=>'该通道所在主店信息不存在！']);
            }

            $allcate=DB::table('ms_cate')->get();
            $allcate=json_decode(json_encode($allcate),true);
            foreach($allcate as &$cate)
            {
                if(trim($cate['ali_cate'])==trim($pay->category)&&$cate['level']=='3')
                {
                    $cate['choice']=true;
                }
            }

// 新增支付宝省市区

            $district=DB::table('ms_region')->where('code',$store->district_code)->where('level','4')->first();
            $city=DB::table('ms_region')->where('code',$district->pid)->where('level','3')->first();
            $area=[
                'province'=>$city->pid,
                'city'=>$district->pid,
                'district'=>$district->code,
            ];




            $msconfig=DB::table('ms_configs')->where('id','1')->first();
            $pid=$request->get('pid');
            return view('minsheng.readdstore',['pay'=>$pay,'store'=>$store,'msconfig'=>$msconfig,'allcate'=>$allcate,'area'=>$area]);
        }






        // 1修改成功，2失败
        if($request->isMethod('post')){
            $pay_id=trim($request->get('pay_id','33'));
            if(empty($pay_id))
            {
                return json_encode(['status'=>'2','message'=>'支付通道不存在！']);
            }
            $pay=DB::table('ms_pay_way')->where('id',$pay_id)->first();
            if(empty($pay))
                return json_encode(['status'=>'2','message'=>'支付通道不存在！']);

            $store=DB::table('ms_stores')->where('store_id',$pay->store_id)->first();
            if(empty($store))
                return json_encode(['status'=>'2','message'=>'主店铺信息不存在！']);

            $data=[
                'store_name'=>trim($request->get('store_name')),
                'store_short_name'=>trim($request->get('store_short_name')),
                'store_address'=>trim($request->get('store_address')),
                'store_phone'=>trim($request->get('store_phone')),
                'store_user'=>trim($request->get('store_user')),
                'category'=>trim($request->get('category')),
                'bank_name'=>trim($request->get('bankName')),
                'contact_name'=>trim($request->get('contact_name')),
                'id_card'=>trim($request->get('id_card')),
                'bank_no'=>trim($request->get('bank_no')),
                'usertype'=>str_pad(trim($request->get('usertype')),2,'0',STR_PAD_LEFT)
            ];

// echo $data['usertype'];

// die;
            // 新加地址
            $newadd=[
                'preaddress'=>trim($request->get('preaddress')),
                'district_code'=>trim($request->get('district')),
            ];

// var_dump(mb_substr($data['store_address'],strrpos($data['store_address'], ')')+1));die;


            $address=$newadd['preaddress'].mb_substr($data['store_address'],strrpos($data['store_address'], ')')+1);
            $data['store_address']=$address;

            // $data['district_code']=$newadd['district_code'];




// 新增支付宝省市区

            $district=DB::table('ms_region')->where('code',$newadd['district_code'])->where('level','4')->first();
            $city=DB::table('ms_region')->where('code',$district->pid)->where('level','3')->first();
            $area=[
                'province'=>$city->pid,
                'city'=>$district->pid,
                'district'=>$district->code,
            ];





            $verify=$this->verifyform($data);
            if($verify['status']==2)
                return json_encode($verify);


            $apidata=[
                'payWay'=>$pay->pay_way,

                'merchantName'=>$data['store_name'],
                'shortName'=>$data['store_short_name'],
                'merchantAddress'=>$data['store_address'],
                'contactName'=>$data['contact_name'],
                'servicePhone'=>$data['store_phone'],
                'category'=>$data['category'],
                'idCard'=>$data['id_card'],
                'accNo'=>$data['bank_no'],
                'accName'=>$data['store_user'],
                'usertype'=>$data['usertype'],
            ];

            $ms=$this->initms();
            // t1交易需要传入联行号
            if($store->cooperator==$this->msconfig->cooperator_t1)
            {
                $bank_type=trim($request->get('bankName'));
                $bank=DB::table('ms_bank')->where('bank_type',$bank_type)->first();
                if(empty($bank))
                    return json_encode(['status'=>'2','message'=>'开户行不存在！']);

                $apidata['bank_type']=$data['bank_type']=$bank->bank_type;
                $apidata['bank_name']=$data['bank_name']=$bank->bank_name;

            }


            //其他民生业务参数---服务商事先配置的参数
            $ms->drawFee=(float)$this->msconfig->draw_fee;
            $ms->tradeRate=(float)$this->msconfig->trade_rate;
            $ms->request_url=$this->msconfig->request_url;//进件地址

            $rand_id='m'.MinSheng::randnum();//服务商流水号，服务商处唯一
            $apireturn=$ms->makeInfo(array_merge($apidata,['date'=>date('Ymdhis'),'cooperator'=>$store->cooperator,'callBack'=>url('api/minsheng/infonotify'),'ext'=>'']),$rand_id,$area);

            $data['rand_id']=$rand_id;//更换流水号
            switch($apireturn['status'])
            {
                // 失败
                case 1:
                    $data['status']='3';
                    $data['remark']=$apireturn['message'];
                    DB::table('ms_pay_way')->where('id',$pay_id)->update($data);
                    return json_encode(['status'=>'2','message'=>'开户失败！'.$apireturn['message']]);
                    break;

                // 正在审核
                case 2:
                    $data['status']='1';
                    DB::table('ms_stores')->where('store_id',$pay->store_id)->update(['district_code'=>$district->code]);
                    DB::table('ms_pay_way')->where('id',$pay_id)->update($data);
                    return json_encode(['status'=>'1','message'=>'正在审核：'.$apireturn['message']]);
                    break;
                // 成功
                case 3:
                    $data['status']='3';
                    $data['remark']='该通道进件成功！';
                    DB::table('ms_stores')->where('store_id',$pay->store_id)->update(['district_code'=>$district->code]);
                    DB::table('ms_pay_way')->where('id',$pay_id)->update($data);
                    return json_encode(['status'=>'1','message'=>'通道开启成功！']);
                    break;

            }


        }

    }







    public function order()
    {
        $typeIn=[501,502];
        $where=[];
        if (!Auth::user()->hasRole('admin')) {
            $where[]=['user_id',auth()->user()->id];
        }
        $order = DB::table("orders")
            ->join("ms_stores", "orders.store_id", "=", "ms_stores.store_id")
            ->whereIn("orders.type",$typeIn)
            ->where($where)
            ->select("orders.remark",'orders.out_trade_no', "orders.store_id", 'ms_stores.store_short_name', "orders.created_at", "orders.updated_at", "orders.total_amount", "orders.type", "orders.pay_status","orders.merchant_id")
            ->orderBy("orders.created_at", "desc")
            ->paginate(9);
        return view('minsheng.order', compact('order'));
    }












    /*
        浦发分店列表

    */

    public function branchStore(Request $request)
    {
        $pid=$request->get('pid');

        $sql="select * from ms_stores where pid='{$pid}'";
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
        return view('minsheng.branch.index', compact('datapage', 'paginator'));

    }

    /*
        浦发分店添加

        code_number  二维码唯一
        store_id  唯一

    */

    public function BranchAdd(Request $request)
    {
        if($request->isMethod('get')){
            $store_id=$request->get('pid');
            return view('minsheng.branch.add',['store_id'=>$store_id]);
        }

        if($request->isMethod('post')){
            $data = $request->all();
            $store_id=trim($data['pid']);
            $mainstore = DB::table('ms_stores')->where('store_id', $store_id)->first();
            $newstore=json_decode(json_encode($mainstore),true);
            try {
                $newstore['store_short_name']=trim($data['store_short_name']);
                $newstore['store_user']=trim($data['store_user']);
                $newstore['store_phone']=trim($data['store_phone']);
                $newstore['store_id']='m' . date('Ymdhis', time()) . rand(100000, 999999);
                $newstore['pid']=$store_id;
                $newstore['created_at']=date('Y-m-d H:i:s');
                $newstore['updated_at']=date('Y-m-d H:i:s');
                unset($newstore['id']);


                if(empty($newstore['store_short_name']))
                    return json_encode(['status'=>'2','message'=>'商户简称不能为空']);

                $havestore = DB::table('ms_stores')->where('store_short_name', $newstore['store_short_name'])->first();
                if($havestore)
                    return json_encode(['status'=>'2','message'=>'商户简称不能相同！']);

                if(empty($newstore['store_user']))
                    return json_encode(['status'=>'2','message'=>'商铺联系人不能为空！']);


                if(empty($newstore['store_phone']))
                    return json_encode(['status'=>'2','message'=>'商铺联系人号码不能为空！']);


                $lastid=DB::table('ms_stores')->insert($newstore);

                if($lastid)
                {
                    return json_encode(['status'=>'1','message'=>'分店添加成功！','url'=>url('/admin/minsheng/storeList?pid=' . $store_id)]);
                    return redirect('/admin/minsheng/branchStore?pid=' . $data['pid']);
                }

            } catch (\Exception $e) {
                return json_encode(['status'=>'2','message'=>'系统异常！'.$e->getMessage().$e->getLine()]);
            }
            return json_encode(['status'=>'2','message'=>'系统异常！']);
        }



    }


    /*
        验证数据
    */
    protected function verifyform($data)
    {


        if(!(Verify::length($data['store_name'],3,null,1)))
        {
            return ['status'=>'2','message'=>'商户名称至少3个字符！'];
        }

        if(!(Verify::length($data['store_short_name'],3,null,1)))
        {
            return ['status'=>'2','message'=>'商户简称至少3个字符！'];
        }

        if(Verify::isEmpty($data['store_address']))
        {
            return ['status'=>'2','message'=>'店铺地址不能为空！'];
        }

        if(!Verify::isMobile($data['store_phone']))
        {
            return ['status'=>'2','message'=>'请输入正确的手机号码！'];
        }

        if(Verify::isEmpty($data['category']))
        {
            return ['status'=>'2','message'=>'请选择行业分类！'];
        }

        if(!Verify::length($data['id_card'],15,18,3))
        {
            return ['status'=>'2','message'=>'请输入正确的身份证号码！'];
        }

        if(!Verify::allnum($data['bank_no']))
        {
            return ['status'=>'2','message'=>'收款人银行卡号不能为空！'];
        }

        if(Verify::isEmpty($data['store_user']))
        {
            return ['status'=>'2','message'=>'收款人不能为空！'];
        }

        return ['status'=>'1','message'=>'验证通过！'];
    }


    /*
        验证数据   bank_type
    */
    protected function verifyform2($data)
    {

        if(!(Verify::length($data['store_short_name'],3,null,1)))
        {
            return ['status'=>'2','message'=>'商户简称至少3个字符！'];
        }

        if(Verify::isEmpty($data['store_address']))
        {
            return ['status'=>'2','message'=>'店铺地址不能为空！'];
        }

        if(!Verify::isMobile($data['store_phone']))
        {
            return ['status'=>'2','message'=>'请输入正确的手机号码！'];
        }


        if(!Verify::allnum($data['bank_no']))
        {
            return ['status'=>'2','message'=>'收款人银行卡号不能为空！'];
        }

        if(Verify::isEmpty($data['store_user']))
        {
            return ['status'=>'2','message'=>'收款人不能为空！'];
        }

        return ['status'=>'1','message'=>'验证通过！'];
    }



    public function setwxsubappid(Request $request)
    {



        try
        {

            $store_id=$request->get('store_id');


            $faildata=DB::table('ms_pay_way')->where('store_id',$store_id)->where('status','1')->get();

            if(!$faildata->isEmpty())
            {
                // 接口工具参数准备
                $ms=MinSheng::start();
                $_config=DB::table('ms_configs')->where('id','=','1')->first();

                $this->msconfig=$_config;
                MinSheng::$rsa->self_public_key=MinSheng::$rsa->matePubKey($_config->self_public_key);
                MinSheng::$rsa->self_private_key=MinSheng::$rsa->matePriKey($_config->self_private_key);
                MinSheng::$rsa->third_public_key=MinSheng::$rsa->matePubKey($_config->third_public_key);

                $config=DB::table('ms_configs')->where('id','=','1')->first();

                $ms->request_url=$config->request_url;//进件地址

                foreach($faildata as $pay)
                {

                    $ok=$ms->searchStore($pay,$store_id);

                    if($ok['status']=='2')
                    {

                        if(!empty($ok['merchant_id']))
                        {

                            $ko=DB::table('ms_pay_way')->where('rand_id',$pay->rand_id)->update(['status'=>'2','remark'=>'入件成功','merchant_id'=>$ok['merchant_id']]);
                        }
                    }
                }



            }




        }
        catch(\Exception $e)
        {
// echo $e->getMessage().$e->getLine().$e->getFile();
        }


// die;
        return  json_encode(MinSheng::setforeach($request->get('store_id')));


    }

    public function scanPay(Request $request){
        Log::info($request->all());

    }
}