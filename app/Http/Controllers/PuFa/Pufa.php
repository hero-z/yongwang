<?php
/*
*  浦发接口
*/
namespace App\Http\Controllers\PuFa;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PuFa\Tools;

// use App\User;
// use Illuminate\Http\Request;
// use Illuminate\Pagination\LengthAwarePaginator;
// use Illuminate\Pagination\Paginator;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
// use App\Models\PufaConfig;
// use App\Http\Controllers\PuFa\Verify;
// use App\Models\PufaStores;
// use App\Models\PufacqrLsitsinfo;

// use Illuminate\Support\Facades\Validator;
// use App\Merchant;

// use Illuminate\Support\Facades\Log;

class Pufa extends Controller
{
	protected static $obj=null;

	public $request_url;//商铺进件等信息的浦发网关
	public $partner;//服务商的合作号
	public $key;//接口秘钥

	private function __construct()
	{

	}

	public static function start()
	{
		if(!(self::$obj instanceof self))
		{
			self::$obj=new self();
		}
		return self::$obj;
	}
	/*
		修改商户费率  mch_bill_rate_deit
		入参：   商户在浦发的商户号
				支付类型英文名称
				费率
		返回：['status'=>'1',message=>'修改失败']
			['status'=>'2',message=>'修改成功']
	*/
	public function saveRate($merchant_id,$apiCode,$billRate)
	{

            $request_url=$this->request_url;
            // 主体数据
            $requestdata=[
                'mchPayConf'=>[
                    'merchantId'=>$merchant_id,//'浦发商户号'
                    'apiCode'=>$apiCode,//pay_road  表的idnum字段  数字
                    'billRate'=>$billRate,//'给商户配置的费率，单位是千分之'
                ]
            ];
            // 公共体数据
            $commondata=[
                'partner'=>$this->partner, 
                'serviceName'=>'mch_bill_rate_deit',
                'dataType'=>'xml',
                'charset'=>'UTF-8',
                'data'=>Tools::makeXml($requestdata),
                'dataSign'=>'',
            ];


            $key=$this->key;
            // 生成签名、生成xml数据
            $signdata = Tools::createjjSign($commondata, $key);
            // 向浦发接口发送xml数据
            $xmlresult = Tools::curl($signdata,$request_url);//获取银行xml数据
            //浦发接口返回数据     
            if($xmlresult)
            {
                $apireturn=Tools::xml_to_array($xmlresult);
                if(isset($apireturn['response']['isSuccess'])&&$apireturn['response']['isSuccess']=='T')
                {
                	return ['status'=>'2','message'=>'费率修改成功！'];
                	echo '修改成功';die;
                }
                else
                {
                	return ['status'=>'1','message'=>'费率修改失败！'.$apireturn['response']['errorMsg']];
                	echo '修改失败';die;
                }

            }
            else
            {
                return ['status'=>'1','message'=>'费率修改失败！浦发未返回数据！'];
            	echo '修改失败';die;
            }


	}














    public $paytype=[
        '221'=>['pay.weixin.native'=>'浦发广州-微信三通道线下扫码'],//'浦发广州-微信三通道线下扫码|pay.weixin.native',
        '222'=>['pay.weixin.micropay'=>'浦发广州-微信三通道线下小额'],//'浦发广州-微信三通道线下小额|pay.weixin.micropay',
        '223'=>['pay.weixin.jspay'=>'浦发广州-微信三通道公众账号'],//'浦发广州-微信三通道公众账号|pay.weixin.jspay',
        '10000164'=>['pay.alipay.jspayv3'=>'浦发广州-支付宝三通道服务窗支付'],//'浦发广州-支付宝三通道服务窗支付|pay.alipay.jspayv3',
        '10000165'=>['pay.alipay.nativev3'=>'浦发广州-支付宝三通道扫码支付'],//'浦发广州-支付宝三通道扫码支付|pay.alipay.nativev3',
        '10000166'=>['pay.alipay.micropayv3'=>'浦发广州-支付宝三通道小额支付'],//'浦发广州-支付宝三通道小额支付|pay.alipay.micropayv3',
    ];


/*
	支付类型通道
	入参：
		类属性  request_url
		        partner
		        key
		业务数据
				merchant_id
				idnum   支付方式数字
				rate  费率设置，页面表单中的费率

	返回：
		return ['status'=>1,'message'=>'浦发接口连接中断！'];
		return ['status'=>'2','message'=>'支付类型开启成功！','data'=>$paytype];//data的是 pay.weixin.jspay 支付类型英文描述
*/

