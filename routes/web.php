<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|


*/
//前台
Route::get('/', 'IndexController@index');
Route::get('/logout', 'Auth\LoginController@logout');
Auth::routes();

//需要登陆
Route::group(['middleware' => 'auth'], function () {
    Route::post('/updateInfo', 'AppController@updateInfo')->name("updateInfo");
    Route::post('/appUpdateFile', 'AppController@appUpdateFile')->name("appUpdateFile");
    Route::get('/setApp', 'AppController@setApp')->name("setApp");
    Route::post('/setAppPost', 'AppController@setAppPost')->name("setAppPost");
});


//要登录的链接
Route::group(['namespace' => 'AlipayOpen', 'middleware' => 'auth', 'prefix' => 'admin/alipayopen'], function () {
    Route::resource('/store', 'StoreController');
    //网站logo设置
    Route::get("logoIndex","logoController@logoIndex")->name("logoIndex");
    Route::post("setLogo","logoController@setLogo")->name("setLogo");
    //admin 管理用户模块
    Route::get('/users', 'UsersController@users')->name('users');
    Route::get('/ajaxusers', 'UsersController@ajaxusers')->name('ajaxusers');
    Route::post('/ajaxpasswd', 'UsersController@ajaxpasswd')->name('ajaxpasswd');
    Route::post('/edituser', 'UsersController@edituser')->name('edituser');
    Route::get('/setrate', 'UsersController@setrate')->name('setrate');
    Route::post('/dosetrate', 'UsersController@dosetrate')->name('dosetrate');
    Route::post('/doedituser', 'UsersController@doedituser')->name('doedituser');
    Route::get("/changeShopOwner", "shopOwnerController@changeShopOwner")->name("changeShopOwner");
    Route::post("/changeTo", "shopOwnerController@changeTo")->name("changeTo");
    Route::post("/changeOwner", "shopOwnerController@changeOwner")->name("changeOwner");
    Route::get('/qrp', 'UsersController@qrp');
    Route::get('/updateu', 'UsersController@updateu')->name('updateu');
    Route::post('/useradd', 'UsersController@useradd')->name('useradd');
    Route::post('/updateuSave', 'UsersController@updateuSave')->name('updateuSave');
    Route::post('/deleteu', 'UsersController@deleteu')->name('deleteu');
    Route::any('/deluserlist', 'UsersController@deluserlist')->name('deluserlist');
    Route::post('/dropuser', 'UsersController@dropuser')->name('dropuser');
    Route::post('/userback', 'UsersController@userback')->name('userback');
    //后台配置模块
    Route::get('/isvconfig', 'AlipayIsvConfigController@isvconfig')->name('isvconfig');
    Route::post('/saveconfig', 'AlipayIsvConfigController@saveconfig')->name('saveconfig');
    //商户业务流水操作查询
    Route::get('/ApplyorderBatchquery', 'ApplyorderBatchqueryController@query')->name('ApplyorderBatchquery');
    Route::get('/alipaytradelist', 'AlipayTradeListController@index')->name('alipaytradelist');
    //授权列表
    Route::any('/oauthlist', 'OauthController@oauthlist')->name('oauthlist');
    Route::get('/updateOauthUser', 'OauthController@updateOauthUser')->name('updateOauthUser');
    Route::post('/updateOauthUserPost', 'OauthController@updateOauthUserPost')->name('updateOauthUserPost');
    Route::get("deleteOauth", "OauthController@deleteOauth")->name("deleteOauth");
    //各种提醒设置
    Route::post('/shopNotify', 'AlipayReturnController@shopNotify')->name('shopNotify');
    Route::get('/setWxNotify', 'AlipayReturnController@setWxNotify')->name('setWxNotify');
    Route::post('/setWxNotifyPost', 'AlipayReturnController@setWxNotifyPost')->name('setWxNotifyPost');


    //权限管理
    Route::resource('/role', 'RoleController');
    Route::resource('/permission', 'PermissionController');
    Route::get('/assignment', 'RolePermissionController@assignment')->name('assignment');
    Route::post('/assignmentpost', 'RolePermissionController@assignmentpost')->name('assignmentpost');
    Route::post('/delRole', 'RolePermissionController@delRole')->name('delRole');
    Route::get('/setRole', 'RolePermissionController@setRole')->name('setRole');
    Route::post('/setRolePost', 'RolePermissionController@setRolePost')->name('setRolePost');

    //数据统计
    Route::any('/datacount', 'DatacountController@datalist')->name('datalist');
    Route::post('getpostdatadp', 'DatacountController@datadp')->name('getpostdatadp');
    Route::post('/getpostdataadminPaylist', 'DatacountController@dataPaylist')->name('getpostdataadminPaylist');

    Route::any('/neworderlist', 'NewOrderManageController@datalist')->name('neworderlist');
    Route::post('getdplist', 'NewOrderManageController@datadp')->name('getdplist');
    Route::post('/getadminPaylist', 'NewOrderManageController@dataPaylist')->name('getadminPaylist');
    Route::post('/gettotalamount', 'NewOrderManageController@gettotalamount')->name('gettotalamount');
    Route::any('/userprofit', 'UserProfitManageController@userprofit')->name('userprofit');
    Route::any('/profitsplit', 'UserProfitManageController@profitsplit')->name('profitsplit');
    //导出数据(Excel)
    Route::get('/exportexceldata', 'DatacountController@expexceldata')->name('adminexpexceldata');
    Route::any('/orderexportdata', 'NewOrderManageController@expexceldata')->name('orderexportdata');
    //商户统一管理
    Route::get('/merchantmanagement', 'MerchantmanagementController@mmdatalists')->name('mmdatalists');
    Route::get('/merchantshoplist', 'MerchantmanagementController@mmshoplists')->name('mmshoplists');
    Route::get('/merchantshopbind', 'MerchantmanagementController@mmshopbind')->name('mmshopbind');
    Route::post('/merchantpostdata', 'MerchantmanagementController@mmpostdata')->name('mmpostdata');
    Route::post('/merchantshopdelpost', 'MerchantmanagementController@mmshopdelpost')->name('mmshopdelpost');
    Route::get("/editMerchantNames", "MerchantmanagementController@editMerchantNames")->name("editMerchantNames");
    Route::post("/updateMerchantNames", "MerchantmanagementController@updateMerchantNames")->name("updateMerchantNames");
    Route::post("/searchMerchant", "MerchantmanagementController@searchMerchant")->name("searchMerchant");
    //口碑开店收款操作
    Route::get('/oauthRestore', 'OauthController@oauthRestore')->name('oauthRestore');
    Route::get('/changeStatus', 'OauthController@changeStatus')->name('changeStatus');
    Route::any('/oauthSearch', 'OauthController@oauthSearch')->name('oauthSearch');
    Route::post('/restore', 'OauthController@restore')->name('restore');
    Route::get('/restoree', 'OauthController@restoree')->name('restoree');
    Route::post('/restoreSearch', 'OauthController@restoreSearch')->name('restoreSearch');
    //口碑门店列表操作
    Route::post('/storeRestoreSearch', 'StoreController@storeRestoreSearch')->name('storeRestoreSearch');
    Route::post('/storeSearch', 'StoreController@storeSearch')->name('storeSearch');
    Route::get('/storeChangeStatus', 'StoreController@storeChangeStatus')->name('storeChangeStatus');
    Route::get('/restoreIndex', 'StoreController@restoreIndex')->name('restoreIndex');
    Route::post('/storeRestore', 'StoreController@storeRestore')->name('storeRestore');
    Route::get('/storeRestoree', 'StoreController@storeRestoree')->name('storeRestoree');
    Route::get("/editshoplists", "StoreController@editshoplists")->name("editshoplists");
    Route::post("/updateshoplists", "StoreController@updateshoplists")->name("updateshoplists");
    Route::post('/delShop', "StoreController@delShop")->name("delShop");
    Route::get("/addOldShop", "StoreController@addOldShop")->name("addOldShop");
    Route::post("/insertOldShop", "StoreController@insertOldShop")->name("insertOldShop");


    //支付宝分店
    Route::get("/AlipayBranchIndex", "AlipayBranchController@AlipayBranchIndex");
    Route::get("/AlipayBranchAdd", "AlipayBranchController@AlipayBranchAdd");
    Route::get("/addOldBranchIndex","addOldBranchController@index")->name("addOldBranchIndex");
    Route::post("/addOldBranch","addOldBranchController@add")->name('addOldBranch');
    Route::post("/AlipayBranchAddPost", "AlipayBranchController@AlipayBranchAddPost");
    //口碑分店列表
    Route::get("salipayBranchIndex","SalipayBranchController@salipayBranchIndex")->name("salipayBranchIndex");
    Route::get("addSalipayBranch","SalipayBranchController@addSalipayBranch")->name("addSalipayBranch");
    Route::post("createSalipayBranch","SalipayBranchController@createSalipayBranch")->name("createSalipayBranch");
    //收银员列表
    Route::get("/CashierIndex", "CashierController@CashierIndex");
    Route::get("/CashierAdd", "CashierController@CashierAdd");
    Route::post("/CashierAddPost", "CashierController@CashierAddPost")->name('CashierAddPost');
    Route::get("/CashierBind", "CashierController@CashierBind");
    Route::post("/CashierBindPost", "CashierController@CashierBindPost")->name('CashierBindPost');
    Route::get("/pinganCashierQr", "CashierController@pinganCashierQr")->name('pinganCashierQr');
    //绑定收银员
    Route::get("/bindCashierIndex", "bindCashierController@bindCashierIndex")->name("bindCashierIndex");
    Route::post("/bindCashier", "bindCashierController@bindCashier")->name("bindCashier");
});
//授权后跳转注册
Route::group(['namespace' => 'AlipayOpen','prefix' => 'auto/alipayopen'], function () {
    Route::resource('/store', 'StoreAutoController');
});
Route::group(['namespace' => 'AlipayOpen'], function () {
    Route::get('/callback', 'OauthController@callback');
    Route::get('alipayopen/userinfo', 'OauthController@userinfo');
    Route::post('alipayopen/userinfoinsert', 'OauthController@userinfoinsert')->name('userinfo');
});

