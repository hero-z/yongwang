<?php

/*
	民生的进件资料业务处理类

*/
namespace App\Http\Controllers\MinSheng;

use App\Http\Controllers\Controller;

use App\Http\Controllers\MinSheng\MinSheng;
use App\Http\Controllers\Tools\Verify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessController extends Controller
{ 
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
		入参：
		type   1表示阿里分类
		id：  父id
		level  哪一层
	*/
	public function cate(Request $request)
	{
		// $this->saveInfo();die;
		$id=trim($request->get('id'));
		$objdata = DB::table("ms_cate")->select(['id','pid','name','ali_cate','wx_cate','remark'])->where("pid", $id)->get();
		return $json= json_encode($objdata);
	}

	public function bank(Request $request)
	{
		$bank_name=trim($request->get('bank_name'));
		$objdata = DB::table("ms_bank")->select(['bank_name','bank_type','id'])->where("bank_name",'like', '%'.$bank_name.'%')->limit(500)->get();
		// echo '<pre>';print_r($objdata);die;
		return $json= json_encode($objdata);
	}

	/*
		第1步：生成商户进件资料提交表单并处理表单提交数据

		进件时间必须在  0:30~23:30



		truncate table ms_stores;
		truncate table ms_pay_way;
	*/
	public function info(Request $request)
	{  

		// 生成表单
        if($request->isMethod('get')){

    // 如果是空码---则去注册并绑定到merchant_shops
    $merchant_id=auth()->guard('merchant')->user()->id;//merchant表的id
    $merchant_info=DB::table('merchant_shops')->where('merchant_id','=',$merchant_id)->where('store_type','=','ms')->first();
    if(!empty($merchant_info))
    {
        echo '<h1>您已经入驻民生店铺了，无需重复入驻</h1>';
        die;
    }

        	$code_number=trim($request->get('code_number'));
        	if(empty($code_number))
        		return view('system.notice',['message'=>'未检测到二维码！']);

			$info = DB::table("mscqr_lsitsinfos")->where("code_number", $code_number)->first();
			if($info->code_type==0)
			{
				$user_id=$info->user_id;
	        	// return view('minsheng.addstore',['user_id'=>$user_id,'code_number'=>$code_number]);
	        	return view('minsheng.addstore',compact('user_id','code_number'));
			}
			else
			{
        		return view('system.notice',['message'=>'当前二维码已被占用！']);
			}
        }

        // 处理表单数据
        /*
        	商户名称---商户全称必须唯一
			返回：
			return ['status'=>1,'message'=>'入驻民生成功！'];
			return ['status'=>2,'message'=>'入驻民生失败！'];
        */
        if($request->isMethod('post')){
        		try
	        	{
		        	set_time_limit(0);
		        	$code_number=trim($request->get('code_number'));
		        	if(empty($code_number))
		        	{
						return json_encode(['status'=>'2','message'=>'请输入二维码！']);						
		        	}

		        	$user_id=trim($request->get('user_id'));
		        	if(empty($user_id))
		        	{
						return json_encode(['status'=>'2','message'=>'请输入推广员ID！']);						
		        	}

		        	$data=[
		        		'merchantName'=>trim($request->get('merchantName')),//商户名称：测试商户
		        		'shortName'=>trim($request->get('shortName')),//商户简称：测试商户
		        		'merchantAddress'=>trim($request->get('merchantAddress')),//商户地址
		        		'district'=>trim($request->get('district')),//区编码
		        		'usertype'=>'0'.trim($request->get('usertype')),//区编码

		        		'servicePhone'=>trim($request->get('servicePhone')),//18180352791
		        		'category'=>trim($request->get('category')),//2015050700000011
		        		'idCard'=>trim($request->get('idCard')),//321084198908173614
		        		'accNo'=>trim($request->get('accNo')),//6212261102024404421
		        		'accName'=>trim($request->get('accName')),//戴明康
		        		'tradetype'=>trim($request->get('tradetype')),//清算方式  涉及到费率 和  SMZF_DZWL_T0  SMZF_DZWL_T1
		        		'ext'=>trim($request->get('ext')),//备注
						'callBack'=>url('api/minsheng/infonotify'),
						// 'payWay'=>trim($request->get('payWay')),//支付宝或者微信   ZFBZF   WXZF
						'date'=>date('YmdHis'),//YmdHis
		        	];
// dd($data['district']);
		        	//表单验证
		        	$verify=$this->verifyform($data);
		        	if($verify['status']=='2')
		        		return json_encode($verify);
		        	

		        	// 找出地区编码
		        	$district=DB::table('ms_region')->where('code',$data['district'])->where('level','4')->first();
		        	if(empty($district))
		        	{
						return json_encode(['status'=>'2','message'=>'请选择省市区编码！']);
		        	}

		        	$district_code=$data['district'];
		        	$city_code=$district->pid;
		        	$province=DB::table('ms_region')->where('code',$district->pid)->first();
		        	$province_code=$province->pid;

		        	$region=[
		        		'province'=>$province_code,
		        		'city'=>$city_code,
		        		'district'=>$district_code,
		        	];



		        	//限制条件---店铺全称和身份证
			        $havestore=DB::table('ms_pay_way')->where('store_name','=',$data['merchantName'])->count();
			        if($havestore>0)
						return json_encode(['status'=>'2','message'=>'该店铺已经入驻了！']);
		        	//限制条件---店铺全称和身份证
			        $havestore=DB::table('ms_stores')->where('store_short_name','=',$data['shortName'])->count();
			        if($havestore>0)
						return json_encode(['status'=>'2','message'=>'该商户简称已经存在！']);

		        	switch($data['tradetype'])
		        	{
		        		case '1':
		        			$data['tradetype']='SMZF_DZWL_T0';break;
		        		case '2':
		        			$data['tradetype']='SMZF_DZWL_T1';

		        			// $data['bank_name']=trim($request->get('bankType'));
		        			// $data['bank_type']=trim($request->get('bankName'));
		        			$bankdata=DB::table("ms_bank")->where("id", trim($request->get('bankName')))->first();
		        			if(empty($bankdata))
		        			{
		        				return json_encode(['status'=>'2','message'=>'请选择开户行对应的联行号！']);
		        			}
		        			$data['bank_name']=$bankdata->bank_name;
		        			$data['bank_type']=$bankdata->bank_type;
		        			break;
		        		default:
							return json_encode(['status'=>'2','message'=>'入驻民生失败！']);break;

		        	}
					$cateobj = DB::table("ms_cate")->select(['ali_cate','wx_cate'])->where("id", $data['category'])->first();
					if(empty($cateobj))
					{
						return json_encode(['status'=>'2','message'=>'请选择行业分类！']);						
					}

		        	// 接口工具参数准备
					$ms=MinSheng::start();
			        $config=DB::table('ms_configs')->where('id','=','1')->first();
			        MinSheng::$rsa->self_public_key=MinSheng::$rsa->matePubKey($config->self_public_key);
			        MinSheng::$rsa->self_private_key=MinSheng::$rsa->matePriKey($config->self_private_key);
			        MinSheng::$rsa->third_public_key=MinSheng::$rsa->matePubKey($config->third_public_key);
	
					if($data['tradetype']=='SMZF_DZWL_T0')
					{
						$data['cooperator']=$config->cooperator_t0;
					}
					elseif($data['tradetype']=='SMZF_DZWL_T1')
					{
						$data['cooperator']=$config->cooperator_t1;
					}
					else
					{
						return json_encode(['status'=>'2','message'=>'清算方式必须选择！']);
						echo '清算方式必须选择！';die;
					}
	
	
			        //其他民生业务参数---服务商事先配置的参数
					$ms->drawFee=(float)$config->draw_fee;
					$ms->tradeRate=(float)$config->trade_rate;
					$ms->request_url=$config->request_url;//进件地址
	
					$store_id='m'.MinSheng::randnum();//服务商生成的商户编号

 
		/////////////支付宝通道进件start///////////////////////////////////////////
					$data['payWay']='ZFBZF';
					$data['category']=$cateobj->ali_cate;
					$pay_way_merchant_id_ali='m'.MinSheng::randnum();
					$return_ali=$ms->makeInfo($data,$pay_way_merchant_id_ali,$region);
    					$ali_cin=[
    						'store_id'=>$store_id,
    						'rand_id'=>$pay_way_merchant_id_ali,
    						'store_name'=>$data['merchantName'],
    						'store_short_name'=>$data['shortName'],
    						'store_address'=>$data['merchantAddress'],
    						'store_user'=>$data['accName'],
    						'usertype'=>$data['usertype'],
    						'store_phone'=>$data['servicePhone'],
    						'id_card'=>$data['idCard'],
    						'bank_no'=>$data['accNo'],
							'bank_type'=>isset($data['bank_type'])?$data['bank_type']:'',
							'bank_name'=>isset($data['bank_name'])?$data['bank_name']:'',
    						'pay_way'=>$data['payWay'],
    						'category'=>$data['category'],
    						'remark'=>'支付宝进件等待审核',
    						'status'=>'1',//1表示正在进件   2表示进件成功  3进件失败
								'created_at'=>date('Y-m-d H:i:s'),
								'draw_fee'=>$ms->drawFee,
								'trade_rate'=>$ms->tradeRate,
    					];
    					if($return_ali['status']=='3')
    					{
    						$ali_cin['merchant_id']=$return_ali['merchantCode'];
    						$ali_cin['status']='2';
    						$ali_cin['remark']='阿里进件成功';
    					}
    					// 失败，记录原因
    					elseif($return_ali['status']=='1')
    					{
    						$ali_cin['status']='3';
    						$ali_cin['remark']=$return_ali['message'];
    					} 
	        		/////////////支付宝通道进件end///////////////////////////////////////////
	        	
	        		sleep(0.2);
	        	
	        		/////////////微信通道进件start///////////////////////////////////////////
					$data['payWay']='WXZF';
					$data['category']=$cateobj->wx_cate;;
					$pay_way_merchant_id_wx='m'.MinSheng::randnum();
					$return_wx=$ms->makeInfo($data,$pay_way_merchant_id_wx,$region);
    					$wx_cin=[
    						'store_id'=>$store_id,
    						'rand_id'=>$pay_way_merchant_id_wx,
    						'store_name'=>$data['merchantName'],
    						'store_short_name'=>$data['shortName'],
    						'store_address'=>$data['merchantAddress'],
    						'store_user'=>$data['accName'],
    						'usertype'=>$data['usertype'],
    						'store_phone'=>$data['servicePhone'],
    						'id_card'=>$data['idCard'],
    						'bank_no'=>$data['accNo'],
							'bank_type'=>isset($data['bank_type'])?$data['bank_type']:'',
							'bank_name'=>isset($data['bank_name'])?$data['bank_name']:'',
    						'pay_way'=>$data['payWay'],
    						'category'=>$data['category'],
    						'remark'=>'微信进件等待审核',
    						'status'=>'1',
								'created_at'=>date('Y-m-d H:i:s'),
								'draw_fee'=>$ms->drawFee,
								'trade_rate'=>$ms->tradeRate,
    					];
    					if($return_wx['status']=='3')
    					{
    						$wx_cin['remark']='微信进件成功';
    						$wx_cin['merchant_id']=$return_wx['merchantCode'];
    						$wx_cin['status']='2';

                            MinSheng::setforeach($store_id);

                        }
    					// 失败，记录原因
    					elseif($return_wx['status']=='1')
    					{
    						$wx_cin['status']='3';
    						$wx_cin['remark']=$return_wx['message'];
    					} 

	        		/////////////微信通道进件end///////////////////////////////////////////





///////////////////////////商户信息入库START//////////////////////////////////////////////////////////////
					// 将商铺信息入库，其次入库支付方式
    					if(($return_ali['status']=='2'||$return_ali['status']=='3')||($return_wx['status']=='2'||$return_wx['status']=='3'))
    					{

							$store_cin=[
								'store_id'=>$store_id,
								'user_id'=>$user_id,
								'status'=>'1',
								'pid'=>'0',
								'cooperator'=>$data['cooperator'],
								'store_user'=>$data['accName'],
								'store_phone'=>$data['servicePhone'],

								// 新增支付宝省市区
								'district_code'=>$data['district'],
								// 'store_name'=>$data['merchantName'],
								'store_short_name'=>$data['shortName'],
								// 'store_address'=>$data['merchantAddress'],
								// 'id_card'=>$data['idCard'],
								// 'bank_no'=>$data['accNo'],
								'remark'=>'商户扫码进件',
								// 'bank_type'=>isset($data['bank_type'])?$data['bank_type']:'',
								// 'bank_name'=>isset($data['bank_name'])?$data['bank_name']:'',
								'draw_fee'=>$ms->drawFee,
								'trade_rate'=>$ms->tradeRate,
								'created_at'=>date('Y-m-d H:i:s'),
							];

							$insert_id=DB::table('ms_stores')->insertGetId($store_cin);//插入成功返回true




							if($insert_id)
							{
								// 支付方式入库
    							DB::table('ms_pay_way')->insert($ali_cin);
    							DB::table('ms_pay_way')->insert($wx_cin);

								DB::table('mscqr_lsitsinfos')->where('code_number','=',$code_number)->update(['code_type' => '1','store_id'=>$store_id,'updated_at'=>date('Y-m-d H:i:s')]);

// 自动绑定收银员
$merchant_id=auth()->guard('merchant')->user()->id;//merchant表的id
$merchantdata=[
	'merchant_id'=>$merchant_id,//店主/店员id
	'store_type'=>'ms',
	'store_id'=>$store_id,
	'desc_pay'=>'厦门民生银行',
	'status'=>'1'
	];
$insert=DB::table("merchant_shops")->insert($merchantdata);
// file_put_contents('./mmmmmmmmmmmmmmmmmmmmmm.txt', date('Y-m-d H:i:s')."\n\n".var_export($merchantdata,true)."\n\n".var_export($insert,true)."\n\n\n\n",FILE_APPEND);

								return json_encode(['status'=>'1','message'=>'支付宝：'.$return_ali['message'].'；微信：'.$return_wx['message']]);
							}
							else
							{
								return json_encode(['status'=>'2','message'=>'服务商数据库错误！']);
							}

    					}
    					else
    					{
							return json_encode(['status'=>'2','message'=>'支付宝：'.$return_ali['message'].'；微信：'.$return_wx['message']]);
    					}

////////////////////////////商户信息入库END/////////////////////////////////////////////////////////////




	        	/*
	        		sleep(0.2);
	        	
	        		/////////////qq通道进件start///////////////////////////////////////////
	        					$data['payWay']='QQZF';
	        					$data['category']='团购';
	        	
	        					$pay_way_merchant_id_qq=MinSheng::randnum();
	        					$return_qq=$ms->makeInfo($data,$pay_way_merchant_id_qq);
		        					$cin=[
		        						'store_id'=>$store_id,
		        						'pay_way'=>$data['payWay'],
		        						'rand_id'=>$pay_way_merchant_id_qq,
		        						'category'=>$data['category'],
		        						'remark'=>'qq支付方式',
		        						'status'=>$return_qq['status']=='3'?'2':'1',
		        					];
		        					if($return_qq['status']=='3')
		        					{
		        						$cin['merchant_id']=$return_qq['merchantCode'];
		        						$cin['status']='2';
		        					}
		        					DB::table('ms_pay_way')->insert($cin);
	        	
	        		/////////////qq通道进件end///////////////////////////////////////////
		        					*/
	        	}
	        	catch(\Exception $e)
	        	{
					return json_encode(['status'=>'2','message'=>'系统错误：'.$e->getMessage().$e->getLine()]);
	        		echo $e->getMessage().$e->getLine();
	        	}


        }

	}

	/*
		验证数据
	*/
	protected function verifyform($data)
	{
		if(Verify::isEmpty($data['tradetype']))
		{
			return ['status'=>'2','message'=>'请选择清算方式！'];
		}

		if(!(Verify::length($data['merchantName'],3,null,1)))
		{
			return ['status'=>'2','message'=>'商户名称至少3个字符！'];
		}

		if(!(Verify::length($data['shortName'],3,null,1)))
		{
			return ['status'=>'2','message'=>'商户简称至少3个字符！'];
		}


		if(Verify::isEmpty($data['merchantAddress']))
		{
			return ['status'=>'2','message'=>'店铺地址不能为空！'];
		}

		if(Verify::isEmpty($data['usertype']))
		{
			return ['status'=>'2','message'=>'联系人类型不能为空！'];
		}

		if(!Verify::isMobile($data['servicePhone']))
		{
			return ['status'=>'2','message'=>'请输入正确的手机号码！'];
		}

		if(Verify::isEmpty($data['category']))
		{
			return ['status'=>'2','message'=>'请选择行业分类！'];
		}

		if(!Verify::length($data['idCard'],15,18,3))
		{
			return ['status'=>'2','message'=>'请输入正确的身份证号码！'];
		}

		if(!Verify::allnum($data['accNo']))
		{
			return ['status'=>'2','message'=>'收款人银行卡号不能为空！'];
		}

		if(Verify::isEmpty($data['accName']))
		{
			return ['status'=>'2','message'=>'收款人不能为空！'];
		}

		return ['status'=>'1','message'=>'验证通过！'];
	}


	/*
		第2步：商户进件资料异步通知
	*/
	public function infonotify(Request $request)
	{
		try
		{

	    	// 接口工具参数准备
			$ms=MinSheng::start();
	        $config=DB::table('ms_configs')->where('id','=','1')->first();
	        MinSheng::$rsa->self_public_key=MinSheng::$rsa->matePubKey($config->self_public_key);
	        MinSheng::$rsa->self_private_key=MinSheng::$rsa->matePriKey($config->self_private_key);
	        MinSheng::$rsa->third_public_key=MinSheng::$rsa->matePubKey($config->third_public_key);

		    $cout=$ms->unlockData($request->all());
			file_put_contents(storage_path().'/logs/ms_info_infonotify.txt', "\n\n".date('Y-m-d H:i:s')."\n".var_export($cout,true),FILE_APPEND);


			// 失败就不管了
			if($cout['status']!='2')
			{
				return ;
			}

            $merchant_id=$cout['data']['message']['body']['merchantCode'];//民生的商户号
            $rand_id=$cout['data']['message']['body']['merchantId'];//我方支付方式流水号

	    	if(isset($cout['data']['message']['head']['respType'])&&$cout['data']['message']['head']['respType']=='S')
	    	{
				$payroad=DB::table('ms_pay_way')->where('rand_id',$rand_id)->first();
				// 没有这个申请这个商户，直接返回
				if(empty($payroad))
				{
					return ;
				}

				$store_id=$payroad->store_id;
				$payway=$payroad->pay_way;

/*				$payroad->merchant_id=$merchant_id;
				$payroad->status='2';
				$payroad->remark='通道开通成功';
				$ok=$payroad->update();
*/
				$ok=DB::table('ms_pay_way')->where('rand_id',$rand_id)->update([

					'merchant_id'=>$merchant_id,
					'status'=>'2',
					'remark'=>'通道开通成功',
					]);



	    		echo  '000000';



                //设置微信公众号
                if($payway=='WXZF')
                {
	                MinSheng::setforeach($store_id);
                }

                return ;
	    	}
	    	else
	    	{
				$r=DB::table('ms_pay_way')->where('rand_id','=',$rand_id)->update(['merchant_id' => $merchant_id,'status'=>'3','remark'=>$cout['data']['message']['head']['respMsg']]);
				
			    	echo  '000000';
			    	return ;					
	    	}
		}
		catch(\Exception $e)
		{
			file_put_contents(storage_path().'/logs/ms_info_infonotify.txt', "\n\n".date('Y-m-d H:i:s')."\n".$e->getMessage().$e->getLine(),FILE_APPEND);

		}
	}

	/*
		第3步：商户进件资料成功页
	*/
	public function mssuccess()
	{
		return view('minsheng.storesuccess');
	}




	/*
		第4步：商户进件资料修改---必须是已经成功入驻的商户
	*/
	public function saveInfo()
	{
die;
		$ms=$this->initms();
		$ms->request_url=$this->msconfig->request_url;

		$ms->drawFee='0.5';
		$ms->tradeRate='0.005';
		$cin=[
			'date'=>date('YmdHis'),
			'store_short_name'=>'我的修改商户',//流水号--服务商
			'store_address'=>'我的商户地址',//流水号--服务商
			'store_phone'=>'17002582596',//流水号--服务商
			'bank_no'=>'6212261102024404421',//流水号--服务商
			'store_user'=>'戴明康测试',//流水号--服务商
			'bank_type'=>'103301010794',//流水号--服务商
			'bank_name'=>'中国农业银行股份有限公司南京玉兰路支行',//流水号--服务商
			'cooperator'=>'SMZF_YMXKJ_T0',//流水号--服务商
			'callBack'=>url('api/minsheng/infonotify'),//流水号--服务商
			'pay_way_id'=>'20170509152339873040',//流水号--服务商
		];
/*
成功：
Array
(
    [status] => 3
    [message] => 资料修改成功！
)

不返回数据：
status  1



*/
		$return =$ms->saveInfo($cin);
		echo '<pre>';
		print_r($return);

	}



/*

		省市区编码获取

*/
    public function region(Request $request)
    {
        if($request->isMethod('post'))
        {
        	$table=DB::table('ms_region');
            $level=trim($request->get('level'));
            $pid=trim($request->get('pid'));
 
        	$table->where('pid',$pid);

            $data=$table->where('level',$level)->get();


            $data=json_decode(json_encode($data),true);
            if(!empty($data))
            {
                return json_encode(['status'=>'1','data'=>$data]);
            }
            else
            {
                return json_encode(['status'=>'2']);
            }
        }
    }



}