	public function payType($merchant_id,$idnum,$rate,$pid,$trans_partner)
	{
        try
        {
            $request_url=$this->request_url;
            // 主体数据
            $requestdata=[
                'mchPayConf'=>[
                    'merchantId'=>$merchant_id,//'浦发商户号'
                    'payTypeId'=>$idnum,//pay_road  表的idnum字段  数字  也是表单中提交过来的
                    'billRate'=>$rate,//费率是表单中提交过来的
                    // 'partner'=>'微信，第一家商户进件后浦发会通知，第一家是不用传的'
                    // 'pid'=>'服务商支付宝pid，在isv_config'
                ]
            ];

            $paytypearr=explode('.',key($this->paytype[$idnum]));
            // 微信需要传商户的第三方商户号，第一家进件资料后浦发银行会提供。
            if($paytypearr[1]=='weixin')
            {
                if(!empty($trans_partner))
                {
                    $requestdata['mchPayConf']['partner']=$trans_partner;//除了第一家商户外，别的商户必须上传
                }
            }
            elseif($paytypearr[1]=='alipay')
            {
                $requestdata['mchPayConf']['pid']=$pid;
            }
            else
            {
                return ['status'=>1,'message'=>'代码错误！请检查代码！'];
            }



            // 公共体数据
            $commondata=[
                'partner'=>$this->partner, 
                'serviceName'=>'normal_mch_pay_conf_add',
                'dataType'=>'xml',
                'charset'=>'UTF-8',
                'data'=>Tools::makeXml($requestdata),
                'dataSign'=>'',
            ];

            $key=$this->key;
            // 生成签名、生成xml数据
            $signdata = Tools::createjjSign($commondata, $key);
            $xmlresult = Tools::curl($signdata,$request_url);//获取银行xml数据

            //浦发接口返回数据     
            if($xmlresult)
            {
                $apireturn=Tools::xml_to_array($xmlresult);

                if(isset($apireturn['response']['isSuccess'])&&$apireturn['response']['isSuccess']=='T')
                {
                    $paytype=$apireturn['response']['mchPayConf']['apiCode'];
                    return ['status'=>'2','message'=>'支付类型开启成功！','data'=>$paytype];//返回的是  支付类型英文描述
                }
                //已经成功申请过的支付类型
                elseif(isset($apireturn['response']['isSuccess'])&&$apireturn['response']['isSuccess']=='F'&&isset($apireturn['response']['errorCode'])&&$apireturn['response']['errorCode']=='mchpayconf.exsit')
                {
                    $allpaytype=$this->paytype;
                    $paytype=key($allpaytype[$idnum]);
                    return ['status'=>'2','message'=>'支付类型开启成功！','data'=>$paytype];//返回的是  支付类型英文描述
                }
                else
                {
                    $msg=isset($apireturn['response']['errorMsg'])?$apireturn['response']['errorMsg']:'';
                    return ['status'=>1,'message'=>'支付类型开启失败：'.$msg];
                }

            }
            else
            {
                return ['status'=>1,'message'=>'浦发接口连接中断！'];
            }

        }
        catch(\Exception $e)
        {
            return ['status'=>1,'message'=>'通道开启失败！'.$e->getMessage()];
        }

	}



}