//支付宝通知页面
Route::group(['namespace' => 'AlipayOpen'], function () {
    Route::any('/notify', 'NotifyController@notify')->name('notify');
    Route::any('/notify_m', 'NotifyController@notify_m')->name('notify_m');
    Route::any('/operate_notify_url', 'NotifyController@operate_notify_url')->name('operate_notify_url');
    Route::any('/alipay_notify', 'NotifyController@alipay_notify')->name('alipay_notify');
});

Route::group(['namespace' => 'AlipayOpen', 'prefix' => 'admin/alipayopen'], function () {
    Route::get('/oauth', 'OauthController@oauth');
    Route::get('/auth', 'OauthController@auth');
    Route::get('/alipay_trade_precreate', 'AlipayTradePrecreateController@TradePrecreateQrCode')->name('alipay_trade_precreate');
    //输入金额页面
    Route::get('/alipay_trade_create', 'AlipayCreateOrderController@alipay_trade_create')->name('alipay_trade_create');
    //仅收款输入金额页
    Route::get('/alipay_oqr_create', 'AlipayCreateOrderController@alipay_oqr_create')->name('alipay_oqr_create');

    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@home')->name('home');
    //单页面
    Route::get('/PaySuccess', 'AlipayPageController@PaySuccess')->name('PaySuccess');
    Route::get('/OrderErrors', 'AlipayPageController@OrderErrors')->name('OrderErrors');
    //创建订单确认金额视图路由
    Route::get('/create', 'AlipayOrderController@create')->name('create');
    //收款码
    Route::get('/skm', 'AlipayQrController@Skm')->name('skm');
    Route::get('/onlyskm', 'AlipayQrController@OnlySkm')->name('onlyskm');

    //员工推广界面提交
    Route::get('/selfserviceadd', 'SelfServiceShosController@selfserviceadd')->name('selfserviceadd');
    Route::post('/selfshoppost', 'SelfServiceShosController@SelfShopPost')->name('SelfShopPost');
});
//API
Route::group(['namespace' => 'Api'/*'middleware' => 'auth'*/, 'prefix' => 'admin/api'], function () {
    //收款码接口
    Route::post('/AlipayTradeCreate', 'AlipayTradeCreateController@AlipayTradeCreate')->name("AlipayTradeCreate");
    Route::post('/AlipayOqrCreate', 'AlipayTradeCreateController@AlipayOqrCreate')->name("AlipayOqrCreate");
    Route::post('/AlipayqrCreate', 'AlipayTradeCreateController@AlipayqrCreate')->name("AlipayqrCreate");
    Route::any('/getProvince', 'ProvinceCityController@getProvince')->name("getProvince");
    Route::any('/getCity', 'ProvinceCityController@getCity')->name("getCity");
    Route::any('/getCategory', 'AlipayShopCategoryController@getCategory')->name("getCategory");
    Route::any('/getNewCategory', 'AlipayShopCategoryController@getNewCategory')->name("getNewCategory");
    Route::any('/OrderStatus', 'AlipayTradeCreateController@OrderStatus')->name("OrderStatus");
    Route::any('/uploadImagePingAn', 'PublicController@uploadImagePingAn')->name("uploadImagePingAn");//上传至服务器
    Route::any('/uploadImageUnionPay', 'PublicController@uploadImageUnionPay')->name("uploadImageUnionPay");//上传至服务器
    Route::any('/send', 'SmsController@send');
    Route::get("/setSms", "SmsController@setSms")->name("setSms");
    Route::post("/updateSms", "SmsController@updateSms")->name("updateSms");
    Route::any("/testapi", "TestController@testapi");
});
//API  AUTH
Route::group(['namespace' => 'Api', 'middleware' => 'auth', 'prefix' => 'admin/api'], function () {
    Route::any('/QueryStatus', 'AlipayTradeQueryController@QueryStatus')->name("QueryStatus");
    Route::any('/AlipayShopCategory', 'AlipayShopCategoryController@query')->name("AlipayShopCategory");
    /*Route::any('/getProvince', 'ProvinceCityController@getProvince')->name("getProvince");
    Route::any('/getCity', 'ProvinceCityController@getCity')->name("getCity");*/
    Route::any('/upload', 'PublicController@upload')->name("upload");
    Route::any('/uploadlocal', 'PublicController@uploadlocal')->name("uploadlocal");
    Route::any('/uploads', 'PublicController@uploads')->name("uploads");
    Route::any('/uploadfile', 'PublicController@uploadfile')->name("uploadfile");
    Route::any('/SummaryBatchquery', 'AlipayQueryController@index')->name("ShopSummaryBatchquery");
    Route::any('/batchquery', 'AlipayQueryController@batchquery')->name("batchquery");
    Route::any('/ApplyOrderBatchQuery', 'AlipayQueryController@ApplyOrderBatchQuery')->name("ApplyOrderBatchQuery");
    Route::any('/ShopQueryDetail', 'AlipayQueryController@ShopQueryDetail')->name("ShopQueryDetail");
    Route::any('/ssl', 'OpenSSLController@create')->name("ssl");
    //app更新包路径
    Route::any("/updateUrl","PublicController@updateUrl")->name("updateUrl");

});
//微信
Route::group(['namespace' => 'Weixin', 'prefix' => 'admin/weixin'], function () {
    Route::any('/server', 'ServerController@server');
    Route::any('/oauth', 'OauthController@oauth');
    Route::any('/oauth_callback', 'OauthController@oauth_callback');
    Route::any('/orderview', 'WeixinPayController@orderview');
    Route::any('/order', 'WeixinPayController@order')->name('order');
    Route::any('/createorder', 'WeixinPayController@createOrder');
    Route::any('/ordernotify', 'WeixinPayController@ordernotify');
    Route::get('/paySuccess', 'WeixinPayController@paySuccess')->name('WeiXinPaySuccess');


});
//需要登陆
Route::group(['namespace' => 'Weixin', 'middleware' => 'auth', 'prefix' => 'admin/weixin'], function () {
    //服务商设置
    Route::get('/spset', 'ServiceProviderController@spset')->name("spset");
    Route::post('/spsetPost', 'ServiceProviderController@spsetPost')->name("spsetPost");
    //商户添加
    Route::get('/shopList', 'ShopsListsController@index')->name("WxShopList");
    Route::get('/WxAddShop', 'ShopsListsController@WxAddShop')->name("WxAddShop");
    Route::post('/WxShopPost', 'ShopsListsController@WxShopPost')->name("WxShopPost");
    Route::get('/WxEditShop', 'ShopsListsController@WxEditShop')->name("WxEditShop");
    Route::post('/WxEditShopPost', 'ShopsListsController@WxEditShopPost')->name("WxEditShopPost");
    Route::get('/WxPayQr', 'ShopsListsController@WxPayQr')->name("WxPayQr");
    Route::get('/WxOrder', 'ShopsListsController@WxOrder')->name("WxOrder");
    //微信商户删除搜索还原
    Route::get('/wxChangeStatus', "ShopsListsController@wxChangeStatus")->name("wxChangeStatus");
    Route::get('/wxRestore', "ShopsListsController@wxRestore")->name("wxRestore");
    Route::post('/wxRestoree', "ShopsListsController@wxRestoree")->name("wxRestoree");
    Route::get('/wxRestoreee', "ShopsListsController@wxRestoreee")->name("wxRestoreee");
    Route::get("deleteWx", "ShopsListsController@deleteWx")->name("deleteWx");
    Route::post("searchWx", "ShopsListsController@searchWx")->name("searchWx");
    Route::post("searchW", "ShopsListsController@searchW")->name("searchW");
    //微信的分店
    Route::get("/deleteBw", "WxBranchController@deleteBw")->name("deleteBw");
    Route::get("/BwRestore", "WxBranchController@BwRestore")->name("BwRestore");
    Route::get("/BranchIndex", "WxBranchController@BranchIndex");
    Route::get("/BranchAdd", "WxBranchController@BranchAdd");
    Route::post("/BranchAddPost", "WxBranchController@BranchAddPost")->name('WXBranchAddPost');

});
//设置
Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
    Route::get('/set', 'PageSetsController@setPage');
    Route::post('/setPagePost', 'PageSetsController@setPagePost')->name('setPagePost');
});
//支付宝微信 二码合一需要登陆
Route::group(['namespace' => 'AlipayWeixin', 'middleware' => 'auth', 'prefix' => 'admin/alipayweixin'], function () {
    //服务商设置
    Route::get('/AlipayWexinLists', 'AlipayWeixinController@AlipayWexinLists')->name("AlipayWexinLists");
    Route::get('/addAliPayWeixinStore', 'AlipayWeixinController@addAliPayWeixinStore')->name("addAliPayWeixinStore");
    Route::post('/addAliPayWeixinStorePost', 'AlipayWeixinController@addAliPayWeixinStorePost')->name("addAliPayWeixinStorePost");
    Route::post('/delAlipayWexin', 'AlipayWeixinController@delAlipayWexin')->name("delAlipayWexin");
    Route::get('/qr', 'AlipayWeixinController@qr');
    Route::post('/addTwo', 'AlipayWeixinController@addTwo')->name("addTwo");
    Route::get("/editAddTwo", 'AlipayWeixinController@editAddTwo')->name("editAddTwo");
    Route::get("deleteAddTwo", "AliapyWeixinController@deleteAddTwo")->name("deleteAddTwo");
    Route::post("/updateAddTwo", 'AlipayWeixinController@updateAddTwo')->name("updateAddTwo");
    Route::post("/xuanzhong", "AlipayWeixinController@xuanzhong")->name("xuanzhong");
    Route::post("/xuanzhonge", "AlipayWeixinController@xuanzhonge")->name("xuanzhonge");
    Route::get('/qrCode', 'AwQrController@qrCode');
    Route::get("otherUrl",'otherUrlController@index')->name("otherUrl");
});
//支付宝微信
Route::group(['namespace' => 'AlipayWeixin', 'prefix' => 'admin/alipayweixin'], function () {
    Route::get('/pay', 'AwQrController@pay');

});
//平安银行 需要登录
Route::group(['namespace' => 'PingAn', 'middleware' => 'auth', 'prefix' => 'admin/pingan'], function () {
    Route::get('/index', 'StoreController@index')->name('PingAnStoreIndex');
    Route::get('/add', 'StoreController@add')->name('PingAnStoreAdd');
    Route::post('/addPost', 'StoreController@addpost')->name('PingAnStoreAddPost');
    Route::post('/DelPinanStore', 'StoreController@DelPinanStore')->name('DelPinanStore');
    Route::post('/PayStatus', 'StoreController@PayStatus')->name('PayStatus');
    Route::get('/SetStore', 'StoreController@SetStore')->name('SetStore');
    Route::post('/SetStorePost', 'StoreController@SetStorePost')->name('SetStorePost');
    Route::get('/setMerchantRate', 'StoreController@setMerchantRate')->name('setMerchantRate');
    Route::post('/setMerchantRatePost', 'StoreController@setMerchantRatePost')->name('setMerchantRatePost');
    Route::get('/PingAnStoreQR', 'StoreController@PingAnStoreQR')->name('PingAnStoreQR');
    Route::get('/OrderQuery', 'StoreController@OrderQuery')->name('PingAnOrderQuery');
    Route::get('/downloadbill', 'DownloadBillController@downloadbill')->name('pingandownloadbill');
    Route::post('/downloadbillpost', 'DownloadBillController@downloadbillpost')->name('pingandownloadbillpost');
    Route::get('/pinganquerybill', 'DownloadBillController@pinganquerybill')->name('pinganquerybill');
    Route::post('/pinganquerybillpost', 'DownloadBillController@pinganquerybillpost')->name('pinganquerybillpost');
    //空码生成
    Route::get('/QrLists', 'PinganQrController@QrLists')->name('QrLists');
    Route::post('/createQr', 'PinganQrController@createQr')->name('createQr');
    Route::any('/DownloadQr', 'PinganQrController@DownloadQr')->name('DownloadQr');

    //二维码跳转地址
    Route::any('/Qrcode', 'PinganQrController@Qrcode')->name('Qrcode');

    //通道配置模块
    Route::get('/pinganconfig', 'PingAnConfigController@pinganconfig')->name('pinganconfig');
    Route::post('/savepinganconfig', 'PingAnConfigController@savepinganconfig')->name('savepinganconfig');


    //支付宝 微信
    Route::get('/alipay', 'AlipayController@alipay');


    //商户资料
    Route::get('/MerchantFile', 'StoreController@MerchantFile')->name('MerchantFile');
    //平安银行商户列表
    Route::post('/pinganSearch', 'StoreController@pinganSearch')->name('pinganSearch');
    Route::get('/pinganRestore', 'StoreController@pinganRestore')->name('pinganRestore');
    Route::post('/pinganRestoreSearch', 'StoreController@pinganRestoreSearch')->name('pinganRestoreSearch');
    Route::post('/pinganRestoree', 'StoreController@pinganRestoree')->name('pinganRestoree');
    Route::get('/pinganRestoreee', 'StoreController@pinganRestoreee')->name('pinganRestoreee');
    Route::post("/pinganDelete", "StoreController@pinganDelete")->name("pinganDelete");
    Route::get("editPingan", "StoreController@editPingan")->name("editePingan");
    Route::post("upPingan", "StoreController@upPingan")->name("upPingan");
    //查询费率
    Route::post("PingAnRate", "StoreController@PingAnRate")->name("PingAnRate");

    //平安的分店
    Route::get("/BranchIndex", "PinanBranchController@BranchIndex");
    Route::get("/BranchAdd", "PinanBranchController@BranchAdd");
    Route::post("/BranchAddPost", "PinanBranchController@BranchAddPost")->name('BranchAddPost');

    //微信一户一码
    Route::post('/getbusiness','WxSubMerchantController@getBusiness');
    Route::post('/getsubappid','WxSubMerchantController@getSubAppid');
    Route::post('/createsubmerchant','WxSubMerchantController@createSubMerchant');
    Route::post('/submerchantset','WxSubMerchantController@SubMerchantSet');
    //平安见证宝管理
    Route::get('/witnessinfo','WitNessInfoController@WitNessInfo');
    Route::post('/witness/querywitness',"WitNessInfoController@QueryWitNess");
    Route::post('/witness/withdraw',"WitNessInfoController@WithDraw");
    Route::post('/witness/resetCard', 'StoreController@witnessAutomPost')->name('witnessResetCard');
});

