<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/5/16
 * Time: 18:40
 */

namespace App\Http\Controllers\Push;


use App\Merchant;
use App\Models\JpushConfig;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JPush\Client;

class JpushController
{
    public function push($type, $price, $out_trade_no)
    {
        try {
            $order = Order::where('out_trade_no', $out_trade_no)->first();
            $merchant_id = $order->merchant_id;
            $store_id = $order->store_id;
            //发个单个
            if ($store_id && $merchant_id) {
                $RegistrationId = Merchant::where('id', $merchant_id)->first()->imei;

            }
            //发给收银员
            if ($store_id && $merchant_id == "") {
                $merchant_id = DB::table('merchant_shops')->
                join('merchants', 'merchant_shops.merchant_id', 'merchants.id')->
                where('merchant_shops.store_id', $store_id)->
                whereNotNull('merchants.imei')->
                select('merchants.imei')->
                get();
                $RegistrationId = array_flatten(json_decode($merchant_id, true));
            }

            if ($RegistrationId == "") {
                return '';
            }
            $config = JpushConfig::where('id', 1)->first()->toArray();
            $client = new Client($config['DevKey'], $config['API_DevSecre']);
            $push = $client->push();
            //自定义消息
            $notify = $push->setPlatform(array('ios', 'android'))
                ->addRegistrationId($RegistrationId)
                ->message('' . $type . '到账' . $price . '元')
                ->send();
        } catch (\Exception $exception) {
            Log::info($exception);
        }
    }


    public function push_out($RegistrationId)
    {

        try {
            $config = JpushConfig::where('id', 1)->first()->toArray();
            $client = new Client($config['DevKey'], $config['API_DevSecre']);
            $push = $client->push()
                ->setPlatform(array('ios', 'android'))
                ->addRegistrationId($RegistrationId)
                ->message('login out')
                ->send();
        } catch (\Exception $exception) {
            Log::info($exception);
        }
    }
}