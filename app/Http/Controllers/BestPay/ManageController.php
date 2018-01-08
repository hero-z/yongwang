<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2017/12/16
 * Time: 14:01
 */

namespace App\Http\Controllers\BestPay;



use App\Merchant;
use App\Models\BestPayStore;
use App\Models\Bill;
use App\Models\MerchantShops;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManageController  extends BaseController
{
    const MBAST_PAY_URL='https://webpaywg.bestpay.com.cn/barcode/placeOrder';
    const MAC_STR='MERCHANTID=%s&ORDERNO=%s&ORDERREQNO=%s&ORDERDATE=%s&BARCODE=%s&ORDERAMT=%s&KEY=%s';
    const PAY_STR='merchantId=%s&subMerchantId=%s&barcode=%s&orderNo=%s&orderReqNo=%s&orderDate=%s&channel=%s&busiType=%s&orderAmt=%s&productAmt=%s&attachAmt=%s&goodsName=%s&storeId=%s&backUrl=%s&ledgerDetail=%s&attach=%s&mac=%s';
    public function test()
    {
        $data['merchantId']='02150108040598665';
        $data['subMerchantId']='';
        $data['barcode']='510161901066753799';
        $data['orderNo']='mb'.date('YmdHis').rand(100000000,99999999);
        $data['orderReqNo']='mq'.date('YmdHis').rand(100000000,99999999);
        $data['channel']='05';
        $data['busiType']='0000001';
        $data['orderDate']=date('YmdHis');
        $data['orderAmt']=1;
        $data['productAmt']=1;
        $data['attachAmt']=0;
        $data['goodsName']=urlencode('test');
        $data['storeId']='e1514449053874186';
//        $data['storeId']='e'.time().rand(100000,999999);
        $data['backUrl']=urlencode(route('bestpay.mpayback'));
        $data['ledgerDetail']='';
        $data['attach']='';
        $data['mchntTmNum']='';
        $data['deviceTmNum']='';
        $data['erpNo']='';
        $data['goodsDetail']='';
        //获取mac
        $param['MERCHANTID']=$data['merchantId'];
        $param['ORDERNO']=$data['orderNo'];
        $param['ORDERREQNO']=$data['orderReqNo'];
        $param['ORDERDATE']=$data['orderDate'];
        $param['BARCODE']=$data['barcode'];
        $param['ORDERAMT']=$data['orderAmt'];
        $param['KEY']='327253DC94ED5D7E691E8BBAB3EDE0118E6C4EADE879552C';
        $mac=self::GetMac($param);
        if($mac['success']){
            $data['mac']=$mac['data'];
        }else{
            dd($mac['msg']);
        }
        Log::info($data);
        $paramsJoined = array();
        foreach($data as $param => $value) {
            $paramsJoined[] = "$param=$value";
        }
        $paramData = implode('&', $paramsJoined);
        Log::info($paramsJoined);

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL                =>  self::MBAST_PAY_URL,
            CURLOPT_POST               =>  1,
            CURLOPT_SSL_VERIFYPEER     =>  FALSE,
            CURLOPT_SSL_VERIFYHOST     =>  FALSE,
            CURLOPT_RETURNTRANSFER     =>  1,
            CURLOPT_POSTFIELDS         =>  $paramData
        ));
        Log::info($ch);
        $data = curl_exec($ch);
        Log::info($data);
        curl_close($ch);
        $res=json_decode($data,true);

        if($res['success'])
        {
            dd($res);
        }else{
            dd($res);
        }

        //step 2 fetch the pubkey
        $params=array(
            'keyIndex' => '',
            'encryKey' => '',
            'encryStr' => '',
            'interCode' => 'INTER.SYSTEM.001'
        );

        $paramData=json_encode($params);


        $ch = curl_init();
        $header =  array('Content-Type: application/json');
        curl_setopt_array($ch, array(
            CURLOPT_URL                =>  $URL."/common/interface",
            CURLOPT_HTTPHEADER         =>  $header,
            CURLOPT_POST               =>  1,
            CURLOPT_SSL_VERIFYPEER     =>  FALSE,
            CURLOPT_SSL_VERIFYHOST     =>  FALSE,
            CURLOPT_RETURNTRANSFER     =>  1,
            CURLOPT_POSTFIELDS         =>  $paramData
        ));

        $data = curl_exec($ch);
        curl_close($ch);

        $res_arr = json_decode($data,true);
        if($res_arr && $res_arr['success'])
        {
            //get the params
            $keyIndex=$res_arr['result']['keyIndex'];
            $pubKey=$res_arr['result']['pubKey'];

            $pay_params=array(
                'SERVICE' => 'mobile.securitypay.pay',
                'MERCHANTID' => $payment['bestpay_account'],
                'MERCHANTPWD' => $MerchantPwd,
                'BEFOREMERCHANTURL' => "http://www.baidu.com",
                'BACKMERCHANTURL' => "http://127.0.0.1:8040/wapBgNotice.action",
                'ORDERSEQ'=>$orderseq,
                'ORDERREQTRANSEQ'=>$ordereqtranseq,
                'ORDERTIME'=>"$time",
                'CURTYPE'=>'RMB',
                'ORDERAMOUNT'=>$order['order_amount'],
                'SUBJECT'=>$subject,
                'PRODUCTID'=>'04',
                'SIGNTYPE'=>'MD5',
                'PRODUCTDESC'=>$subject,
                'PRODUCTAMOUNT'=>$order['order_amount'],
                'ATTACHAMOUNT'=>'0',
                'CUSTOMERID'=>'1',
                'BUSITYPE'=>'04',
                'SWTICHACC'=>'false'
            );


            $paramsJoined = array();
            foreach($pay_params as $param => $value) {
                $paramsJoined[] = "$param=$value";
            }
            $pay_paramData = implode('&', $paramsJoined);

            $sign_params=array(
                'SERVICE' => 'mobile.securitypay.pay',
                'MERCHANTID' => $payment['bestpay_account'],
                'MERCHANTPWD' => $MerchantPwd,
                'SUBMERCHANTID' => '',
                'BACKMERCHANTURL'=>"http://127.0.0.1:8040/wapBgNotice.action",
                'ORDERSEQ'=>$orderseq,
                'ORDERREQTRANSEQ'=>$ordereqtranseq,
                'ORDERTIME'=>"$time",
                'ORDERVALIDITYTIME'=>'',
                'CURTYPE'=>'RMB',
                'ORDERAMOUNT'=>$order['order_amount'],
                'SUBJECT'=>$subject,
                'PRODUCTID'=>'04',
                'PRODUCTDESC'=>$subject,
                'CUSTOMERID'=>'1',
                'SWTICHACC'=>'false',
                'KEY'=>$payment['bestpay_key']
            );

            $paramsJoined = array();
            foreach($sign_params as $param => $value) {
                $paramsJoined[] = "$param=$value";
            }
            $sign_paramData = implode('&', $paramsJoined);

            $sign = strtoupper(md5($sign_paramData));

            $pay_paramData.="&SIGN=".$sign;


            $random_key=md5(mt_rand());
            $cipher = new Crypt_AES();
            $cipher->setKey($random_key);
            $crypttext = base64_encode($cipher->encrypt($pay_paramData));

            $rsa = new Crypt_RSA();
            $rsa->loadKey($pubKey);

            $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
            $encrypted = base64_encode($rsa->encrypt($random_key));


            $webUrl = $URL."/gateway.pay?platform=wap_3.0&encryStr=".$crypttext."&keyIndex=".$keyIndex."&encryKey=".$encrypted;
            $webUrl = str_replace("+","%2B",$webUrl);

            $button = '<input type="button" onclick="javascript:window.location.href=\''.$webUrl.'\'" value="立即支付" />';

        }

        return $button;
    }

    protected function GetMac(Array $param)
    {
        $info='';
        if(!array_key_exists('MERCHANTID',$param)){
            $info.='键MERCHANTID不存在!';
        }
        if(!array_key_exists('ORDERNO',$param)){
            $info.='键ORDERNO不存在!';
        }
        if(!array_key_exists('ORDERREQNO',$param)){
            $info.='键ORDERREQNO不存在!';
        }
        if(!array_key_exists('ORDERDATE',$param)){
            $info.='键ORDERDATE不存在!';
        }
        if(!array_key_exists('BARCODE',$param)){
            $info.='键BARCODE不存在!';
        }
        if(!array_key_exists('ORDERAMT',$param)){
            $info.='键ORDERAMT不存在!';
        }
        if(!array_key_exists('KEY',$param)){
            $info.='键KEY不存在!';
        }
        if($info==''){
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
            return [
                'success'=>1,
                'data'=>$md5
            ];
        }
        return [
            'success'=>0,
            'msg'=>$info
        ];
    }

    public function index(Request $request)
    {
        $alias_name=$request->alias_name;
        $wheresql=[];
        if($alias_name){
            $wheresql[]=['best_pay_stores.alias_name','like','%'.$alias_name.'%'];
        }
        if(!Auth::user()->hasRole('admin')){
            $wheresql[]=['best_pay_stores.admin_id',Auth::user()->id];
        }
        $stores=DB::table('best_pay_stores')
            ->join('merchants','best_pay_stores.merchant_id','merchants.id')
            ->join('users','best_pay_stores.admin_id','users.id')
            ->where('best_pay_stores.pid',0)
            ->where($wheresql)
            ->select('best_pay_stores.*','users.name as user_name','merchants.name as merchant_name')
            ->orderBy('best_pay_stores.updated_at','desc')
            ->paginate(8);
//        $stores=BestPayStore::where('pid',0)->where($wheresql)->paginate(8);
        return view('admin.bestpay.index',compact('stores','alias_name'));
    }

    public function add(Request $request)
    {
        $info='';
        try{
            if($request->isMethod('GET')){
                return view('admin.bestpay.add');
            }elseif($request->isMethod('POST')){
                $insertdata=$request->except('_token');
                $m_id=$request->merchant_id;
                $bstore=BestPayStore::where('merchant_id',$m_id)->first();
                $merchant=Merchant::find($m_id);
                if($merchant&&$merchant->pid==0){
                    if(!$bstore){
                        $insertdata['store_id']='e'.time().rand(100000,999999);
                        $insertdata['admin_id']=Auth::user()->id;
                        $res=BestPayStore::create($insertdata);
                        $res2=MerchantShops::create([
                            'merchant_id'=>$m_id,
                            'store_id'=>$insertdata['store_id'],
                            'store_name'=>$insertdata['alias_name'],
                            'store_type'=>'bestpay',
                            'desc_pay'=>'官方翼支付',
                        ]);
                        if($res&&$res2){
                            return json_encode([
                                "success"=>1,
                                "msg"=>'添加成功!'
                            ]);
                        }else{
                            $info='数据库操作失败!';
                        }
                    }else{
                        $info='商户id为'.$m_id.'的商户已经绑定了翼支付通道!';
                    }
                }else{
                    $info='商户不存在!或者商户非管理员账号!';
                }
                return json_encode([
                    "success"=>0,
                    "msg"=>'添加失败!'.$info
                ]);
            }
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
            $info=$error.$line;
            if($request->isMethod('POST')){
                return json_encode([
                    "success"=>0,
                    "msg"=>"查询失败!".$info
                ]);
            }
        }
        return view('admin.webank.error',compact('info'));
    }

    public function del(Request $request)
    {
        $info='';
        if(Auth::user()->hasRole('admin')){
            $id=$request->id;
            $bstore=BestPayStore::find($id);
            if($bstore){
                $res=$bstore->delete();
                $res2=MerchantShops::where('store_id',$bstore->store_id)->delete();
                if($res&&$res2){
                    return json_encode([
                        'success'=>1,
                        'msg'=>'操作成功!',
                    ]);
                }else{
                    $info='数据库操作失败';
                }
            }else{
                $info='查询出错';
            }
        }else{
            $info='没有权限!';
        }
        return json_encode([
            'success'=>0,
            'msg'=>$info,
        ]);
    }

    public function query(Request $request)
    {
        $wheresql=[];
        if(!Auth::user()->hasRole('admin')){
            $wheresql[]=['bills.admin_id',Auth::user()->id];
        }
        $bills=Bill::where('type',101)->where($wheresql)->orderBy('updated_at','desc')->paginate(8);
        $admins=$merchants=[];
        if($bills){
            foreach ($bills as $v) {
                $admins[]=$v->admin_id;
                $merchants[]=$v->merchant_id;
            }
            $admins=User::whereIn("id",$admins)->pluck('name',"id")->toArray();
            $merchants=Merchant::whereIn("id",$merchants)->pluck('name',"id")->toArray();
        }
        return view('admin.bestpay.bill',compact('admins','merchants','bills'));
    }
}