//平安银行 不要登录可访问
Route::group(['namespace' => 'PingAn', 'prefix' => 'admin/pingan'], function () {
    //支付宝 微信
    Route::get('/alipay', 'AlipayController@alipay');
    Route::post('/PingAnAlipay', 'AlipayController@PingAnAlipay')->name('PingAnAlipay');
    Route::get('/ReturnStatus', 'AlipayController@ReturnStatus')->name('ReturnStatus');
    Route::get('/PaySuccess', 'AlipayController@PaySuccess');
    Route::get('/weixin/orderview', 'WeiXinController@orderview');
    Route::get('/weixin/orderStatus', 'WeiXinController@OrderStatus');
    Route::post('/PAWxOrder', 'WeiXinController@PAWxOrder')->name('PAWxOrder');
    Route::any('/notify_url', 'NotifyController@notify_url');
    Route::any('/wx_notify_url', 'NotifyController@wx_notify_url');
    Route::any('/notify_url_m', 'NotifyController@notify_url_m');
    Route::any('/wx_notify_url_m', 'NotifyController@wx_notify_url_m');
    //翼支付
    Route::get('/pay_view', 'BestPayController@pay_view')->name('BestPay_view');
    Route::post('/BestPayPost', 'BestPayController@BestPayPost')->name('BestPayPost');
    Route::any('/best_notify_url', 'NotifyController@best_notify_url')->name('best_notify_url');
    Route::any("/acceptStatu", "BestPayController@acceptStatu")->name("acceptStatu");
    //京东
    Route::get('/jdpay_view', 'JdController@jdpay_view')->name('jdPay_view');
    Route::post('/jdpost', 'JdController@jdpost')->name('jdpost');
    Route::any('/jd_notify_url', 'NotifyController@jd_notify_url')->name('jd_notify_url');
    Route::any('/jd_url', 'JdController@acceptStatus')->name('jd_url');
    //见证宝
    Route::post("/witness/getcity", "WitnessController@getCity");
    Route::post("/witness/getopenbank", "WitnessController@getOpenBank");
});

