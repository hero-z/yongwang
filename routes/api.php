<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');*/

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    //user表接口
    $api->group(['namespace' => 'App\Api\Controllers'], function ($api) {
        $api->any('user/register', 'AuthController@register');
        $api->any('user/login', 'AuthController@authenticate');
    });
    //接口需要token验证
    $api->group(['namespace' => 'App\Api\Controllers', 'middleware' => 'jwt.auth'], function ($api) {
        $api->any('user/me', 'AuthController@getAuthenticatedUser');
    });
    //商户merchant端接口
    $api->group(['namespace' => 'App\Api\Controllers'], function ($api) {
        $api->any('merchant/register', 'AuthMerchantController@register');
        $api->any('merchant/login', 'AuthMerchantController@login');
        $api->any('merchant/me', 'AuthMerchantController@getAuthenticatedUser');
    });

    // 接口需要token验证 商户端接口
    $api->group(['namespace' => 'App\Api\Controllers\Merchant'/*, 'middleware' => 'auth:merchantApi'*/], function ($api) {
        $api->any('MerchantOrder', 'MerchantOrderController@index');
        $api->any('MerchantQueryDay', 'MerchantOrderController@queryCurdayPOSorder');
        $api->any('MerchantRefund', 'MerchantOrderController@refund');
    });


    // 接口需要token验证 商户端接口
    $api->group(['namespace' => 'App\Api\Controllers\Merchant', 'prefix' => 'merchant','middleware' => [/*'jwt.auth', 'jwt.refresh','auth:merchantApi'*/'merchant.api']], function ($api) {
        $api->any('MOrder', 'MerchantController@MOrder');
        $api->any('TradePay', 'MerchantController@TradePay')->name('TradePayApi');
        $api->any('MoneyPay', 'MerchantController@MoneyPay');
        $api->any('TradeQuery', 'MerchantController@TradeQuery');
        $api->any('Order', 'MerchantController@Order');
        $api->any('Questions', 'QuestionController@getQuestions');
        $api->any('add', 'InsertController@addMerchant');
        $api->any('store', 'SelectController@selectStore');
        $api->any('pay_qr_url', 'SelectController@selectPayQr');
        $api->any('storeList', 'MerchantController@StoreList');
        $api->any('getCashier', 'MerchantController@getCashier');
        $api->any('getTotalAmount', 'MerchantController@getTotalAmount');
        $api->any('getMenu', 'MerchantController@getMenu');
        $api->any('getMachine', 'MerchantController@getMachine');
        $api->any('addMachine', 'MerchantController@addMachine');
        $api->any('delMachine', 'MerchantController@delMachine');
        $api->any('getMachineCfg', 'MerchantController@getMachineCfg');
        $api->any('setMachineCfg', 'MerchantController@setMachineCfg');
        $api->any('getOrderDetail', 'MerchantController@getOrderDetail');
        //查询收银员
        $api->any('getStoreMerchantInfo', 'SelectController@getStoreMerchantInfo');
        //退款接口
        $api->any('payRefund', 'RefundController@payRefund');
        //花呗分期
        $api->any('AlipayHbfq', 'HbfqController@AlipayHbfq');

        //清楚设备名称
        $api->any('outimei', 'SelectController@outimei');


    });

    $api->group(['namespace' => 'App\Api\Controllers\Merchant', 'prefix' => 'merchant'], function ($api) {
        $api->any('appUpdate', 'SelectController@appUpdate');
        //意锐
        $api->any('in', 'InspiryController@in');
        $api->any('inUnified', 'InspiryController@inUnified');

    });
    // 短信接口
    $api->group(['namespace' => 'App\Api\Controllers\Sms', 'prefix' => 'Sms'], function ($api) {
        $api->any('send', 'SmsController@send');
    });
    // test
    $api->group(['namespace' => 'App\Api\Controllers\Test', 'prefix' => 'test'], function ($api) {
        $api->any('test', 'TestController@hello');
    });

//新大陆接口
    // 接口需要token验证 商户端接口
    $api->group(['namespace' => 'App\Api\Controllers\Merchant', 'prefix' => 'merchant','middleware' => [/*'jwt.auth', 'jwt.refresh','auth:merchantApi'*/'merchant.api']], function ($api) {
        $api->any('newlandCreateOrder', 'NewLandController@newlandCreateOrder');
    });
    //翼支付意锐
    $api->group(['namespace' => 'App\Api\Controllers\Merchant', 'prefix' => 'bestpay'], function ($api) {
        //意锐
        $api->any('test', 'BestPayController@test');
        $api->any('init', 'BestPayController@init');
        $api->any('inUnified', 'BestPayController@inUnified');
    });
});
