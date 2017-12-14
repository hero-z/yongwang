<?php
/**
 *  暂时还没做
 * 浦发支付宝订单查询接口
 */

namespace App\Http\Controllers\PuFa;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\PuFa\Map;
use App\Models\PufaStores;
use App\Models\PufaTradeQueries;
use App\Http\Controllers\PuFa\Tools;
use Illuminate\Support\Facades\Config;


class AlipayTradeQueryController extends Controller
{


    static function log($data,$filename='./chencailog.txt')
    {
        return;
        file_put_contents($filename, date('Y-m-d H:i:s')."\n".var_export($data,true)."\n\n",FILE_APPEND);
    }


    /**
     * 去浦发查询订单情况
     */
    public function QueryOrder(Request $request)
    {
        //入参条件：输入订单号

        $out_trade_no=$request->get('out_trade_no');
        $out_trade_no='201703241714467255359';
        $order = PufaTradeQueries::where('out_trade_no', $out_trade_no)->first();

        if(empty($order))
        {
            echo '订单不存在';
            die;
        }

        $data=[
                'service'=>'unified.trade.query',
            'version'=>'2.0',
            'charset'=>'UTF-8',
            'sign_type'=>'MD5',
                'mch_id'=>$order['store_id'],//商户号
                'out_trade_no'=>$order['out_trade_no'],//服务商生成的商户流水号
                'nonce_str'=>md5($order['out_trade_no']),
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


/*
Array
(
    [appid] => 2016081701760348
    [attach] => 测试账号--有效收款
    [buyer_logon_id] => 134***@qq.com
    [buyer_pay_amount] => 0.01
    [buyer_user_id] => 2088812605592949
    [charset] => UTF-8
    [fee_type] => CNY
    [fund_bill_list] => [{"amount":"0.01","fundChannel":"ALIPAYACCOUNT"}]
    [invoice_amount] => 0.01
    [mch_id] => 101520000465
    [nonce_str] => 6e616f93ec2e1f474c24e72eb6dc762c
    [openid] => 134***@qq.com
    [out_trade_no] => 201703241714467255359
    [out_transaction_id] => 2017032421001004940290335661
    [point_amount] => 0.00
    [receipt_amount] => 0.01
    [result_code] => 0
    [sign] => 0E6AEDED4766A32E6E194FAF20C57239
    [sign_type] => MD5
    [status] => 0
    [time_end] => 20170324171500
    [total_fee] => 1
    [trade_state] => SUCCESS
    [trade_type] => pay.alipay.jspay
    [transaction_id] => 101520000465201703241029479741
    [version] => 2.0
)
*/




    //记录日志            
    self::log($thirddata,'./chencaiquery.log.txt');


                $verfysign=Tools::isTenpaySign($thirddata,$key);//验证签名
                if($verfysign&&$thirddata['status'] == 0 && $thirddata['result_code'] == 0)
                {
                    echo '查询成功<pre>';
                    echo '<br>';;
                    print_r($thirddata);die;
                }
                else
                {
                    echo '查询失败';
                }
            }








    }
 
}