//链接
Route::group(['namespace' => 'PingAn'], function () {
    Route::get('/Qrcode', 'PinganQrController@QrCode');
});
//城市级联
Route::group(['namespace' => 'PingAn'], function () {
    Route::post('/getcitycountydata', 'StoreController@getcitycountydata')->name('getcitycountydata');
});

//商户自助提交
Route::group(['namespace' => 'PingAn', 'prefix' => 'admin/pingan', 'middleware' => 'auth.merchant'], function () {
    Route::get('/autoStore', 'StoreController@autoStore')->name('autoStore');
    Route::post('/autoStorePost', 'StoreController@autoStorePost')->name('autoStorePost');
    Route::get('/success', 'StoreController@success')->name('PingAnSuccess');
    Route::get('/autom', 'StoreController@autom')->name('autom');
    Route::post('/automPost', 'StoreController@automPost')->name('automPost');
    Route::get('/autoFile', 'StoreController@autoFile')->name('autoFile');
    Route::post('/autoFilePost', 'StoreController@autoFilePost')->name('autoFilePost');

	//见证宝
    Route::get("/witness/account/create", "WitnessController@create");
    Route::get("/witness/bind/bank", "WitnessController@bindBank");
    Route::get("/witness/verify", "WitnessController@verify");
    Route::post('/witness/automPost', 'StoreController@witnessAutomPost')->name('witnessAutomPost');
    //见证宝鉴权
    Route::post("/witness/verifymessage", "WitnessController@verifyMessage");
    Route::post("/witness/verifymoney", "WitnessController@verifyMoney");

});
//商户
Route::group(['namespace' => 'Merchant', 'prefix' => 'merchant', 'middleware' => 'auth.merchant'], function () {
    Route::any('logout', 'LoginController@logout');
    Route::get('index', 'IndexController@index');
    Route::get('weixin', 'WeixinLsController@weixinls')->name('weixinLs');
    Route::get('pingan', 'PinganLsController@pinganls')->name('pinganLs');
    Route::get('list', 'PufaController@list')->name('pufaorderlist');
    Route::any('orderlists', 'OrderManageController@orderls')->name('orderlistssearch');
    Route::any('mobileOrderlists', 'mobileOrderController@orderls')->name('mobileOrderlistssearch');
    Route::any('neworderlists', 'NewOrderManageController@orderls')->name('neworderlists');
    Route::any('orderdetail', 'mobileOrderController@orderdetail')->name('orderdetail');
    Route::any('mdStoreList', 'mobileOrderController@mdStoreList')->name('mdStoreList');
    Route::any('mdGetCashier', 'mobileOrderController@mdGetCashier')->name('mdGetCashier');
    Route::any('mdPayCode', 'mobileOrderController@mdPayCode')->name('mdPayCode');
    Route::any('mdMyInfo', 'mobileOrderController@mdMyInfo')->name('mdMyInfo');
    Route::any('mdMyInfoDetail', 'mobileOrderController@mdMyInfoDetail')->name('mdMyInfoDetail');
    Route::any('mdMchMachine', 'mobileOrderController@mdMchMachine')->name('mdMchMachine');
    Route::any('mdMchMachineAdd', 'mobileOrderController@mdMchMachineAdd')->name('mdMchMachineAdd');
    Route::any('mdMchMachineEdit', 'mobileOrderController@mdMchMachineEdit')->name('mdMchMachineEdit');
    Route::any('mdMchMachineCfg', 'mobileOrderController@mdMchMachineCfg')->name('mdMchMachineCfg');
    Route::any('mdMchMachineSetCfg', 'mobileOrderController@mdMchMachineSetCfg')->name('mdMchMachineSetCfg');
    Route::any('mdMchTx', 'mobileOrderController@mdMchTx')->name('mdMchTx');
    Route::any('mdMchMyCard', 'mobileOrderController@mdMchMyCard')->name('mdMchMyCard');
    Route::any('mdMchMyCardAdd', 'mobileOrderController@mdMchMyCardAdd')->name('mdMchMyCardAdd');
    Route::any('mdMchMyCardSelect', 'mobileOrderController@mdMchMyCardSelect')->name('mdMchMyCardSelect');
    Route::any('mdMchOrder', 'mobileOrderController@mdMchOrder')->name('mdMchOrder');
    Route::any('newMobileOrderlists', 'NewMobileOrderController@orderls')->name('newMobileOrderlists');
    Route::get("alipayls", "AliPayLsController@alipayls")->name('alipayLs');
    Route::any("mobile", "mobileOrderController@orderls")->name("mobile");
    Route::get("alipaysls", "AliPayLsController@alipaysls")->name("alipaysLs");
    Route::get("/AlipayTradePayCreate", "TradePayController@AlipayTradePayCreate")->name('AlipayTradePayCreate');
    Route::post("/TradePayCodeType", "TradePayController@TradePayCodeType")->name('TradePayCodeType');
    Route::post("/TradePayQuery", "TradePayController@TradePayQuery")->name('TradePayQuery');
    Route::get("/setWays", "PayWaysController@setWays")->name('setWays');
    Route::post("/setWaysPost", "PayWaysController@setWaysPost")->name('setWaysPost');
    Route::post("/WxOrderStatus", "TradePayController@WxOrderStatus")->name('WxOrderStatus');
    Route::post("/WxPOrderStatus", "TradePayController@WxPOrderStatus")->name('WxPOrderStatus');
    Route::get("/scanLs", "scanLsController@scanLs")->name("scanLs");
    Route::get("/editMerchant", "IndexController@editMerchant")->name("editMerchant");
    Route::post("/updateMerchant", "IndexController@updateMerchant")->name("updateMerchant");
    //导出数据(Excel)
    Route::get('/exportexceldata', 'OrderManageController@expexceldata')->name('expexceldata');
    Route::post('/getpostdatamerchantCashier', 'OrderManageController@dataCashier')->name('getpostdatamerchantCashier');
    Route::post('/getpostdatamerchantPaylist', 'OrderManageController@dataPaylist')->name('getpostdatamerchantPaylist');

    Route::post('/gettotalamount', 'NewOrderManageController@gettotalamount')->name('merchantgettotalamount');
    Route::get('/newexportdata', 'NewOrderManageController@expexceldata')->name('newexportdata');
    Route::post('/newgetmerchantCashier', 'NewOrderManageController@dataCashier')->name('newgetmerchantCashier');
    Route::post('/newgetmerchantPaylist', 'NewOrderManageController@dataPaylist')->name('newgetmerchantPaylist');
    //公众号
    Route::get('PingAnQr', 'PingAnController@PingAnQr');
    Route::get('PingAnOrderList', 'PingAnController@PingAnOrderList');

    //平安公众号注册
    Route::get('/autoStore', 'PingAnStoreController@autoStore')->name('PAautoStore');
    Route::post('/autoStorePost', 'PingAnStoreController@autoStorePost')->name('PAautoStorePost');
    Route::get('/autom', 'PingAnStoreController@autom')->name('PAautom');
    Route::post('/automPost', 'PingAnStoreController@automPost')->name('PAautomPost');
    Route::get('/autoFile', 'PingAnStoreController@autoFile')->name('PAautoFile');
    Route::post('/autoFilePost', 'PingAnStoreController@autoFilePost')->name('PAautoFilePost');
   //生成自动金额公告页面
    Route::get('/FixedView', 'FixedAmountController@FixedView')->name('FixedView');
    Route::get('/UnionPayFixed', 'FixedAmountController@UnionPayFixed')->name('UnionPayFixed');
    Route::get('/choosePayWay', 'FixedAmountController@choosePayWay')->name('choosePayWay');
    Route::get('/allPayFixed', 'FixedAmountController@allPayFixed')->name('allPayFixed');
    Route::post('/getcodeurl', 'FixedAmountController@getcodeurl')->name('getcodeurl');

    //商户端收银员管理
    Route::get('/cashierindex', 'CashierManageController@index')->name('cashierindex');
    Route::post('/cashierdel', 'CashierManageController@del')->name('cashierdel');
    Route::get('/cashieradd', 'CashierManageController@add')->name('cashieradd');
    Route::post('/cashierdoadd', 'CashierManageController@doadd')->name('cashierdoadd');
    Route::post('/cashierupdate', 'CashierManageController@update')->name('cashierupdate');


    //支付宝花呗分期页面
    Route::get('/alipayhbfq', 'AlipayHbfqController@alipayhbfq')->name('alipayhbfq');
    Route::post('/alipayhbfqPost', 'AlipayHbfqController@alipayhbfqPost')->name('alipayhbfqPost');

	//平安见证宝管理
    Route::get('/witness/index',"WitNessController@Index");
    Route::post('/witness/querywitness',"WitNessController@QueryWitNess");
    Route::post('/witness/withdraw',"WitNessController@WithDraw");
    Route::get('/witness/withdrawinfo',"WitNessController@withdrawInfo");

});
//固定金额异步通知
Route::group(['namespace' => 'Merchant', 'prefix' => 'merchant'], function () {
    Route::any('/wxcodeurlnotify', 'NotifyController@wxcodeurlnotify')->name('wxcodeurlnotify');
    Route::any('/alicodeurlnotify', 'NotifyController@alicodeurlnotify')->name('alicodeurlnotify');
});

