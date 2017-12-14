<?php
/**
* 暂时还没做
 * 浦发支付宝退单接口
 */

namespace App\Http\Controllers\PuFa;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\PuFa\Map;
use App\Models\PufaStores;
use App\Models\PufaTradeQueries;
use App\Http\Controllers\PuFa\Tools;
use Illuminate\Support\Facades\Config;


class AlipayTradeRefundController extends Controller
{


    static function log($data,$filename='./chencailog.txt')
    {
        return;
        file_put_contents($filename, date('Y-m-d H:i:s')."\n".var_export($data,true)."\n\n",FILE_APPEND);
    }


    /**
     * 退单流程：  查询退单表，1有退单记录并且状态成功的直接返回；2有退单记录并且状态失败的做修改操作；3无退单记录做增加记录处理
     */
    public function RefundOrder(Request $request)
    {
        //入参条件：输入订单号及退单金额
        $out_trade_no=$request->get('out_trade_no');
        $out_trade_no='201703241714467255359';
        $refund_fee=0.01;
        $order = PufaTradeQueries::where('out_trade_no', $out_trade_no)->first();

        $shijian=date('YmdHis');
        $out_refund_no = $shijian . rand(1000000, 9999999);//服务商生成的交易流水号

        if(empty($order))
        {
            echo '订单不存在';
            die;
        }

        $data=[
                'service'=>'unified.trade.refund',
            'version'=>'2.0',
            'charset'=>'UTF-8',
            'sign_type'=>'MD5',
                'mch_id'=>$order['store_id'],//商户号--查询
                'out_trade_no'=>$out_trade_no,//商户订单号--查询
                'out_refund_no'=>$out_refund_no,//退款流水单号
                'total_fee'=>(int)($order['total_amount']*100),//总订单金额
                'refund_fee'=>(int)($refund_fee*100),//退款金额
                'op_user_id'=>'cc',//操作员--商户号
                'refund_channel'=>'ORIGINAL',//原路退款
                'nonce_str'=>md5($order['out_trade_no']),
                'sign'=>''
        ];


        $shop = PufaStores::where('store_id', $order['store_id'])->first();

        if(empty($shop))
        {
            echo '商户不存在';
            die;
        }
        else
        {
            $shop=$shop->toArray();
        }

        $key=$shop['store_pwd'];
        $request_url=Config::get('alipaypufa.pf_request_url');

            // 生成签名、生成xml数据
            $data=Tools::createSign($data,$key);
            $xmldata=Tools::toXml($data);//生成xml数据

            // 向浦发接口发送xml下单数据
            $xmlresult=Tools::curl($xmldata,$request_url);//获取银行xml数据
            //获取到数据
            if($xmlresult)
            {

                $thirddata=Tools::setContent($xmlresult);//返回银行结果数组

                var_dump($thirddata);






    //记录日志            
    self::log($thirddata,'./chencairefund.log.txt');


                $verfysign=Tools::isTenpaySign($thirddata,$key);//验证签名
                if($verfysign&&$thirddata['status'] == 0 && $thirddata['result_code'] == 0)
                {


/*

Array
(
    [charset] => UTF-8
    [mch_id] => 101520000465
    [nonce_str] => 6e616f93ec2e1f474c24e72eb6dc762c
    [out_refund_no] => 201703241855074983416
    [out_trade_no] => 201703241714467255359
    [refund_channel] => ORIGINAL
    [refund_fee] => 1
    [refund_id] => 101520000465201703241129808845
    [result_code] => 0
    [sign] => 4911385EE95361BA6CB44F5E3B6F9451
    [sign_type] => MD5
    [status] => 0
    [trade_type] => pay.alipay.jspay
    [transaction_id] => 101520000465201703241029479741
    [version] => 2.0
)
*/                
                    echo '退款成功<pre>';
                    echo '<br>';;
                    print_r($thirddata);die;
                }
                else
                {
                    echo '退款失败';
                }
            }







    }

    //更新订单状态
    public function UpdateStatus(Request $request)
    {

    }
}