<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/17
 * Time: 16:09
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Http\Controllers\Push\AopClient;
use App\Http\Controllers\Push\JpushController;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayShopLists;
use App\Models\AlipayStoreInfo;
use App\Models\AlipayTradeQuery;
use App\Models\MerchantOrders;
use App\Models\Order;
use App\Models\PageSets;
use App\Models\PushConfig;
use App\Models\PushPrintShopList;
use App\Models\WeixinPayConfig;
use App\Models\WeixinPayNotify;
use App\Models\WXNotify;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class NotifyController extends AlipayOpenController
{
    //机器
    public function notify_m(Request $request)
    {
        //支付异步通知
        $config = AlipayIsvConfig::where('id', 1)->first();
        $alipayrsaPublicKey = $config->alipayrsaPublicKey;
        $aop = $this->AopClientNotify();
        $aop->alipayrsaPublicKey = $alipayrsaPublicKey;
        $umxnt = $aop->rsaCheckUmxnt($request->all(), $alipayrsaPublicKey);
        if ($umxnt) {
            $data = $request->all();
            $AlipayTradeQuery = Order::where('out_trade_no', $data['out_trade_no'])->first();
            //如果状态不相同修改数据库状态
            if ($AlipayTradeQuery->status != $data['trade_status']) {
                Order::where('out_trade_no', $data['out_trade_no'])->update([
                    'status' => $data['trade_status'],
                    'total_amount' => $data['receipt_amount'],
                    'pay_status' => $this->pay_status($data['trade_status']),
                ]);
            }

        }

        return true;
    }

    //扫码通知
    public function notify(Request $request)
    {
        //支付异步通知
        $config = AlipayIsvConfig::where('id', 1)->first();
        $alipayrsaPublicKey = $config->alipayrsaPublicKey;
        $aop = $this->AopClientNotify();
        $aop->alipayrsaPublicKey = $alipayrsaPublicKey;
        $umxnt = $aop->rsaCheckUmxnt($request->all(), $alipayrsaPublicKey);
        if ($umxnt) {
            $data = $request->all();
            $Order = Order::where('out_trade_no', $data['out_trade_no'])->first();
            //如果状态不相同修改数据库状态
            if ($Order->status != $data['trade_status']) {
                Log::info($data);
                Order::where('out_trade_no', $data['out_trade_no'])->update([
                    'status' => $data['trade_status'],
                    'pay_status' => $this->pay_status($data['trade_status']),
                    'total_amount' => $data['receipt_amount'],
                ]);

                //微信通知商户收营员
                try {
                    //店铺通知微信
                    if ($data['trade_status'] == 'TRADE_SUCCESS') {
                        $push_store = PushPrintShopList::where('store_id', $Order->store_id)->where("type","yilianyun")->get();
                        $info = PushPrintShopList::where('store_id', $Order->store_id)->where("type","yilianyun")->first();

                        try {
                            //小票机
                            $push = new AopClient();
                            $PushConfig = PushConfig::where('id', 1)->first();
                            $substr = substr($Order->store_id, 0, 1);
                            if ($substr == 'o') {
                                $store=AlipayAppOauthUsers::where('store_id',$Order->store_id)->first();
                                $store_name=$store->auth_shop_name;
                                $phone=$info->phone;
                            } else {
                                $store=AlipayShopLists::where('store_id',$Order->store_id)->first();
                                $store_name=$store->main_shop_name;
                                $phone=$info->phone;

                            }
                            if ($push_store){
                                $str = "<center>".$store_name."</center>
            <center>联系方式:".$phone."</center>
订单号:" . $Order->out_trade_no . "
付款金额:" . $Order->total_amount . "
付款用户:支付宝
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

                        //安卓app语音播报
                        $jpush=new JpushController();
                        $jpush->push(''.$data['out_trade_no'].'-支付宝', $data['total_amount'],$data['out_trade_no']);

                        //U打印
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
                                $code_description = $list->code_description;
                                $phone=$list->phone;
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
                                $message = $message . "支付宝";
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




                        $store_id = $Order->store_id;
                        $WeixinPayNotifyStore = WeixinPayNotify::where('store_id', $store_id)->first();
                        //实例化
                        $config = WeixinPayConfig::where('id', 1)->first();

                        if ($WeixinPayNotifyStore&&$config) {
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
                                "keyword2" => '支付宝(' . $data['buyer_logon_id'] . ')'.$markstr,
                                "keyword3" => '' . $Order->updated_at . '',
                                "keyword4" => $data['trade_no'],
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

                } catch (\Exception $exception) {
                    Log::info($exception);
                    return json_encode([
                        'status' => 1,
                    ]);
                }
            }
        }
        return true;
    }

    public function alipay_notify(Request $request)
    {
        Log::info('...' . $request);
    }

    //商户开店状态通知URL
    public function operate_notify_url(Request $request)
    {
        Log::info($request);
        $requestArray = $request->toArray();
        $config = AlipayIsvConfig::where('id', 1)->first();
        $alipayrsaPublicKey = $config->alipayrsaPublicKey;
        $aop = $this->AopClientNotify();
        $aop->alipayrsaPublicKey = $alipayrsaPublicKey;
        $umxnt = $aop->rsaCheckUmxnt($requestArray, $alipayrsaPublicKey);
        Log::info('_________' . $umxnt);
        if ($umxnt) {
            $data = [
                'shop_id' => $request->get('shop_id', ''),
                'audit_status' => $request->get('audit_status'),

            ];
            AlipayShopLists::where('apply_id', $request->get('apply_id'))->update($data);
            $store = AlipayShopLists::where('apply_id', $request->get('apply_id'))->first();
            $storeInfo = AlipayStoreInfo::where('store_id', $store->store_id)->first();
            $dataInfo = [
                'store_id' => $store->store_id,
                'biz_type' => $request->get('biz_type', ''),
                'notify_time' => $request->get('notify_time', ''),
                'shop_id' => $request->get('shop_id', ''),
                'apply_id' => $request->get('apply_id', ''),
                'is_show' => $request->get('is_show', ''),
                'request_id' => $request->get('request_id', ''),
                'audit_status' => $request->get('audit_status', ''),
            ];
            if ($request->get('result_code', '')) {
                $dataInfo['result_code'] = $request->get('result_code', '');
                $dataInfo['result_desc'] = $request->get('result_desc', '');
            }
            if ($storeInfo) {
                AlipayStoreInfo::where('store_id', $store->store_id)->update($dataInfo);
            } else {
                AlipayStoreInfo::create($dataInfo);
            }
        } else {
            return false;
        }

        return true;
    }

    public function pay_status($status)
    {
        if ($status == "TRADE_SUCCESS") {
            $pay_status = 1;
        } elseif ($status == "TRADE_CLOSED") {
            $pay_status = 4;
        } elseif ($status == "WAIT_BUYER_PAY") {
            $pay_status = 3;
        } else {
            $pay_status = 2;
        }
        return $pay_status;
    }
}

//通过的返回提醒
/*$request=array (
          'is_online' => 'T',
          'biz_type' => 'CREATE_SHOP_AUDIT',
          'notify_time' => '2017-02-04 16:42:11',
          'shop_id' => '2017020400077000000000058537',
          'sign_type' => 'RSA',
          'notify_type' => 'shop_audit_result',
          'apply_id' => '2017020400107000000000090975',
          'version' => '2.0',
          'sign' => 'lQrTCugG0iibEfoaNyBPtr8VXSZGznfKUBmaV769b2SskvIJP83nBJ8whMwLKD5i4rWE9ux+u1ANzaEx5aMLxuDcbr143l41QSwADwMoqWEbD5TxY5NDCOD8biLjqtYhpXFJlOG+RDqtWV8ExnmfN8OSwawsteKh4mu+sk1b7h4=',
          'is_show' => 'T',
          'request_id' => '20170204163910',
          'notify_id' => '98408d09b1f2d0c065ecdf828a43b24mhe',
          'audit_status' => 'AUDIT_SUCCESS',
      );*/

//不通过的返回提醒
/*array (
    'is_online' => 'F',
    'biz_type' => 'CREATE_SHOP_AUDIT',
    'notify_time' => '2017-02-13 13:23:16',
    'sign_type' => 'RSA2',
    'notify_type' => 'shop_audit_result',
    'apply_id' => '2017021300107000000027889581',
    'version' => '2.0',
    'result_code' => 'RISK_AUDIT_FAIL',
    'sign' => 'QLQC/U7ACBT5g512PzIyeg9t19XLHUmojJuaMpXqbweXCg4HhZoJiI5Jg+yBOIJ99vU5imKCUDza+GcS8SKGSNeL9MI0ZkqLoEk7lIHwsHqqWM0+XbE61S0lNKdJkVY8Unhn7ylCBxFbUYT0Lgwcnv1UhFtHHiVA/1gt5wopyTZQP1UUUX/71ve13x7mfypZ8toGg34gOu2qFIyGuJt7tIgj61Qt2Euc84NEhEZz9kyEn5QeK/gNn5gZsm93QDHF40OMBRB0wVepCwXJS+MXH/v0Clsj5iyEPu1EDCR4SronQ59jqfueo0Wm5V2Tc6wgvyJVo85nCgP94gao78XMXg==',
    'result_desc' => '您提交的证照图片不清晰，无法辨别证照信息，请重新拍摄并提供清晰的证照图片（营业执照不清晰）;',
    'is_show' => 'F',
    'request_id' => '20170213112044',
    'notify_id' => 'c009530cd81267ec37df445d757e60bjh2',
    'audit_status' => 'AUDIT_FAILED',
)*/