Route::group(['namespace' => 'WxApp', 'prefix' => 'merchant', 'middleware' => 'auth.merchant'], function () {
    Route::any('PayCodeQr', 'PayCodeQrController@paycodeqr')->name('PayCodeQr');
});
//商户
Route::group(['namespace' => 'Merchant', 'prefix' => 'merchant', 'middleware' => 'throttle:5'], function () {
    Route::get('login', 'LoginController@showLoginForm')->name('merchantLogin');
    Route::post('login', 'LoginController@login');
    Route::get('register', 'RegisterController@showRegister')->name('showRegister');
    Route::post('register', 'RegisterController@register');
    Route::get('setPassword', 'ResetPasswordController@setPassword')->name('setPassword');
    Route::post('setPasswordPost', 'ResetPasswordController@setPasswordPost')->name('setPasswordPost');
});

//微信公众号
Route::group(['namespace' => 'WxApp', 'prefix' => 'wxapp', 'middleware' => 'auth'], function () {
    Route::post('WxAppMenu', 'MenuController@WxAppMenu')->name('WxAppMenu');
    Route::get('WxAppMenuList', 'MenuController@menulist')->name('WxAppMenuList');
    Route::get('WxAppMenuSubList', 'MenuController@menusublist')->name('WxAppMenuSubList');
    Route::get('WechatMenuAdd', 'MenuController@menuadd')->name('WechatMenuAdd');
    Route::post('WechatMenuAddpost', 'MenuController@menuaddpost')->name('WechatMenuAddpost');
    Route::get('WechatMenuEdit', 'MenuController@menuedit')->name('WechatMenuEdit');
    Route::post('WechatMenuEditpost', 'MenuController@menueditpost')->name('WechatMenuEditpost');
    Route::post('WechatMenuDel', 'MenuController@menudel')->name('WechatMenuDel');
    Route::get('WechatMenuSet', 'MenuController@menuset')->name('WechatMenuSet');
    Route::post('WechatMenuDoSet', 'MenuController@menudoset')->name('WechatMenuDoSet');


});

//微信卡券
Route::group(['namespace' => 'WxCard', 'prefix' => 'wxcard', 'middleware' => 'auth'], function () {
    Route::get("/WxCardManage", "WxCardController@index")->name('WxCardManage');
    Route::get("/WxCardOperate", "WxCardController@operate")->name('WxCardOperate');
    //子商户
    Route::get("/WxCardsubMerchantAdd", "WxCardController@addsubmerchant")->name('WxCardsubMerchantAdd');
    Route::post("/WxCardsubMerchantDel", "WxCardController@delsubmerchant")->name('WxCardsubMerchantDel');
    Route::post("/postsubMerchantdata", "WxCardController@postmerchantdata")->name('postsubMerchantdata');
    //获取子商户第二类目
    Route::post('/getsecondcategory', 'WxCardController@getsecondcategory')->name('getsecondcategory');
    //上传图片
    Route::post('/wxcarduploads', 'WxCardController@wxcarduploads')->name('wxcarduploads');

});
//设备管理
Route::group(["namespace" => "ticket", "prefix" => "admin/ticket", "middleWare" => "auth"], function () {
    Route::get("index", "ticketController@index")->name("ticketIndex");
    Route::get("addMerchine", "ticketController@addMerchine")->name("addMerchine");
    Route::post("insertMerchine", "ticketController@insertMerchine")->name("insertMerchine");
    Route::get("deleteMerchine", "ticketController@deleteMerchine")->name("deleteMerchine");
    Route::get("editMerchine", "ticketController@editMerchine")->name("editMerchine");
    Route::post("updateMerchine", "ticketController@updateMerchine")->name("updateMerchine");
    Route::get("merchineConfig", "ticketController@merchineConfig")->name("merchineConfig");
    Route::post("updateConfig", "ticketController@updateConfig")->name("updateConfig");
    Route::get('setMerchine',"ticketController@setMerchine")->name("setMerchine");
    Route::get("merchineLists","ticketController@merchineLists")->name("merchineLists");
    Route::get("merchineList",'ticketController@merchineList')->name("merchineList");
    //U印智能打印云
    Route::get("setUprint","ticketController@setUprint")->name("setUprint");
    Route::get("UprintIndex","ticketController@UprintIndex")->name('UprintIndex');
    Route::get("addUprint","ticketController@addUprint")->name("addUprint");
    Route::post("inserUprint","ticketController@insertUprint")->name("insertUprint");
    Route::get("deleteUprint","ticketController@deleteUprint")->name("deleteUprint");
    Route::get("editUprint","ticketController@editUprint")->name("editUprint");
    Route::post("updateUprint","ticketController@updateUprint")->name("updateUprint");
    //极光配置
    Route::get("setJpushConfigs","jpushConfigsController@setJpushConfigs")->name("setJpushConfigs");
    Route::post("updateJpushConfigs","jpushConfigsController@updateJpushConfigs")->name("updateJpushConfigs");
   //app更新
    Route::get("updateAppIndex","updateAppController@index")->name("updateAppIndex");
    Route::post("updateApp","updateAppController@updateApp")->name("updateApp");


});
//广告系统管理
Route::group(['namespace' => "Ad", "prefix" => "admin/ad", "middleWare" => "auth"], function () {
    Route::get("index", "adController@index")->name("adIndex");
    Route::get("addAd", "adController@addAd")->name("addAd");
    Route::post("insertAd", "adController@insertAd")->name("insertAd");
    Route::get("deleteAd", "adController@deleteAd")->name("deleteAd");
    Route::get("editAd", "adController@editAd")->name("editAd");
    Route::post("updateAd", "adController@updateAd")->name("updateAd");
});


