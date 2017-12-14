<?php
namespace App\Http\Controllers\MinSheng;

use App\Http\Controllers\MinSheng\MinSheng;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PingAn\NotifyController;
use App\Http\Controllers\Push\JpushController;
use App\Models\PageSets;
use App\Models\WeixinPayConfig;
use App\Models\WeixinPayNotify;
use App\Models\WXNotify;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\DB;

//阿里权限配置
use App\Models\AlipayIsvConfig;
// 阿里配置文件
use Illuminate\Support\Facades\Config;
// 请求参数类
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WxpayController extends Controller
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
		第1步：生成微信授权链接(二维码)
	*/
	public function wxQr(Request $request)
	{  
		$store_id=$request->get('store_id');

		$store=DB::table('ms_stores')->where('store_id',$store_id)->first();
		if(empty($store))
		{
			echo '店铺不存在！';die;
		}

		$main_store_id=$store->pid;
		if($main_store_id!='0')
		{
			$wx_pay_way=DB::table('ms_pay_way')->where('store_id',$main_store_id)->where('pay_way','WXZF')->first();
		}
		else
		{
			$wx_pay_way=DB::table('ms_pay_way')->where('store_id',$store_id)->where('pay_way','WXZF')->first();
		}


		if(empty($wx_pay_way))
		{
			return view('system.notice',['message'=>'该店铺没有开通支付宝付款功能！']);
		}

		if(($wx_pay_way->status!='2'))
		{
			return view('system.notice',['message'=>'该店铺正在开通支付宝付款功能！']);
		} 

		$cashier_id=trim($request->get('cashier_id'))?trim($request->get('cashier_id')):'0';//默认店铺收款
		$cashier=false;
		if($cashier_id)
		{
			$cashier=DB::table('merchants')->where('id',$cashier_id)->first();
		}

		$store_name=$store->store_short_name;
		$cashier_name=$cashier?$cashier->name:$store->store_short_name;
        $code_url = url("admin/weixin/oauth?sub_info=MS_{$store_id}_{$cashier_id}");
		return view('minsheng.makeQr',['code_url'=>$code_url,'store_name'=>$store_name,'cashier_name'=>$cashier_name]);
	}

	/*
		第2步：用户通过支付宝app扫描二维码进入表单页面
        return redirect(url("api/minsheng/aliform?u_id={$u_id}&cashier_id={$m_id}"));//跳转到输入金额页面

	*/
	public function wxform(Request $request)
	{

        $store_id = $request->get('store_id');//服务商商户号
        $cashier_id = $request->get('cashier_id');//收银员id
/*
        var_dump($store_id);
        var_dump($request->all());die;*/

        $store=DB::table('ms_stores')->where('store_id',$store_id)->first();
        if(empty($store))
        {
        	echo '店铺不存在！';die;
			return view('system.notice',['message'=>'店铺不存在！']);
        }

        $shopinfo=[
            'store_name'=>$store->store_short_name,
            'store_id'=>$store_id,
        ];
        return view('minsheng.wx.wxForm', compact('shopinfo','cashier_id'));         
	}

	/*
		第3步：接收表单，并调用民生的扫码接口获得民生的url并跳转（此时会自动唤起支付宝支付）
	*/
	public function wxhandle(Request $request)
	{
        try
        {
        	$store_id = $request->get('store_id');
	    	$store=DB::table('ms_stores')->where('store_id','=',$store_id)->first();
	    	if(empty($store))
	    		return json_encode(['status'=>'2','message'=>'商户不存在！']);
	    	if($store->status!='2')
	    		return json_encode(['status'=>'2','message'=>'商铺正在开通微信收款！']);

	    	//判断当前商户是否支持支付宝付款
	    	$paytype='WXZF';
	    	// 查出主店铺信息
	    	if($store->pid!='0')
	    	{
	    		$main_store_id=$store->pid;
	    		$payway=DB::table('ms_pay_way')->where('pay_way',$paytype)->where('store_id',$main_store_id)->first();
	    	}
	    	else
	    	{
	    		$payway=DB::table('ms_pay_way')->where('pay_way',$paytype)->where('store_id',$store_id)->first();
	    	}	    	

	    	if(empty($payway))
	    		return json_encode(['status'=>'2','message'=>'商家没有微信通道！']);

	    	if($payway->status!='2')
	    		return json_encode(['status'=>'2','message'=>'商家正在开通微信付款通道！']);

	        $cashier_id = trim($request->get('cashier_id','0'));
	        $total_amount = trim($request->get('total_fee'))?trim($request->get('total_fee')):'0';

	        if(!$total_amount)
	        {
	            $return = [
	                'status' => '2',
	                "message" => "请输入交易金额",
	            ];
	            return json_encode($return);
	        }

	        // 用户授权后，服务商拿到的用户资料---user_id有用
            $wx_user_data = $request->session()->get('wx_user_data');
            $user_id=$wx_user_data[0]['id'];
	        if(empty($user_id))
	        {
	            $return = [
	                'status' => '2',
	                "message" => "用户微信授权失败！",
	            ];
	            return json_encode($return);
	        }

			// 民生接口工具参数准备
		    $ms=$this->initms();
			
			$ms->request_url=$this->msconfig->request_url;


			$config=DB::table('ms_configs')->where('id','1')->first();

			if(empty($config->wx_app_id))
			{
	    		return json_encode(['status'=>'2','message'=>'商家微信配置不正确！']);
			}
 

			// 民生接口下单
			$data=[
				'date'=>date('YmdHis'),
				'merchant_id'=>$payway->merchant_id,//支付宝商编--民生颁发的    2017050405307047   qq
				'totalAmount'=>$total_amount,//单位元
				'subject'=>$store->store_short_name.'收款',//'显示在支付宝订单上的订单标题',
				'desc'=>$store->store_short_name.'收款',//'订单描述',
				'operatorId'=>$cashier_id,//'操作员编号',
				'storeId'=>$store_id,//服务商生成的商户号
				// 'terminalId'=>'商户机具终端编号',//服务商生成的商户号
				'cooperator'=>$store->cooperator,//结算方式
				'callBack'=>url('api/minsheng/wxpaynotify'),//回调地址
				'reqMsgId'=>'m'.date('YmdHis').mt_rand(1000000,9999999),

				'userId'=>$user_id,//支付宝用户标识；微信合作方标识；qq不需要

				'subAppid'=>$config->wx_app_id,//微信公众号
				// 'goodsTag'=>'weixin_test_goods',//微信物品标识
			];

		    $order=$ms->webPay('wx',$data);

file_put_contents(storage_path().'/logs/11133.txt',var_export($order,true),FILE_APPEND);

		    if($order['status']=='2')
		    {
		    	// 将订单信息入库
		    	$cin=[
		    		'out_trade_no'=>$data['reqMsgId'],
		    		'store_id'=>$store_id,
		    		'merchant_id'=>$cashier_id,
		    		'type'=>'502',
		    		'total_amount'=>$data['totalAmount'],
		    		'pay_status'=>'3',
		    		'created_at'=>date('Y-m-d H:i:s',strtotime($data['date'])),
		    	];

				$insert=DB::table("orders")->insert($cin);
				if($insert)
				{
                    file_put_contents(storage_path().'/logs/11133.txt',var_export($order['data'],true),FILE_APPEND);

                    return json_encode(['status'=>'1','data'=>$order['data'],'message'=>$order['message']]);
				}
				else
				{
	            	return json_encode(['status'=>'2','message'=>'服务商数据库错误！']);
				}

            	return json_encode(['status'=>'1','message'=>'订单创建成功，等待支付！']);
		    }
		    else
		    {
            	return json_encode(['status'=>'2','message'=>$order['message']]);
		    }

        }
		catch(\Exception $e)
		{
		file_put_contents(storage_path().'/logs/wwwwwwwwwwwwwwww.txt', $e->getMessage().$e->getLine(),FILE_APPEND);
            	return json_encode(['status'=>'2','message'=>'系统错误！']);

		}

	}

	/*
		第4步：支付成功返回成功页面---同步

	*/
	public function wxsuccess()
	{

	}

	/*
		第5步：接收民生的异步结果
	*/
	public function wxpaynotify(Request $request)
	{
		try
		{
	    	// 接口工具参数准备
		    $ms=$this->initms();

		    $cout=$ms->unlockData($request->all());
			file_put_contents(storage_path().'/logs/ms_weixinpay.txt', "\n\n".date('Y-m-d H:i:s')."\n".var_export($cout,true),FILE_APPEND);

			if($cout['status']=='2')
			{
				$Order=DB::table("orders")->where("out_trade_no", $cout['data']['message']['body']['reqMsgId'])->first();
				if($Order&&$Order->status!=$cout['data']['message']['head']['respType']){
					$savedata=[
						'buyer_id'=>$cout['data']['message']['body']['buyerId'],
						'pay_status'=>$cout['data']['message']['head']['respType']=='S'?'1':'3',//1成功2失败3等待支付
						'status'=>$cout['data']['message']['head']['respType'],
						'trade_no'=>$cout['data']['message']['head']['smzfMsgId'],
						'updated_at'=>date('YmdHis')
					];
					DB::table("orders")->where("out_trade_no", $cout['data']['message']['body']['reqMsgId'])->update($savedata);

					if(isset($cout['data']['message']['head']['respType'])&&$cout['data']['message']['head']['respType']=='S')
					{
						//店铺通知微信
						$store_id = $Order->store_id;
						//安卓app语音播报
						$jpush=new JpushController();
						$jpush->push(''.$cout['data']['message']['body']['reqMsgId'].'-支付宝',$cout['data']['message']['body']['totalAmount'],$cout['data']['message']['body']['reqMsgId']);
						$printnotify=new NotifyController();
						$printnotify->printY($store_id, $Order, '民生微信');
						//U打印
						$printnotify->printU($store_id, $Order, "民生微信");
						//微信提醒
						$WeixinPayNotifyStore = WeixinPayNotify::where('store_id', $store_id)->first();
						//实例化
						$config = WeixinPayConfig::where('id', 1)->first();
						if ($WeixinPayNotifyStore && $config) {
							$options = [
								'app_id' => $config->app_id,
								'secret' => $config->secret,
								'token' => '18851186776',
								'payment' => [
									'merchant_id' => $config->merchant_id,
									'key' => $config->key,
									'cert_path' => $config->cert_path, // XXX: 绝对路径！！！！
									'key_path' => $config->key_path,      // XXX: 绝对路径！！！！
									'notify_url' => $config->notify_url,       // 你也可以在下单时单独设置来想覆盖它
								],
							];
							$app = new Application($options);
							$userService = $app->user;
							$template = PageSets::where('id', 1)->first();
							$notice = $app->notice;
							$userIds = $WeixinPayNotifyStore->receiver;
							$open_ids = explode(",", $userIds);
							$templateId = $template->string1;
							$url = $WeixinPayNotifyStore->linkTo;
							$color = $WeixinPayNotifyStore->topColor;
							$markstr='';
							if($Order&&!empty($Order->remark)){
								$markstr.='(备注:'.$Order->remark.')';
							}
							$data = array(
								"keyword1" => $Order->total_amount,
								"keyword2" => '微信(' . $cout['data']['message']['body']['buyerId'] . ')'.$markstr,
								"keyword3" => '' . $Order->updated_at . '',
								"keyword4" => $cout['data']['message']['body']['channelNo'],
								"remark" => '祝' . $WeixinPayNotifyStore->store_name . '生意红火',
							);

							foreach ($open_ids as $v) {
								$s = WXNotify::where('open_id', $v)->where('store_id', $store_id)->first();
								if ($s) {
									if ($s->status) {
										try {
											$notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($v)->send();
										} catch (\Exception $exception) {
											Log::info($exception);
											continue;
										}
									}
								} else {
									WXNotify::create([
										'store_id' => $store_id,
										'open_id' => $v,
									]);
									try {
										$notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($v)->send();
									} catch (\Exception $exception) {
										Log::info($exception);
										continue;
									}
								}
							}
						}
					}
				}
			}
		}
		catch(\Exception $e)
		{
			file_put_contents(storage_path().'/logs/ms_weixinpay.txt', "\n\n".date('Y-m-d H:i:s')."\n".$e->getMessage().$e->getLine(),FILE_APPEND);

		}



	}

}