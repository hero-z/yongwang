<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/2
 * Time: 11:10
 */

namespace App\Http\Controllers\Api;

use Alipayopen\Sdk\Request\AlipayTradeCreateRequest;
use App\Merchant;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayShopLists;
use App\Models\Order;
use App\Models\AlipayTradeQuery;
use App\Models\WeixinPayConfig;
use App\Models\WeixinPayNotify;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AlipayTradeCreateController extends BaseController
{
    /**
     * 统一收单交易创建接口 有门店
     */
    public function AlipayqrCreate(Request $request)
    {
        //0.接受参数
        $merchant_id = $request->get('m_id', '');
        $total_amount = $request->get('total_amount');
        $u_id = $request->get('u_id');
        $remark=$request->get("remark");
        $shop = AlipayShopLists::where('id', $u_id)->first();
        if ($shop) {
            $shop = $shop->toArray();
        } else {
            $shop['main_shop_name'] = "商户";
        }
        $config = AlipayIsvConfig::where('id', 1)->first();
        if ($config) {
            $config = $config->toArray();
        }
        $goods_id = "goods_" . date('YmdHis', time());
        //1.实例化公共参数
        $c = $this->AopClient();
        $c->notify_url = url('/notify');
        $c->method = "alipay.trade.create";

        $c->version = "2.0";
        //2.调用接口
        $requests = new AlipayTradeCreateRequest();
        $requests->setNotifyUrl(url('/notify'));
        $user = $request->session()->get('user_data');
        $out_trade_no = time() . rand(100, 999);
        /**
         * 如果打开下面的注释记得查看前后的标点符号
         * */
        $requests->setBizContent("{" .
            "\"out_trade_no\":" . $out_trade_no . "," .
            /*  "\"seller_id\":\"2088102169018185\"," .*/
            "\"total_amount\":" . $total_amount . "," .
            "\"subject\":\"" . $shop['main_shop_name'] . "收款" . "\"," .
            "\"body\":\"" . $shop['main_shop_name'] . "扫码收款" . "\"," .
            "\"buyer_id\":" . $user[0]->user_id . "," .
            "\"goods_detail\":[{" .
            "\"goods_id\":\"" . $goods_id . "\"," .
            "\"goods_name\":\"" . $shop['main_shop_name'] . "\"," .
            " \"quantity\":1," .
            "\"price\":" . $total_amount . "" .
            "}]," .
         //   "\"store_id\":\"" . $shop['store_id'] . "\"," .
            "\"alipay_store_id\":\"" . $shop['shop_id'] . "\"," .
            "\"extend_params\":{" .
            "\"sys_service_provider_id\":\"" . $config['pid'] . "\"" .
            /*  "\"hb_fq_num\":\"3\"," .
                 "\"hb_fq_seller_percent\":\"100\"" .*/
            "}," .
            "\"timeout_express\":\"90m\"" .
            "}");
        $result = $c->execute($requests, null, $shop['app_auth_token']);
        Log::info((array)$result);
        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            $trade_no = $result->$responseNode->trade_no;//订单号
            //保存数据库
            $merchant_name = "";
            try {
                if ($merchant_id) {
                    $merchant_name = Merchant::where('id', $merchant_id)->first()->name;//收营员名称
                }
            } catch (\Exception $exception) {
            }
            $insert = [
                'trade_no' => $trade_no,
                "out_trade_no" => $out_trade_no,
                "status" => "",
                "type" => "102",
                "merchant_id" => $merchant_id,
                "remark"=>$remark,
                "buyer_id"=>$user[0]->user_id,
                "total_amount" => $total_amount,
                'store_id' => $shop['store_id'],
            ];
            Order::create($insert);
            $data = [
                'status' => 1,
                "trade_no" => $trade_no,
                "msg" => "OK",
            ];
        } else {
            $data = [
                'status' => 0,
                "trade_no" => "",
                "msg" => "error",
            ];
        }
        return json_encode($data);

    }

    /**
     * 统一收单交易创建接口 无门店
     */
    public function AlipayOqrCreate(Request $request)
    {
        //0.接受参数
        $total_amount = $request->get('total_amount');
        $merchant_id = $request->get('m_id', '');
        $store_id = $request->get('store_id');
        $remark=$request->get("remark");
        $shop = AlipayAppOauthUsers::where('store_id', $store_id)->first();
        if ($shop) {
            $shop = $shop->toArray();
        } else {
            $shop['auth_shop_name'] = "商户";
        }
        $config = AlipayIsvConfig::where('id', 1)->first();
        if ($config) {
            $config = $config->toArray();
        }
        $goods_id = "goods_" . date('YmdHis', time());
        //1.实例化公共参数
        $c = $this->AopClient();
        $c->notify_url = url('/notify');
        $c->method = "alipay.trade.create";
        $c->version = "2.0";

        //2.调用接口
        $requests = new AlipayTradeCreateRequest();
        $requests->setNotifyUrl(url('/notify'));
        $user = $request->session()->get('user_data');
        $out_trade_no = time() . rand(100, 999);
        /**
         * 如果打开下面的注释记得查看前后的标点符号
         * */
        $requests->setBizContent("{" .
            "\"out_trade_no\":" . $out_trade_no . "," .
            /*  "\"seller_id\":\"2088102169018185\"," .*/
            "\"total_amount\":" . $total_amount . "," .
            "\"subject\":\"" . $shop['auth_shop_name'] . "收款" . "\"," .
            "\"body\":\"" . $shop['auth_shop_name'] . "扫码收款" . "\"," .
            "\"buyer_id\":" . $user[0]->user_id . "," .
            "\"goods_detail\":[{" .
            "\"goods_id\":\"" . $goods_id . "\"," .
            "\"goods_name\":\"" . $shop['auth_shop_name'] . "\"," .
            " \"quantity\":1," .
            "\"price\":" . $total_amount . "" .
            "}]," .
            "\"store_id\":\"" . 'o' . $shop['user_id'] . "\"," .
            "\"extend_params\":{" .
            "\"sys_service_provider_id\":\"" . $config['pid'] . "\"" .
            /*  "\"hb_fq_num\":\"3\"," .
                 "\"hb_fq_seller_percent\":\"100\"" .*/
            "}," .
            "\"timeout_express\":\"90m\"" .
            "}");
        $result = $c->execute($requests, null, $shop['app_auth_token']);
        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            $trade_no = $result->$responseNode->trade_no;//订单号
            //保存数据库
            $merchant_name = "";
            try {
                if ($merchant_id) {
                    $merchant_name = Merchant::where('id', $merchant_id)->first()->name;//收营员名称
                }
            } catch (\Exception $exception) {
            }

            $insert = [
                'trade_no' => $trade_no,
                "out_trade_no" => $out_trade_no,
                "status" => "",
                "type" => "101",
                "merchant_id" => $merchant_id,
                "remark"=>$remark,
                "buyer_id"=>$user[0]->user_id,
                "total_amount" => $total_amount,
                'store_id' => $shop['store_id'],
            ];
            Order::create($insert);
            $data = [
                'status' => 1,
                "trade_no" => $trade_no,
                "out_trade_no"=>$out_trade_no,
                "msg" => "OK",
            ];
        } else {
            $data = [
                'status' => 0,
                "out_trade_no"=>$out_trade_no,
                "trade_no" => "",
                "msg" => "error",
            ];
        }
        return json_encode($data);

    }

    public function OrderStatus(Request $request)
    {
        //支付同步通知
        $trade_no = $request->get('trade_no');
        $resultCode = $request->get('resultCode');
        AlipayTradeQuery::where('trade_no', $trade_no)->update(['status' => $resultCode]);
        try {
            //店铺通知微信
            if ($resultCode == 9000) {
                $AlipayTradeQuery = AlipayTradeQuery::where('trade_no', $trade_no)->first();
                $store_id = $AlipayTradeQuery->store_id;
                $WeixinPayNotifyStore = WeixinPayNotify::where('store_id', $store_id)->first();
                //实例化
                $config = WeixinPayConfig::where('id', 1)->first();
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
                $broadcast = $app->broadcast;//群发
                $userService = $app->user;
                $open_ids = $userService->lists()->data['openid'];//获得所有关注的微信openid
                /*  foreach ($open_ids as $v) {
                  $userinfo[]=$userService->get($v);

                  }*/

                $notice = $app->notice;
                $userIds = $WeixinPayNotifyStore->receiver;
                $open_ids = explode(",", $userIds);
                $templateId = $WeixinPayNotifyStore->template_id;
                $url = $WeixinPayNotifyStore->linkTo;
                $color = $WeixinPayNotifyStore->topColor;
                $data = array(
                    "keyword1" => $AlipayTradeQuery->total_amount,
                    "keyword2" => '支付宝',
                    "keyword3" => '' . $AlipayTradeQuery->updated_at . '',
                    "keyword4" => $trade_no,
                    "remark" => '查看详细信息点击这里',
                );
                foreach ($open_ids as $v) {
                    $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($v)->send();
                }

            }

        } catch (\Exception $exception) {
            Log::info($exception);
            return json_encode([
                'status' => 1,
            ]);
        }
        return json_encode([
            'status' => 1,
        ]);

    }

}