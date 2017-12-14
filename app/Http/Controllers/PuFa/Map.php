<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/2
 * Time: 11:10
 * 浦发支付宝支付工具类
 */
namespace App\Http\Controllers\PuFa;

class Map
{


	/*
		订单生成状态

	*/
	const  TRADE_READY=1;//订单已经成功生成


	/*
		订单交易状态值
	*/
	const  TRADE_SUCCESS=2;//订单支付成功
	const  TRADE_FAIL=3;//订单支付失败
	/*
		订单退款状态值
	*/
	const TRADE_REFUND=4;//退款

	
	/*
		交易类型
	*/
	const  TRADE_ALIPAY=1;//使用阿里支付宝支付
	const  TRADE_WEIXIN=2;//使用微信支付


}