<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/4/19
 * Time: 10:07
 */

namespace App\Http\Controllers\UnionPay;

use App\Models\PufaConfig;
use App\Http\Controllers\Push\AopClient;
use Illuminate\Support\Facades\DB;
use App\Models\PushConfig;
use App\Models\PushPrintShopList;
use App\Models\Order;
use App\Models\PageSets;
use App\Models\UnionPayConfig;
use App\Models\WeixinPayConfig;
use App\Models\WeixinPayNotify;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotifyController extends BaseController
{

    public function notify_url(Request $request)
    {
        $data = $request->all();
        $check = $this->Check($data);
        if ($check) {
            $order = Order::where('out_trade_no', $data['out_trade_no'])->first();
            $Order = Order::where('out_trade_no', $data['out_trade_no'])->first();
            if ($order) {
                if ($order->status != $data['pay_status']) {
                    Order::where('out_trade_no', $data['out_trade_no'])->update(
                        [
                            'status' => $data['pay_status'],
                            'trade_no'=>$data['trade_no'],
                            'pay_status' => $this->pay_status($data['pay_status']),
                            'total_amount'=>$data['order_money'],
                            'cost_rate'=>$data['cost_rate'],
                            'service_rate'=>$data['service_rate'],
                        ]
                    );
                    //支付成功
                    if ($data['pay_status'] == "PAY_SUCCESS") {

                        //易联云打印机
                        try {
                            //小票机
                            $push = new AopClient();
                            $PushConfig = PushConfig::where('id', 1)->first();
                            $store=DB::table("union_pay_stores")->where('store_id',$Order->store_id)->first();

                            $push_store = PushPrintShopList::where('store_id', $Order->store_id)->where("type","yilianyun")->get();
                            $info = PushPrintShopList::where('store_id', $Order->store_id)->where("type","yilianyun")->first();

                            $store_name=$store->alias_name;
                            $phone=$info->phone;
                            if ($push_store){
                                $str = "<center>".$store_name."</center>
            <center>联系方式:".$phone."</center>
订单号:" . $Order->out_trade_no . "
付款金额:" . $Order->total_amount . "
付款用户:银联固定二维码
付款时间:" . date('Y-m-d:H-i-s', time()) . "
商户备注:" . $Order->remark . "
   ";
                                $conent = urlencode($str);
                                $arr = $push_store->toArray();//有可能多个
                                foreach ($arr as $v) {
                                    try {
                                        $print = $push->action_print(
                                            $PushConfig->push_id,
                                            $v['machine_code'],
                                            $conent, $PushConfig->push_key,
                                            $v['msign']);
                                    } catch (\Exception $exception) {
                                        continue;
                                    }

                                }
                            }
                        } catch (\Exception $exception) {
                            Log::info($exception);
                        }

                        //打印机 api 调用
                        try {
                            $Uprint = new \App\Http\Controllers\Push\Uprint();
                            $list = PushPrintShopList::where("store_id",$Order->store_id)->where("type", "Uprint")->first();
                            $out_trade_no = $Order->out_trade_no;
                            $total_amount = $Order->total_amount;
                            $date = date('Y-m-d:H-i-s', time());
                            $remark = $Order->remark;
                            if($list){
                                $DEVICE_NO = $list->machine_code; //U 印打印机身编号
                                $number = $list->number;
                                $code = $list->code;
                                $phone=$list->phone;
                                $code_description = $list->code_description;
                            }
                            if ($list) {
                                $message = "";
                                $message = $message . "^N" . $number; //将该订单打印两次
                                $message = $message . "^F1";//来电提醒
                                $message = $message . "\n";
                                //打印二维码//
                                $my_qrcode = $code;//要生成的二维码
                                $myurl_length = strlen($my_qrcode); //获取二维码链接长度
                                $ascii_length = chr($myurl_length); //把长度数字转换为 ASCII 码
                                $message = $message . "\n";
                                $message = $message . "^Q";
                                $message = $message . $ascii_length;
                                $message = $message . $my_qrcode;
                                //打印二维码操作完成//
                                $message = $message . "\n";
                                $message = $message . "^H2     " . $store_name; //将”打印演示”横纵放大 2 倍
                                $message = $message . "\n";
                                $message = $message . "\n";
                                $message = $message . " 联系方式： ";
                                $message = $message . $phone; //将”打印演示”横纵放大 2 倍
                                $message = $message . "\n";
                                $message = $message . "\n";
                                $message = $message . " 支付状态： ";//将”支付状态： ”纵向放大 2 倍
                                $message = $message . "已付款";
                                $message = $message . "\n";
                                $message = $message . " 订单号： ";
                                $message = $message . $out_trade_no;
                                $message = $message . "\n";
                                $message = $message . " 付款金额： ";
                                $message = $message . $total_amount;
                                $message = $message . "\n";
                                $message = $message . " 付款方式： ";
                                $message = $message . "银联固定二维码";
                                $message = $message . "\n";
                                $message = $message . " 付款时间： ";
                                $message = $message . $date;
                                $message = $message . "\n";
                                $message = $message . " 商户备注： ";
                                $message = $message . $remark;
                                $message = $message . "\n";
                                $message = $message . ". . . . . . . . . . . . . . . .  ";
                                $message = $message . $code_description;
                                $message = $message . "\n";
                                $Uprint->add_order($DEVICE_NO, $out_trade_no, $message);
                            }

                        } catch (\Exception $e) {
                            Log::info($e);
                        }



                        //微信提醒
                        $WeixinPayNotifyStore = WeixinPayNotify::where('store_id', $order->store_id)->first();
                        //实例化
                        $config = WeixinPayConfig::where('id', 1)->first();
                        $options = [
                            'app_id' => $config->app_id,
                            'secret' => $config->secret,
                            'token' => '18851186776',
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
                        $data = array(
                            "keyword1" => $data['order_money'],
                            "keyword2" => '银联二维码(固定金额码)',
                            "keyword3" => '' . date('Y-m-d H:i:s',time()). '',
                            "keyword4" => $data['trade_no'],
                            "remark" => '祝生意红火',
                        );
                        foreach ($open_ids as $v) {
                            $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($v)->send();
                        }

                    }
                }
            }
        }
    }

    public function pay_status($status)
    {
        if ($status == "PAY_SUCCESS") {
            $pay_status = 1;
        }

        if ($status == "PAY_FAILURE") {
            $pay_status = 2;
        }
        if ($status == "PAY_WAIT") {
            $pay_status = 3;
        }
        return $pay_status;
    }

    public function Check($request)
    {
        //支付异步通知
        $config = UnionPayConfig::where('id', 1)->first();
        $ao = $this->AopClient();
        $aop = new \Alipayopen\Sdk\AopClient();
        $aop->appId = $config->appId;
        $aop->rsaPrivateKey = $config->rsa_private_key;
        $aop->gatewayUrl = $ao->gatewayUrl;
        $aop->signType = 'RSA';
        $aop->alipayrsaPublicKey = $config->union_public_key;
        $true = $aop->rsaCheckUmxnt($request, $config->pinganrsaPublicKey, 'RSA');
        return $true;
    }

}