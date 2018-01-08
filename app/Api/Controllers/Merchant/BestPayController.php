<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/12/28
 * Time: 19:35
 */

namespace App\Api\Controllers\Merchant;


use App\Common\PaiPai\Sign;
use App\Http\Controllers\BestPay\ManageController;
use App\Merchant;
use App\Models\BestPayStore;
use App\Models\Bill;
use App\Models\MerchantPayWay;
use App\Models\MerchantShops;
use App\Models\Paipai;
use EasyWeChat\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;

class BestPayController
{
    const MBAST_PAY_URL='https://webpaywg.bestpay.com.cn/barcode/placeOrder';
    const MBAST_PAY_QUERY_URL='https://webpaywg.bestpay.com.cn/query/queryOrder';
    const MBAST_PAY_REVERSE_URL='https://webpaywg.bestpay.com.cn/reverse/reverse';
    const MAC_STR='MERCHANTID=%s&ORDERNO=%s&ORDERREQNO=%s&ORDERDATE=%s&BARCODE=%s&ORDERAMT=%s&KEY=%s';
    const MAC_CORRECT_STR='MERCHANTID=%s&MERCHANTPWD=%s&OLDORDERNO=%s&OLDORDERREQNO=%s&REFUNDREQNO=%s&REFUNDREQDATE=%s&TRANSAMT=%s&KEY=%s';
    const PAY_STR='merchantId=%s&subMerchantId=%s&barcode=%s&orderNo=%s&orderReqNo=%s&orderDate=%s&channel=%s&busiType=%s&orderAmt=%s&productAmt=%s&attachAmt=%s&goodsName=%s&storeId=%s&backUrl=%s&ledgerDetail=%s&attach=%s&mac=%s';
    const QUERY_MAC_STR='MERCHANTID=%s&ORDERNO=%s&ORDERREQNO=%s&ORDERDATE=%s&KEY=%s';
    public function test()
    {
        return json_encode(['code' => "SUCCESS"]);
    }
    public function inUnified(Request $request)
    {
        Log::info($request->all());
        Log::info('222');
        return json_encode(['code' => "SUCCESS"]);
    }
    public function init(Request $request)
    {
        Log::info('11111');
        Log::info($request->all());
        $code='FAIL';
        $sub_code='';
        $sub_msg='';
        $msg='';
        $cin=$request->all();
        // 小盒验签
        if($cin['sign']==Sign::makeSign($cin))
        {
            $device_no=$cin['device_no'];
            $device=Paipai::where('device_no',$device_no)->first();
            if(!empty($device))
            {
                //商户扫码枪配置通道
                $mpayway=MerchantPayWay::where('merchant_id',$device->m_id)->first();
//                $mpayway=1;
                if($mpayway){
                    //配置通道
                    $auth_code=$cin['auth_code'];
                    $total_fee=$cin['total_fee'];//单位是分
                    //支付宝28微信13QQ91京东18正常都是18位，支付宝早期有密支付是17位，银联新版19位62打头
                    $codehead = substr($auth_code, 0, 2);
                    //翼支付
                    if($codehead=='51'){
                        if($mpayway->bestpay=='bestpay'){
//                        if($mpayway==1){
                            //商户通道信息
                            $mshops=MerchantShops::where('merchant_id',$device->m_id)->where('store_type','bestpay')->first();
                            if($mshops){
                                $bstore=BestPayStore::where('store_id',$mshops->store_id)->first();
                                if($bstore){
                                    $payparams=[];
                                    $payparams['merchantId']=$bstore->merchantId;
//                                $payparams['subMerchantId']='02150108040601368';
                                    $payparams['barcode']=$auth_code;
                                    $payparams['storeId']=$bstore->store_id;
                                    $payparams['orderAmt']=$total_fee;
                                    $payparams['orderNo']='mb'.date('YmdHis').rand(10000000,99999999);
                                    $payparams['orderReqNo']='mq'.date('YmdHis').rand(10000000,99999999);
                                    $payparams['orderDate']=date('YmdHis');
                                    $key=$bstore->data_key;
                                    $pay_key=$bstore->pay_key;
                                    //下单
                                    $res=self::best_pay($payparams,$key);
//                                Log::info($res);
                                    if($res['success']){
                                        //交易请求成功
                                        if($res['data']['success']){
                                            //交易下单成功
                                            $result=$res['data']['result'];
                                            if(self::checkBestPaySign($result,$key)){
                                                //验签成功
                                                try{
                                                    $istdata=[
                                                        'admin_id'=>$bstore->admin_id,
                                                        'merchant_id'=>$device->m_id,
                                                        'store_id'=>$bstore->store_id,
                                                        'device_no'=>$device_no,
                                                        'trade_no'=>$result['ourTransNo'],
                                                        'trade_req_no'=>$payparams['orderReqNo'],
                                                        'out_trade_no'=>$payparams['orderNo'],
                                                        'type'=>'101',
                                                        'total_amount'=>$total_fee/100,
                                                        'receipt_amount'=>$total_fee/100,
                                                        'pay_amount'=>$total_fee/100,
                                                        'invoice_amount'=>$total_fee/100,
                                                        'trade_status'=>$result['transStatus'],
                                                        'pay_status'=>2,
                                                    ];
                                                    $createbill=Bill::create($istdata);
                                                    $merchant=Merchant::find($device->m_id);
                                                    if($result['transStatus']=='B'){
                                                        //交易成功
                                                        $createbill->update(['pay_status'=>1]);
                                                        $printstr=
                                                            "--------------顾客联-----------\r\n"
                                                            ."商户名称:".$bstore->alias_name."\r\n"
                                                            ."商 户 号:".$result['merchantId']."\r\n"
                                                            ."款台编号:".$merchant->name."(".$merchant->id.")\r\n"
                                                            ."订 单 号:".$result['ourTransNo']."\r\n"
                                                            ."支付方式:"."翼支付"."\r\n"
                                                            ."交易金额:".($result['transAmt']/100)."元\r\n"
                                                            ."时    间:".$payparams['orderDate']."\r\n"
                                                            ."交易手机号:".$result['transPhone']."\r\n\r\n"
                                                            ."--------------商户联-----------\r\n"
                                                            ."商户名称:".$bstore->alias_name."\r\n"
                                                            ."商 户 号:".$result['merchantId']."\r\n"
                                                            ."款台编号:".$merchant->name."(".$merchant->id.")\r\n"
                                                            ."订 单 号:".$result['ourTransNo']."\r\n"
                                                            ."支付方式:"."翼支付"."\r\n"
                                                            ."交易金额:".($result['transAmt']/100)."元\r\n"
                                                            ."时    间:".$payparams['orderDate']."\r\n"
                                                            ."交易手机号:".$result['transPhone']."\r\n\r\n"
                                                            ."------------------------------\r\n"
                                                            ."备    注:"."\r\n"
                                                            ."------------------------------\r\n"
                                                            ."持卡人签名:"."\r\n\r\n\r\n"
                                                            ."本人确认以上交易,同意计入本主账号";
                                                        $encode = mb_detect_encoding($printstr, array('ASCII','GB2312','GBK','UTF-8'));
                                                        $str_encode = mb_convert_encoding($printstr, 'GBK', $encode);
//                                                    Log::info([
//                                                        'code' => "SUCCESS",
//                                                        'msg'=>'您已经成功付款',
//                                                        'pp_trade_no'=>$cin['pp_trade_no'],//扫码设备号
//                                                        'transaction_id'=>$result['ourTransNo'],//三方交易号
//                                                        'total_fee'=>$cin['total_fee'],//实际支付金额，非订单金额
//                                                        'time_end'=>date('YmdHis'),
//                                                        'pay_type'=>'bestpay',
//                                                        'printType'=>'2',
//                                                        'receipt'=>base64_encode($str_encode)]);
                                                        return json_encode([
                                                            'code' => "SUCCESS",
                                                            'msg'=>'您已经成功付款',
                                                            'pp_trade_no'=>$cin['pp_trade_no'],//扫码设备号
                                                            'transaction_id'=>$result['ourTransNo'],//三方交易号
                                                            'total_fee'=>$cin['total_fee'],//实际支付金额，非订单金额
                                                            'time_end'=>$payparams['orderDate'],
                                                            'pay_type'=>'bestpay',
                                                            'printType'=>'2',
                                                            'receipt'=>base64_encode($str_encode)]);
//                                        array (
//                                            'success' => true,
//                                            'result' =>
//                                                array (
//                                                    'merchantId' => '02150108040598665',
//                                                    'orderNo' => 'mb20171228233037100000000',
//                                                    'orderReqNo' => 'mq20171228233037100000000',
//                                                    'orderDate' => NULL,
//                                                    'transStatus' => 'B',
//                                                    'transAmt' => '1',
//                                                    'ourTransNo' => '2017122800001762161898',
//                                                    'encodeType' => '1',
//                                                    'sign' => '35625C940A7098464E7A1A11C6BFC764',
//                                                    'payerAccount' => NULL,
//                                                    'payeeAccount' => NULL,
//                                                    'payChannel' => NULL,
//                                                    'productDesc' => NULL,
//                                                    'refundFlag' => NULL,
//                                                    'customerId' => NULL,
//                                                    'coupon' => '0',
//                                                    'scValue' => '0',
//                                                    'mchntTmNum' => '',
//                                                    'deviceTmNum' => '',
//                                                    'attach' => '',
//                                                    'transPhone' => '182****8302',
//                                                    'respCode' => NULL,
//                                                    'respDesc' => NULL,
//                                                ),
//                                            'errorCode' => NULL,
//                                            'errorMsg' => NULL,
//                                        );
                                                    }elseif($result['transStatus']=='A'){
                                                        //请求中
                                                        Log::info('等待支付');
                                                        $createbill->update(['pay_status'=>2]);
                                                        $queryarray=array_except($payparams,['barcode','storeId','orderAmt']);
                                                        $limit=15;//15秒等待时间
                                                        for($i=0;$i<$limit;$i++){
                                                            $queryresult=self::best_pay_query($queryarray,$key);
                                                            Log::info($queryresult);
                                                            if($queryresult['success']){
                                                                if($queryresult['data']['success']){
                                                                    $queryresultdata=$queryresult['data']['result'];
                                                                    if(self::checkBestPaySign($queryresultdata,$key)){
                                                                        if($queryresultdata['transStatus']=='B'){
                                                                            $createbill->update(['pay_status'=>1]);
                                                                            //支付成功
//                                                            array (
//                                                                'success' => true,
//                                                                'result' =>
//                                                                    array (
//                                                                        'merchantId' => '02150108040598665',
//                                                                        'orderNo' => 'mb2017123014535162634079',
//                                                                        'orderReqNo' => 'mq2017123014535122551115',
//                                                                        'orderDate' => '20171230145351',
//                                                                        'transStatus' => 'A',
//                                                                        'transAmt' => '110000',
//                                                                        'ourTransNo' => '2017123000001768920748',
//                                                                        'encodeType' => '1',
//                                                                        'sign' => '882A2AD6172C0C11BD686A63DDEC95FC',
//                                                                        'payerAccount' => NULL,
//                                                                        'payeeAccount' => NULL,
//                                                                        'payChannel' => NULL,
//                                                                        'productDesc' => NULL,
//                                                                        'refundFlag' => NULL,
//                                                                        'customerId' => '182****8302',
//                                                                        'coupon' => NULL,
//                                                                        'scValue' => '0',
//                                                                        'mchntTmNum' => NULL,
//                                                                        'deviceTmNum' => NULL,
//                                                                        'attach' => NULL,
//                                                                        'transPhone' => NULL,
//                                                                    ),
//                                                                'errorCode' => NULL,
//                                                                'errorMsg' => NULL,
//                                                            );
                                                                            $printstr=
                                                                                "--------------顾客联-----------\r\n"
                                                                                ."商户名称:".$bstore->alias_name."\r\n"
                                                                                ."商 户 号:".$queryresultdata['merchantId']."\r\n"
                                                                                ."款台编号:".$merchant->name."(".$merchant->id.")\r\n"
                                                                                ."订 单 号:".$queryresultdata['ourTransNo']."\r\n"
                                                                                ."支付方式:"."翼支付"."\r\n"
                                                                                ."交易金额:".($queryresultdata['transAmt']/100)."元\r\n"
                                                                                ."时    间:".$queryresultdata['orderDate']."\r\n"
                                                                                ."交易手机号:".$queryresultdata['customerId']."\r\n\r\n"
                                                                                ."--------------商户联-----------\r\n"
                                                                                ."商户名称:".$bstore->alias_name."\r\n"
                                                                                ."商 户 号:".$queryresultdata['merchantId']."\r\n"
                                                                                ."款台编号:".$merchant->name."(".$merchant->id.")\r\n"
                                                                                ."订 单 号:".$queryresultdata['ourTransNo']."\r\n"
                                                                                ."支付方式:"."翼支付"."\r\n"
                                                                                ."交易金额:".($queryresultdata['transAmt']/100)."元\r\n"
                                                                                ."时    间:".$queryresultdata['orderDate']."\r\n"
                                                                                ."交易手机号:".$queryresultdata['customerId']."\r\n\r\n"
                                                                                ."------------------------------\r\n"
                                                                                ."备    注:"."\r\n"
                                                                                ."------------------------------\r\n"
                                                                                ."持卡人签名:"."\r\n\r\n\r\n"
                                                                                ."本人确认以上交易,同意计入本主账号";
                                                                            $encode = mb_detect_encoding($printstr, array('ASCII','GB2312','GBK','UTF-8'));
                                                                            $str_encode = mb_convert_encoding($printstr, 'GBK', $encode);
//                                                                        Log::info([
//                                                                            'code' => "SUCCESS",
//                                                                            'msg'=>'您已经成功付款',
//                                                                            'pp_trade_no'=>$cin['pp_trade_no'],//扫码设备号
//                                                                            'transaction_id'=>$result['ourTransNo'],//三方交易号
//                                                                            'total_fee'=>$cin['total_fee'],//实际支付金额，非订单金额
//                                                                            'time_end'=>date('YmdHis'),
//                                                                            'pay_type'=>'bestpay',
//                                                                            'printType'=>'2',
//                                                                            'receipt'=>base64_encode($str_encode)]);
                                                                            return json_encode([
                                                                                'code' => "SUCCESS",
                                                                                'msg'=>'您已经成功付款',
                                                                                'pp_trade_no'=>$cin['pp_trade_no'],//扫码设备号
                                                                                'transaction_id'=>$result['ourTransNo'],//三方交易号
                                                                                'total_fee'=>$cin['total_fee'],//实际支付金额，非订单金额
                                                                                'time_end'=>$payparams['orderDate'],
                                                                                'pay_type'=>'bestpay',
                                                                                'printType'=>'2',
                                                                                'receipt'=>base64_encode($str_encode)]);
                                                                        }elseif($queryresultdata['transStatus']=='A'){
                                                                            //等待付款
                                                                            if($i==$limit-1){
                                                                                //最后一次
                                                                                $createbill->update(['pay_status'=>5]);
                                                                                $correctparams['merchantId']=$payparams['merchantId'];
                                                                                $correctparams['oldOrderNo']=$payparams['orderNo'];
                                                                                $correctparams['oldOrderReqNo']=$payparams['orderReqNo'];
                                                                                $correctparams['refundReqNo']='mc'.date('YmdHis').rand(10000000,99999999);
                                                                                $correctparams['refundReqDate']=$payparams['orderDate'];
                                                                                $correctparams['transAmt']=$payparams['orderAmt'];
                                                                                $correctresult=self::best_pay_correct($correctparams,$pay_key,$key);
                                                                                Log::info('关闭交易');
                                                                                Log::info($correctresult);
                                                                                $sub_code='SYSTEMERROR';
                                                                                $sub_msg='用户支付超时,关闭交易!';
                                                                                $msg='用户支付超时,关闭交易!';
                                                                                break;
                                                                            }else{
                                                                                sleep(1);
                                                                            }
                                                                        }elseif($result['transStatus']=='C'){
                                                                            //失败
                                                                            $createbill->update(['pay_status'=>3]);
                                                                            $sub_code='SYSTEMERROR';
                                                                            $sub_msg='订单查询:支付结果失败!';
                                                                            $msg='订单查询:支付结果失败!';
                                                                            break;
                                                                        }elseif($result['transStatus']=='G'){
                                                                            //订单作废
                                                                            $createbill->update(['pay_status'=>4]);
                                                                            $sub_code='ORDERCLOSED';
                                                                            $sub_msg='订单查询:订单作废!';
                                                                            $msg='订单查询:订单作废!';
                                                                            break;
                                                                        }else{
                                                                            //未知交易标识
                                                                            //失败
                                                                            $sub_code='SYSTEMERROR';
                                                                            $sub_msg='订单查询:未知交易标识,支付结果失败!';
                                                                            $msg='订单查询:未知交易标识,支付结果失败!';
                                                                            break;
                                                                        }
                                                                    }else{
                                                                        $sub_code='SIGNERROR';
                                                                        $sub_msg='查询结果验签失败!';
                                                                        $msg='查询结果验签失败!';
                                                                        break;
                                                                    }
                                                                }else{
                                                                    $sub_code='SYSTEMERROR';
                                                                    $sub_msg='支付结果失败!';
                                                                    $msg='支付结果失败!';
                                                                    break;
                                                                }
                                                            }else{
                                                                $sub_code='SYSTEMERROR';
                                                                $sub_msg='支付结果查询失败!'.$queryresult['msg'];
                                                                $msg='支付结果查询失败!'.$queryresult['msg'];
                                                                break;
                                                            }
                                                        }
                                                    }elseif($result['transStatus']=='C'){
                                                        //失败
                                                        $sub_code='SYSTEMERROR';
                                                        $sub_msg='支付结果失败!';
                                                        $msg='支付结果失败!';
                                                    }elseif($result['transStatus']=='G'){
                                                        //订单作废
                                                        $sub_code='ORDERCLOSED';
                                                        $sub_msg='订单作废!';
                                                        $msg='订单作废!';
                                                    }else{
                                                        //未知交易标识
                                                        //失败
                                                        $sub_code='SYSTEMERROR';
                                                        $sub_msg='支付结果失败!';
                                                        $msg='支付结果失败!';
                                                    }
                                                }catch (\Exception $e){
                                                    file_put_contents(storage_path().'/logs/bestpay_createbill.txt', var_export($createbill,true) ,FILE_APPEND);
                                                    file_put_contents(storage_path().'/logs/bestpay_errorbill.txt', $e->getMessage().$e->getLine().'/r/n' ,FILE_APPEND);
                                                }
                                            }else{
                                                $sub_code='SIGNERROR';
                                                $sub_msg='支付结果验签失败!';
                                                $msg='支付结果验签失败!';
                                            }
                                        }else{
                                            $sub_code='SYSTEMERROR';
                                            $sub_msg='支付失败!'.$res['data']['errorMsg'];
                                            $msg='支付失败!'.$res['data']['errorMsg'];
                                        }
                                    }else{
                                        $sub_code='REQUESTERROR';
                                        $sub_msg='支付请求失败!'.$res['msg'];
                                        $msg='支付请求失败!'.$res['msg'];
                                    }
                                }else{
                                    $sub_code='INVALID_PARAMETER';
                                    $sub_msg='未查询到翼支付通道!';
                                    $msg='未查询到翼支付通道!';
                                }
                            }else{
                                $sub_code='INVALID_PARAMETER';
                                $sub_msg='商户不存在翼支付通道!';
                                $msg='商户不存在翼支付通道!';
                            }
                        }elseif(/*$mpayway==2*/$mpayway->bestpay=='pingan'){
                            //平安翼支付
                            $sub_code='INVALID_PARAMETER';
                            $sub_msg='暂未开通平安翼支付通道!';
                            $msg='暂未开通平安翼支付通道!';
                        }
                    }elseif($codehead=='28'){
                        //支付宝
                        $sub_code='INVALID_PARAMETER';
                        $sub_msg='支付宝支付通道未开通!';
                        $msg='支付宝支付通道未开通!';
                    }elseif($codehead=='13'){
                        //微信
                        $sub_code='INVALID_PARAMETER';
                        $sub_msg='微信支付通道未开通!';
                        $msg='微信支付通道未开通!';
                    }elseif($codehead=='18'){
                        //京东
                        $sub_code='INVALID_PARAMETER';
                        $sub_msg='京东支付通道未开通!';
                        $msg='京东支付通道未开通!';
                    }elseif($codehead=='62'){
                        //银联
                        $sub_code='INVALID_PARAMETER';
                        $sub_msg='银联支付通道未开通!';
                        $msg='银联支付通道未开通!';
                    }else{
                        $sub_code='INVALID_PARAMETER';
                        $sub_msg='未知授权码!';
                        $msg='未知授权码!';
                    }
                }else{
                    $sub_code='INVALID_PARAMETER';
                    $sub_msg='商户配置未通道!';
                    $msg='商户配置未通道!';
                }
            }else{
                $sub_code='INVALID_PARAMETER';
                $sub_msg='未知设备!';
                $msg='未知设备!';
            }
        }else{
            $sub_code='SIGNERROR';
            $sub_msg='通信验签失败!';
            $msg='通信验签失败!';
        }
        $return=[
            'code' => $code,
            'sub_code' => $sub_code,
            'sub_msg' => $sub_msg,
            'msg'=>$msg];
        file_put_contents(storage_path().'/logs/bestpay.txt', var_export($return,true) ,FILE_APPEND);
        Log::info($return);
        return json_encode($return);
    }