//浦发银行  需要登录
Route::group(['namespace' => 'PuFa', 'prefix' => 'admin/pufa', 'middleware' => 'auth'], function () {

    Route::resource('/store', 'StoreController');


    // 商户进件资料
    Route::get('jinjian', 'PufaController@jinjian');

    //用服务商信息生成支付宝二维码链接
    Route::get('pf', 'AlipayQrController@OnlySkm');
    // 查询订单接口
    Route::get('queryorder', 'AlipayTradeQueryController@QueryOrder');
    // 订单退款接口
    Route::get('refundorder', 'AlipayTradeRefundController@RefundOrder');

    // 用服务商信息生成微信二维码链接
    Route::get('wxauth', 'WeiXinController@userwxauth');


    // 后台页面设置
    Route::get('/QrLists', 'PufapayController@QrLists')->name('PFQrLists');

    Route::get('/storelist', 'StoreController@storelist')->name('storelist');
    Route::any('/storeEdit', 'StoreController@storeEdit')->name('storeEdit');
    Route::any('/storeDel', 'StoreController@storeDel')->name('storeDel');
    Route::any('/BranchAdd', 'StoreController@BranchAdd')->name('BranchAdd');
    Route::any('/orderList', 'StoreController@orderList')->name('orderList');
    Route::any('/branchStore', 'StoreController@branchStore')->name('branchStore');
    Route::any('/cashierQr', 'StoreController@cashierQr')->name('cashierQr');
    Route::any('/pufaConfig', 'StoreController@pufaConfig')->name('pufaConfig');
    // 创建二维码
    Route::post('/createQr', 'PufapayController@createQr')->name('PFcreateQr');
    // 下载空码
    Route::any('/DownloadQr', 'PufapayController@DownloadQr')->name('PFDownloadQr');


});



Route::group(['namespace' => 'PuFa', 'prefix' => 'api/pufa', 'middleware' => 'auth.merchant'], function () {
    //门店扫码提交进件资料 
    Route::get('/autoStore', 'StoreController@autoStore')->name('pfautoStore');
    Route::post('/autoStorePost', 'StoreController@autoStorePost')->name('PFautoStorePost');

});


Route::group(['namespace' => 'PuFa', 'prefix' => 'api/pufa'], function () {
    // 浦发银行生成统一入参表单
    Route::any('/ppdata', 'PrepareDataController@aliform');

    // 调起支付宝的统一支付
    // Route::post('/AliUnify', 'AlipayTradeCreateController@AliUnify')->name("AliUnify");
    Route::any('/AliUnify', 'AlipayTradeCreateController@AliUnify')->name("AliUnify");

    // 浦发银行异步通知地址
    Route::post('/notify', 'AlipayTradeCreateController@notify')->name("notify");

    //单页面
    Route::get('/PaySuccess', 'AlipayTradeCreateController@PaySuccess')->name('PaySuccess');
    Route::get('/OrderErrors', 'AlipayTradeCreateController@OrderErrors')->name('OrderErrors');

    // 浦发微信生成统一入参表单
    Route::any('/wxform', 'PufaWeixinController@wxform');
    // 浦发微信统一下单
    Route::any('/wxorder', 'PufaWeixinController@wxorder')->name('wxorder');

    // 浦发银行微信异步通知地址
    Route::post('/wxnotify', 'PufaWeixinController@wxnotify')->name("wxnotify");
    Route::any('/resultPage', 'PufaWeixinController@resultPage')->name('resultPage');

    //商家的支付二维码 
    Route::get('/payway', 'PufapayController@payway')->name('payway');


    // 后台手动提交进件资料
    Route::get('/add', 'StoreController@add')->name('pufaStoreAdd');
    // 处理提交的进件资料
    Route::post('/addPost', 'StoreController@addpost')->name('PufaStoreAddPost');
    //获取 浦发商铺的行业分类
    Route::post('/PFCate', 'PufaController@PFCate')->name('PFCate');
    // 省市区
    Route::any('/region', 'PufaController@region')->name("pf_region");

    Route::post('/pufabankrelation', 'PufaController@pufabankrelation')->name('pufabankrelation');
    Route::post('/pufabank', 'PufaController@pufabank')->name('pufabank');
    Route::post('/province', 'PufaController@province')->name('province');
    Route::post('/city', 'PufaController@city')->name('city');
    Route::any('/uploadImagePufa', 'PufaController@uploadImagePufa')->name("uploadImagePufa");//上传至服务器


    Route::any('/storeSuccess', 'StoreController@storeSuccess')->name('storeSuccess');
});
//支付宝卡券
Route::group(['namespace' => "alipass", "prefix" => "admin/alipass"], function () {
    Route::get("/index", "alipassController@index")->name("index");
    Route::get("/addAlipass", "alipassController@addAlipass")->name("addAlipass");
    Route::post("/createAlipass", "alipassController@createAlipass")->name("createAlipass");
    Route::get("/useAlipass", "alipassController@useAlipass")->name("useAlipass");
    Route::get("description","alipassController@description")->name('description');
});

//二维码统一管理
Route::group(['namespace' => 'Qr', 'middleware' => 'auth', 'prefix' => 'admin/qr'], function () {
    //空码生成
    Route::get('/QrLists', 'QrController@QrLists')->name('QrList');
    Route::post('/createQr', 'QrController@createQr')->name('QrCreate');
    Route::any('/DownloadQr', 'QrController@DownloadQr')->name('QrDownload');
});


//银联商户统一管理
Route::group(['namespace' => 'UnionPay', 'middleware' => 'auth', 'prefix' => 'admin/UnionPay'], function () {

    Route::get('/set', 'ConfigController@set')->name('UnionPaySet');
    Route::post('/setPost', 'ConfigController@setPost')->name('UnionPaySetPost');
    Route::any('/index', 'StoreController@index')->name('UnionPayStoreIndex');
    Route::any('/UnionPayStoreSuccess', 'StoreController@UnionPayStoreSuccess')->name('UnionPayStoreSuccess');
    Route::get('/unionPayBill', 'unionPayBillController@unionPayBill')->name('unionPayBill');
    Route::get("/setUnionPayCard","unionPayBillController@setUnionPayCard")->name("setUnionPayCard");
    Route::post("/setCard","unionPayBillController@setCard")->name("setCard");
    Route::get("/unionpayInfo","unionPayBillController@unionpayInfo")->name("unionpayInfo");
    Route::post("/unionpayChangeStatus","unionPayBillController@unionpayChangeStatus")->name("unionpayChangeStatus");
    Route::post("/unionPayStatus","unionPayBillController@unionPayStatus")->name("unionPayStatus");
    Route::any("/unionRestoreIndex","unionPayBillController@unionRestoreIndex")->name("unionRestoreIndex");
    Route::get("/unionRestore","unionPayBillController@unionRestore")->name("unionRestore");
    Route::post("/deleteUnionPay","unionPayBillController@deleteUnionPay")->name("deleteUnionPay");
    Route::post("/unionSelected","unionPayBillController@unionSelected")->name("unionSelected");
    //银联的分店
    Route::get("/BranchIndex", "BranchController@BranchIndex");
    Route::get("/BranchAdd", "BranchController@BranchAdd");
    Route::post("/BranchAddPost", "BranchController@BranchAddPost")->name('UnionPayBranchAddPost');

});
//银联商户统一管理 无需登录
Route::group(['namespace' => 'UnionPay', 'prefix' => 'admin/UnionPay'], function () {
    Route::any('/UnionPayStoreSuccess', 'StoreController@UnionPayStoreSuccess')->name('UnionPayStoreSuccess');
    Route::any('/notify_url', 'NotifyController@notify_url');

});

//银联商户统一管理 商户要登录
Route::group(['namespace' => 'UnionPay', 'middleware' => 'auth.merchant', 'prefix' => 'admin/UnionPay'], function () {
    Route::get('/create', 'StoreController@create')->name('UnionPayStoreCreate');
    Route::any('/bindCard', 'StoreController@bindCard')->name('UnionPayBindCard');
    Route::any('/bindCardPost', 'StoreController@bindCardPost')->name('UnionPayBindCardPost');
    Route::any('/store', 'StoreController@store')->name('UnionPayStore');
    Route::post('/order', 'UnionPayController@order')->name('UnionPayOrder');

});



////////////////////////民生银行路由设置/////START////////////////////////////////////////////

////////////////////////民生银行路由设置/////START////////////////////////////////////////////
Route::group(['namespace' => 'MinSheng','middleware' => 'auth.merchant',  'prefix' => 'api/minsheng'], function () {
    Route::any('/info', 'BusinessController@info')->name("ms_info");
    Route::any('/merchantls', 'MsLsController@ls')->name("msmerchantls");

});


