<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayShopLists;
use App\Models\PinganStore;
use App\Models\PufaStores;
use App\Models\UnionPayStore;
use App\Models\WeBankStore;
use App\Models\WeixinPayConfig;
use App\Models\WeixinPayNotify;
use App\Models\WeixinShopList;
use App\Models\WXNotify;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServerController extends Controller
{
    //

    public function server()
    {

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

        $server = $app->server;
        $user = $app->user;
        $server->setMessageHandler(function ($message) use ($user) {
            $open_id = $message->FromUserName;//获得发信息的open_id
            $substr = substr($message->Content, 0, 1);
            $so = "";
            try {
                if ($substr == "o") {
                    $so = AlipayAppOauthUsers::where('store_id',$message->Content)->first();
                    $store_id = $so->store_id;
                    $store_name = $so->auth_shop_name;
                }

                if ($substr === "s") {
                    $so = AlipayShopLists::where('store_id', $message->Content)->first();
                    $store_id = $so->store_id;
                    $store_name = $so->main_shop_name;
                }

                if ($substr === "w") {
                    $so = WeixinShopList::where('store_id', $message->Content)->first();
                    $store_id =  $so->store_id;
                    $store_name = $so->store_name;
                }
                if ($substr === "p") {
                    $so = PinganStore::where('external_id', $message->Content)->first();
                    $store_id = $so->external_id;
                    $store_name = $so->alias_name;
                }

                if ($substr === "u") {
                    $so = UnionPayStore::where('store_id', $message->Content)->first();
                    $store_id = $so->store_id;
                    $store_name = $so->alias_name;
                }

                if ($substr === "f") {
                    $so = PufaStores::where('store_id', $message->Content)->first();
                    $store_id = $so->store_id;
                    $store_name = $so->merchant_short_name;
                }
                if ($substr === "b") {
                    $so = WeBankStore::where('store_id', $message->Content)->first();
                    $store_id = $so->store_id;
                    $store_name = $so->store_name;
                }
                if ($substr === "m") {
                    $so = DB::table('ms_stores')->where('store_id', $message->Content)->first();
                    $store_id = $so->store_id;
                    $store_name = $so->store_short_name;
                }
                //上下班 取消
                if ($message->Content == "上班") {
                    $no = WXNotify::where('open_id', $open_id)->first();
                    if ($no) {
                        WXNotify::where('open_id', $open_id)->update(['status'=>1]);
                        return '上班了！打卡成功';
                    } else {
                        return '由于系统升级！请再次绑定店铺id 然后再发 上班 指令';
                    }
                }

                if ($message->Content == "下班") {
                    $no = WXNotify::where('open_id', $open_id)->first();
                    if ($no) {
                        WXNotify::where('open_id', $open_id)->update(['status'=>0]);
                        return '下班了！打卡成功';
                    }else{
                        return '由于系统升级！请再次绑定店铺id 然后再发 下班 指令';
                    }
                }
                if ($so) {
                    $no = WXNotify::where('open_id', $open_id)->where('store_id',$store_id)->first();
                    if (!$no){
                        //绑定收银信息
                        WXNotify::create([
                            'open_id' => $open_id,
                            'store_id' => $store_id,
                        ]);
                    }
                    $WeixinPayNotify = WeixinPayNotify::where('store_id', $store_id)->first();
                    if ($WeixinPayNotify) {
                        if ($WeixinPayNotify->receiver) {
                            $open_ids = explode(",", $WeixinPayNotify->receiver);
                            if (in_array($open_id, $open_ids)) {
                                return '你已经成功绑定' . $store_name . '收银提醒,上下班请直接发 上班 下班 指令到公众号';
                            }
                            $ids = $WeixinPayNotify->receiver . ',' . $open_id;
                            WeixinPayNotify::where('store_id', $store_id)->update([
                                'receiver' => $ids,
                                'store_id' => $store_id,
                                'store_name' => $store_name
                            ]);
                        } else {
                            WeixinPayNotify::where('store_id', $store_id)->update([
                                'receiver' => $open_id,
                                'store_id' => $store_id,
                                'store_name' => $store_name
                            ]);
                        }

                    } else {
                        WeixinPayNotify::create([
                            'receiver' => $open_id,
                            'store_id' => $store_id,
                            'store_name' => $store_name
                        ]);
                    }

                    return '你成功绑定' . $store_name . '收银提醒,上下班请直接发 上班 下班 指令到公众号';

                }
            } catch (\Exception $exception) {
                Log::info($exception);
            }
        });

        return $server->serve()->send();
    }
}