    protected function best_pay(array $params,$key=null)
    {
        $info='';
        if(!array_key_exists('merchantId',$params)){
            $info.='参数merchantId不存在!';
        }
        if(!array_key_exists('subMerchantId',$params)){
            $params['subMerchantId']='';
        }
        if(!array_key_exists('barcode',$params)){
            $info.='参数barcode不存在!';
        }
        if(!array_key_exists('orderNo',$params)){
            $params['orderNo']='mb'.date('YmdHis').rand(10000000,99999999);
        }
        if(!array_key_exists('orderReqNo',$params)){
            $params['orderReqNo']='mq'.date('YmdHis').rand(10000000,99999999);
        }
        if(!array_key_exists('channel',$params)){
            $params['channel']='05';
        }
        if(!array_key_exists('busiType',$params)){
            $params['busiType']='0000001';
        }
        if(!array_key_exists('orderDate',$params)){
            $params['orderDate']=date('YmdHis');
        }
        if(!array_key_exists('orderAmt',$params)){
            $info.='参数orderAmt不存在!';
        }else{
            if(!array_key_exists('productAmt',$params)){
                $params['productAmt']=$params['orderAmt'];
            }
            if(!array_key_exists('attachAmt',$params)){
                $params['attachAmt']=0;
            }
        }
        if(!array_key_exists('goodsName',$params)){
            $params['goodsName']='';
        }else{
            $params['goodsName']=urlencode($params['goodsName']);
        }
        if(!array_key_exists('storeId',$params)){
            $info.='参数storeId不存在!';
        }
        if(!array_key_exists('backUrl',$params)){
            $params['backUrl']=urlencode(route('bestpay.mpayback'));
        }else{
            $params['backUrl']=urlencode($params['backUrl']);
        }
//        $data['storeId']='e'.time().rand(100000,999999);
        if(!array_key_exists('ledgerDetail',$params)){
            $params['ledgerDetail']='';
        }
        if(!array_key_exists('attach',$params)){
            $params['attach']='';
        }
        if(!array_key_exists('mchntTmNum',$params)){
            $params['mchntTmNum']='';
        }
        if(!array_key_exists('deviceTmNum',$params)){
            $params['deviceTmNum']='';
        }
        if(!array_key_exists('erpNo',$params)){
            $params['erpNo']='';
        }
        if(!array_key_exists('goodsDetail',$params)){
            $params['goodsDetail']='';
        }
        if($info==''){
            //获取mac
            $param['MERCHANTID']=$params['merchantId'];
            $param['ORDERNO']=$params['orderNo'];
            $param['ORDERREQNO']=$params['orderReqNo'];
            $param['ORDERDATE']=$params['orderDate'];
            $param['BARCODE']=$params['barcode'];
            $param['ORDERAMT']=$params['orderAmt'];
            $param['KEY']=$key;
            $macstr = sprintf(self::MAC_STR,
                $param['MERCHANTID'],
                $param['ORDERNO'],
                $param['ORDERREQNO'],
                $param['ORDERDATE'],
                $param['BARCODE'],
                $param['ORDERAMT'],
                $param['KEY']
            );
            $md5=strtoupper(md5($macstr));
            $params['mac']=$md5;
            $paramsJoined = array();
            foreach($params as $k => $v) {
                $paramsJoined[] = "$k=$v";
            }
            $paramData = implode('&', $paramsJoined);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL                =>  self::MBAST_PAY_URL,
                CURLOPT_POST               =>  1,
                CURLOPT_SSL_VERIFYPEER     =>  FALSE,
                CURLOPT_SSL_VERIFYHOST     =>  FALSE,
                CURLOPT_RETURNTRANSFER     =>  1,
                CURLOPT_POSTFIELDS         =>  $paramData
            ));
            Log::info(microtime());
            $data = curl_exec($ch);
            curl_close($ch);
            Log::info(microtime());
            $res=json_decode($data,true);
            return  [
                'success'=>1,
                'data'=>$res
            ];
        }
        return [
            'success'=>0,
            'msg'=>$info
        ];

    }

    protected function checkBestPaySign(Array $result,$key)
    {
        $signstr="MERCHANTID=".$result['merchantId'].
            "&ORDERNO=".$result['orderNo'].
            "&ORDERREQNO=".$result['orderReqNo'].
            "&ORDERDATE=".(empty($result['orderDate'])?'null':$result['orderDate']).
            "&OURTRANSNO=".$result['ourTransNo'].
            "&TRANSAMT=".$result['transAmt'].
            "&TRANSSTATUS=".$result['transStatus'].
            "&ENCODETYPE=".$result['encodeType'].
            "&KEY=".$key;
        return strtoupper(md5($signstr))==$result['sign'];
    }

    protected function best_pay_query(Array $params,$key)
    {
        $info='';
        if(!array_key_exists('merchantId',$params)){
            $info.='参数merchantId不存在!';
        }
        if(!array_key_exists('orderNo',$params)){
            $info.='参数orderNo不存在!';
        }
        if(!array_key_exists('orderReqNo',$params)){
            $info.='参数orderReqNo不存在!';
        }
        if(!array_key_exists('orderDate',$params)){
            $info.='参数orderDate不存在!';
        }
        if($info==''){
            //获取mac
            $param['MERCHANTID']=$params['merchantId'];
            $param['ORDERNO']=$params['orderNo'];
            $param['ORDERREQNO']=$params['orderReqNo'];
            $param['ORDERDATE']=$params['orderDate'];
            $param['KEY']=$key;
            $macstr = sprintf(self::QUERY_MAC_STR,
                $param['MERCHANTID'],
                $param['ORDERNO'],
                $param['ORDERREQNO'],
                $param['ORDERDATE'],
                $param['KEY']
            );
            $md5=strtoupper(md5($macstr));
            $params['mac']=$md5;
            $paramsJoined = array();
            foreach($params as $k => $v) {
                $paramsJoined[] = "$k=$v";
            }
            $paramData = implode('&', $paramsJoined);
            Log::info($paramsJoined);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL                =>  self::MBAST_PAY_QUERY_URL,
                CURLOPT_POST               =>  1,
                CURLOPT_SSL_VERIFYPEER     =>  FALSE,
                CURLOPT_SSL_VERIFYHOST     =>  FALSE,
                CURLOPT_RETURNTRANSFER     =>  1,
                CURLOPT_POSTFIELDS         =>  $paramData
            ));
            $data = curl_exec($ch);
            Log::info($data);
            curl_close($ch);
            $res=json_decode($data,true);
            return  [
                'success'=>1,
                'data'=>$res
            ];
        }
        return [
            'success'=>0,
            'msg'=>$info
        ];
    }

    protected function best_pay_correct(Array $params,$paykey,$key)
    {
        $info='';
        if(!array_key_exists('merchantId',$params)){
            $info.='参数merchantId不存在!';
        }
        if(!array_key_exists('subMerchantId',$params)){
            $params['subMerchantId']='';
        }
        if(!array_key_exists('oldOrderNo',$params)){
            $info.='参数oldOrderNo不存在!';
        }
        if(!array_key_exists('oldOrderReqNo',$params)){
            $info.='参数oldOrderReqNo不存在!';
        }
        if(!array_key_exists('refundReqNo',$params)){
            $info.='参数refundReqNo不存在!';
        }
        if(!array_key_exists('refundReqDate',$params)){
            $info.='参数refundReqDate不存在!';
        }
        if(!array_key_exists('transAmt',$params)){
            $info.='参数transAmt不存在!';
        }
        if(!array_key_exists('channel',$params)){
            $params['channel']='05';
        }
        $params['merchantPwd']=$paykey;
        if($info==''){
            //获取mac
            $param['MERCHANTID']=$params['merchantId'];
            $param['MERCHANTPWD']=$params['merchantPwd'];
            $param['OLDORDERNO']=$params['oldOrderNo'];
            $param['OLDORDERREQNO']=$params['oldOrderReqNo'];
            $param['REFUNDREQNO']=$params['refundReqNo'];
            $param['REFUNDREQDAT']=$params['refundReqDate'];
            $param['TRANSAMT']=$params['transAmt'];
            $param['KEY']=$key;
            $macstr = sprintf(self::MAC_CORRECT_STR,
                $param['MERCHANTID'],
                $param['MERCHANTPWD'],
                $param['OLDORDERNO'],
                $param['OLDORDERREQNO'],
                $param['REFUNDREQNO'],
                $param['REFUNDREQDAT'],
                $param['TRANSAMT'],
                $param['KEY']
            );
            $md5=strtoupper(md5($macstr));
            $params['mac']=$md5;
            $paramsJoined = array();
            foreach($params as $k => $v) {
                $paramsJoined[] = "$k=$v";
            }
            $paramData = implode('&', $paramsJoined);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL                =>  self::MBAST_PAY_REVERSE_URL,
                CURLOPT_POST               =>  1,
                CURLOPT_SSL_VERIFYPEER     =>  FALSE,
                CURLOPT_SSL_VERIFYHOST     =>  FALSE,
                CURLOPT_RETURNTRANSFER     =>  1,
                CURLOPT_POSTFIELDS         =>  $paramData
            ));
            $data = curl_exec($ch);
            curl_close($ch);
            $res=json_decode($data,true);
            return  [
                'success'=>1,
                'data'=>$res
            ];
        }
        return [
            'success'=>0,
            'msg'=>$info
        ];
    }
}