Route::group(['namespace' => 'MinSheng', 'prefix' => 'api/minsheng'], function () {

    //民生进件资料
    Route::any('/cate', 'BusinessController@cate')->name("ms_cate");
    Route::any('/bank', 'BusinessController@bank')->name("ms_bank");

    Route::any('/region', 'BusinessController@region')->name("ms_region");

    Route::any('/infonotify', 'BusinessController@infonotify')->name("ms_infonotify");
    Route::any('/mssuccess', 'BusinessController@mssuccess')->name("ms_info_success");
    Route::any('/saveInfo', 'BusinessController@saveInfo')->name("ms_saveInfo");
    
    // 民生支付宝
    Route::get('/aliform', 'AlipayController@aliform')->name("ms_aliform");
    Route::post('/handle', 'AlipayController@handle')->name("ms_handle");
    Route::any('/page', 'AlipayController@page')->name("ms_page");
    Route::post('/paynotify', 'AlipayController@paynotify')->name("ms_paynotify");

    //民生微信
    Route::get('/wxform', 'WxpayController@wxform')->name("ms_wxform");
    Route::post('/wxhandle', 'WxpayController@wxhandle')->name("ms_wxhandle");
    Route::get('/wxsuccess', 'WxpayController@wxsuccess')->name("ms_wxsuccess");
    Route::post('/wxpaynotify', 'WxpayController@wxpaynotify')->name("ms_wxpaynotify");


    //整合
    Route::any('/payway', 'MsPayController@payway')->name("ms_payway");
    


});


Route::group(['namespace' => 'MinSheng', 'prefix' => 'admin/minsheng', 'middleware' => 'auth'], function () {
    //民生二维码管理
    Route::get('/QrLists', 'QrController@QrLists')->name('ms_QrLists');
    Route::post('/createQr', 'QrController@createQr')->name('ms_createQr');
    Route::any('/DownloadQr', 'QrController@DownloadQr')->name('ms_DownloadQr');

    Route::get('/aliQr', 'AlipayController@aliQr')->name("ms_aliqr");
    Route::get('/wxQr', 'WxpayController@wxQr')->name("ms_wxqr");
    Route::any('/order', 'ManageController@order')->name("ms_order");

    // 服务商后台配置
    Route::any('/config', 'ManageController@config')->name("ms_config");
    Route::any('/storeEdit', 'ManageController@storeEdit')->name("ms_store_edit");
    Route::any('/storeList', 'ManageController@storeList')->name("ms_storelist");
    //审核失败后重新进件
    Route::any('/saveStoreAdd', 'ManageController@saveStoreAdd')->name("ms_saveStoreAdd");
    Route::any('/saveRate', 'ManageController@saveRate')->name("ms_saveRate");
    Route::any('/normalEdit', 'ManageController@normalEdit')->name("ms_normalEdit");
    // 民生分店管理
    Route::any('/BranchAdd', 'ManageController@BranchAdd')->name("ms_BranchAdd");
    Route::any('/branchStore', 'ManageController@branchStore')->name("ms_branchStore");
    Route::any('/setwxsubappid', 'ManageController@setwxsubappid')->name("setwxsubappid");
    

});


////////////////////////民生银行路由设置/////END////////////////////////////////////////////




//山东民生银行路由
Route::group(['namespace' => 'SDMinSheng', 'prefix' => 'bank/sdminsheng'], function () {
    //民生进件资料
    Route::any('/uploadmerchant', 'StoreController@uploadmerchant')->name("uploadmerchant");
});

Route::group(['namespace' => 'SDMinSheng', 'prefix' => 'admin/sdminsheng', 'middleware' => 'auth'], function () {

//    Route::get('/aliQr', 'AlipayController@aliQr')->name("ms_aliqr");
//    Route::any('/config', 'ManageController@config')->name("ms_config");

});
//微众银行路由
Route::group(['namespace' => 'WeBank', 'prefix' => 'admin/webank'], function () {
    //进件
    Route::any('/merchantregister', 'StoreController@merchantregister')->name("merchantregister");
    Route::any('/webankQrCode', 'WebankQrController@webankQrCode')->name("webankQrCode");
    Route::any('/weixin/publicPay', 'PayController@publicPay')->name("webankpublicPay");
    Route::post('/weixin/dopay', 'PayController@doPay')->name("webankdoPay");
    Route::any('/weixin/alipaysuccess', 'PayController@alipaysuccess')->name("webankalipaysuccess");
    Route::any('/weixin/alipayerror', 'PayController@alipayerror')->name("webankalipayerror");
    Route::post('/ali_callback', 'NotifyController@ali_callback')->name("webankali_callback");

    Route::any('/weixin/weixinPay', 'PayController@weixinPay')->name("webankweixinPay");
    Route::post('/weixin/wxdopay', 'PayController@wxdoPay')->name("webankwxdoPay");
    Route::any('/weixin/wxpaysuccess', 'PayController@wxpaysuccess')->name("webankwxpaysuccess");
    Route::any('/weixin/wxpayerror', 'PayController@wxpayerror')->name("webankwxpayerror");
    Route::post('/wx_callback', 'NotifyController@wx_callback')->name("webankwx_callback");

    Route::post('/getcate', 'StoreController@getcate')->name("webankgetcate");
    Route::post('/getmcc', 'StoreController@getmcc')->name("webankgetmcc");
    Route::get('/storesuccess', 'StoreController@storesuccess')->name("webankstoresuccess");
});
//微众需要登录服务商
Route::group(['namespace' => 'WeBank', 'prefix' => 'admin/webank','middleware' => 'auth'], function () {
    //列表
    Route::any('/index', 'StoreController@index')->name("webankindex");
    Route::any('/config', 'StoreController@config')->name("webankconfig");
    Route::post('/configpost', 'StoreController@configpost')->name("webankconfigpost");
    Route::post('/sendfile', 'StoreController@sendfile')->name("webanksendfile");
    Route::get('/editcode', 'StoreController@editcode')->name("webankeditcode");
    Route::post('/doeditcode', 'StoreController@doeditcode')->name("upWebank");
    Route::post('/paystatus', 'ManageController@paystatus')->name("Webankpaystatus");
    Route::post('/deletestore', 'ManageController@deletestore')->name("DelWebankstore");
    Route::any('/restore', 'ManageController@restore')->name("webankRestore");
    Route::any('/storeback', 'ManageController@storeback')->name("webankstoreback");
    Route::any('/allstoreback', 'ManageController@allstoreback')->name("webankallstoreback");
    Route::any('/orderlist', 'ManageController@orderlist')->name("webankorderlist");
    Route::any('/merchantfile', 'ManageController@merchantfile')->name("webankmerchantfile");
    Route::post('/merchantfilepost', 'ManageController@merchantfilepost')->name("webankmerchantfilepost");
    Route::any('/eidtmerchantfile', 'StoreController@editmerchantfile')->name("webankeditmerchantfile");
    Route::post('/eidtmerchantfilepost', 'StoreController@editmerchantfilepost')->name("webankeditmerchantfilepost");
    Route::any('/cashierlist', 'ManageController@cashierlist')->name("webankcashierlist");
    Route::any('/cashierqr', 'ManageController@cashierqr')->name("webankcashierqr");
    Route::any('/branchlist', 'WebankBranchController@branchlist')->name("webankbranchlist");
    Route::any('/branchadd', 'WebankBranchController@branchadd')->name("webankbranchadd");
    Route::post('/branchaddpost', 'WebankBranchController@branchaddpost')->name("webankbranchaddpost");
    Route::any('/qrlist', 'ManageController@qrlist')->name("webankqrlist");
    Route::post('/createqr', 'ManageController@createqr')->name("webankcreateqr");
    Route::any('/downloadqr', 'ManageController@downloadqr')->name("webankdownloadqr");
});
//微众需要登录商户
Route::group(['namespace' => 'WeBank', 'prefix' => 'admin/webank','middleware' => 'auth.merchant'], function () {
    //列表
    Route::any('/register', 'StoreController@register')->name("webankregister");
    Route::any('/bindcard', 'StoreController@bindcard')->name("webankbindcard");
    Route::post('/merchantregister', 'StoreController@merchantregister')->name("merchantregister");
    Route::any('/uploadfile', 'StoreController@uploadfile')->name("webankuploadfile");
    Route::post('/douploadfile', 'StoreController@douploadfile')->name("webankdouploadfile");
    Route::post('/douploadfiles', 'StoreController@douploadfiles')->name("webankdouploadfiles");
});
//微众需要登录商户
Route::group(['namespace' => 'WeBank', 'prefix' => 'merchant/webank','middleware' => 'auth.merchant'], function () {
    //列表
    Route::get('/ls', 'WebankLsController@ls')->name("webankmerchantls");
});

//极光推送
Route::group(['namespace' => 'Push', 'prefix' => 'admin/Jpush'], function () {
    //民生进件资料
    Route::any('/push', 'JpushController@push');
});
//帮助中心
Route::group(['namespace'=>"Questions","prefix"=>"admin/questions",'middleware' => 'auth'],function(){
    Route::get("index","questionsController@index")->name("questionsIndex");
    Route::get("addQuestions","questionsController@addQuestions")->name('addQuestions');
    Route::post("createQuestions","questionsController@createQuestions")->name('createQuestions');
    Route::get('questionsDesc','questionsController@questionsDesc')->name("questionsDesc");
    Route::get("editQuestions","questionsController@editQuestions")->name('editQuestions');
    Route::post('updateQuestions',"questionsController@updateQuestions")->name("updateQuestions");
});

//中信 需要登录
Route::group(['namespace' => 'ZhongXin', 'prefix' => 'admin/zx', 'middleware' => 'auth'], function () {

    Route::resource('/store', 'StoreController');


    // 商户进件资料
    Route::get('jinjian', 'ZhongXinController@zxjinjian');

    //用服务商信息生成支付宝二维码链接
    Route::get('pf', 'AlipayQrController@zxOnlySkm');
    // 查询订单接口
    Route::get('queryorder', 'AlipayTradeQueryController@zxQueryOrder');
    // 订单退款接口
    Route::get('refundorder', 'AlipayTradeRefundController@zxRefundOrder');

    // 用服务商信息生成微信二维码链接
    Route::get('wxauth', 'WeiXinController@zxuserwxauth');


    // 后台页面设置
    Route::get('/QrLists', 'PufapayController@QrLists')->name('zxQrLists');

    Route::get('/storelist', 'StoreController@storelist')->name('zxstorelist');
    Route::any('/storeEdit', 'StoreController@storeEdit')->name('zxstoreEdit');
    Route::any('/storeDel', 'StoreController@storeDel')->name('zxstoreDel');
    Route::any('/BranchAdd', 'StoreController@BranchAdd')->name('zxBranchAdd');
    Route::any('/orderList', 'StoreController@orderList')->name('zxorderList');
    Route::any('/branchStore', 'StoreController@branchStore')->name('zxbranchStore');
    Route::any('/cashierQr', 'StoreController@cashierQr')->name('zxcashierQr');
    Route::any('/zxConfig', 'StoreController@zxConfig')->name('zxConfig');
    // 创建二维码
    Route::post('/createQr', 'PufapayController@createQr')->name('zxcreateQr');
    // 下载空码
    Route::any('/DownloadQr', 'PufapayController@DownloadQr')->name('zxDownloadQr');


});


Route::group(['namespace' => 'ZhongXin', 'prefix' => 'admin/zx', 'middleware' => 'auth.merchant'], function () {
    //门店扫码提交进件资料
    Route::get('/autoStore', 'StoreController@autoStore')->name('zxautoStore');
    Route::post('/autoStorePost', 'StoreController@autoStorePost')->name('zxautoStorePost');

});


Route::group(['namespace' => 'ZhongXin', 'prefix' => 'admin/zx'], function () {
    // 浦发银行生成统一入参表单
    Route::any('/ppdata', 'PrepareDataController@zxaliform');

    // 调起支付宝的统一支付
    // Route::post('/AliUnify', 'AlipayTradeCreateController@AliUnify')->name("AliUnify");
    Route::any('/AliUnify', 'AlipayTradeCreateController@AliUnify')->name("zxAliUnify");

    // 浦发银行异步通知地址
    Route::post('/zxnotify', 'AlipayTradeCreateController@notify')->name("zxnotify");

    //单页面
    Route::get('/PaySuccess', 'AlipayTradeCreateController@PaySuccess')->name('zxPaySuccess');
    Route::get('/OrderErrors', 'AlipayTradeCreateController@OrderErrors')->name('zxOrderErrors');

    // 浦发微信生成统一入参表单
    Route::any('/wxform', 'PufaWeixinController@zxwxform');
    // 浦发微信统一下单
    Route::any('/wxorder', 'PufaWeixinController@wxorder')->name('zxwxorder');

    // 浦发银行微信异步通知地址
    Route::post('/wxnotify', 'PufaWeixinController@wxnotify')->name("zxwxnotify");
    Route::any('/resultPage', 'PufaWeixinController@resultPage')->name('zxresultPage');

    //商家的支付二维码
    Route::get('/payway', 'PufapayController@payway')->name('zxpayway');


    // 后台手动提交进件资料
    Route::get('/add', 'StoreController@add')->name('zxStoreAdd');
    // 处理提交的进件资料
    Route::post('/addPost', 'StoreController@addpost')->name('zxStoreAddPost');
    //获取 浦发商铺的行业分类
    Route::post('/PFCate', 'ZhongXinController@PFCate')->name('zxCate');
    // 省市区
    Route::any('/region', 'ZhongXinController@region')->name("zx_region");

    Route::post('/zxbankrelation', 'ZhongXinController@zxbankrelation')->name('zxbankrelation');
    Route::post('/pufabank', 'ZhongXinController@pufabank')->name('zxbank');
    Route::post('/province', 'ZhongXinController@province')->name('zxprovince');
    Route::post('/city', 'ZhongXinController@city')->name('zxcity');
    Route::any('/uploadImagezx', 'ZhongXinController@uploadImagezx')->name("uploadImagezx");//上传至服务器


    Route::any('/storeSuccess', 'StoreController@storeSuccess')->name('zxstoreSuccess');
});
//新大陆刷卡通道
Route::group(['namespace' => 'NewLand', 'prefix' => 'admin/newland'], function () {
    Route::get('newlandIndex',"NewLandController@newlandIndex")->name("newlandIndex");
    Route::get('addNewLand',"NewLandController@addNewLand")->name('addNewLand');
    Route::post('insertNewLand',"NewLandController@insertNewLand")->name('insertNewLand');
    Route::get('editNewLand',"NewLandController@editnewLand")->name("editNewLand");
    Route::post('updateNewLand',"NewLandController@updateNewLand")->name('updateNewLand');
    Route::get('delNewLand',"NewLandController@delNewLand")->name("delNewLand");
    Route::post("searchNewLand","NewLandController@searchNewLand")->name("searchNewLand");
    Route::get('NewLandRestore',"NewLandController@NewLandRestore")->name("NewLandRestore");
    Route::get("RestoreNewLand","NewLandController@RestoreNewLand")->name('RestoreNewLand');
    Route::post('RestoreNewLands',"NewLandController@RestoreNewLands")->name('RestoreNewLands');
    Route::get('deleteNewLand',"NewLandController@deleteNewLand")->name('deleteNewLand');
    Route::get('NewLandBranchIndex',"NewLandController@NewLandBranchIndex")->name('NewLandBranchIndex');
    Route::get('NewLandBranchRestore',"NewLandController@NewLandBranchRestore")->name('NewLandBranchRestore');
    Route::get('NewLandBills',"NewLandController@NewLandBills")->name('NewLandBills');
});




//银联  unionpay 
Route::group(['namespace' => 'Union', 'prefix' => 'up'], function () {
    Route::any('test',"TestController@index");
    Route::any('search',"SearchController@index");
    Route::any('notice',"NoticeController@index");


});

Route::group(['namespace' => 'Union', 'prefix' => 'upstore', 'middleware' => 'auth'], function () {

    Route::any('lst',"StoreController@lst")->name('upstorelst');
    Route::any('edit',"StoreController@edit")->name('upstoreedit');

});



//设备管理
Route::group(["namespace" => "Yirui", "prefix" => "admin/yirui", "middleWare" => "auth"], function () {
    Route::any("paipai/lst", "PaipaiController@lst")->name("paipailst");
    Route::any("paipai/add", "PaipaiController@add")->name("paipaiadd");
    Route::any("paipai/storemerchant", "PaipaiController@storeMerchant")->name("paipaistoremerchant");


});