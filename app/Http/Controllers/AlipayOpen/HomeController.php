<?php

namespace App\Http\Controllers\AlipayOpen;

use App\App;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.index');
    }

    //后台主页
    public function home()
    {
        $data = App::where('id', 1)->first();
        if (Auth::user()->hasRole('admin')) {

            //支付宝

            //支付宝总交易量
            $atotal_amount = DB::table('alipay_trade_queries')
                            ->where('status','like', '%SUCCESS')
                            ->sum('total_amount');
            $atotal_amount +=Order::whereBetween('type',[101,106])->where('pay_status',1)->sum('total_amount');
             //支付宝总店铺数
            //口碑
            $astores = DB::table('alipay_shop_lists')
                        ->where('audit_status', 'AUDIT_SUCCESS')
                        ->count();
            //当面付
            $astores += DB::table('alipay_app_oauth_users')->count();
            //支付宝昨日店铺
            //口碑
            $astore_y = DB::table('alipay_shop_lists')
                        ->where('audit_status', 'AUDIT_SUCCESS')
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->count();
            //当面付
            $astore_y += DB::table('alipay_app_oauth_users')
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->count();
            //支付宝昨天流水
            $atotal_y = DB::table('alipay_trade_queries')
                        ->where('status','like', '%SUCCESS')
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->sum('total_amount');
            $atotal_y +=Order::whereBetween('type',[101,106])
                        ->where('pay_status',1)
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->sum('total_amount');

            //微信统计

            //微信总交易量
            $wtotal_amount = DB::table('wx_pay_orders')->where('status', 'SUCCESS')->sum('total_fee');
            $wtotal_amount +=Order::whereBetween('type',[201,203])
                            ->where('pay_status',1)
                            ->sum('total_amount');
            //微信总店铺数
            $wstores = DB::table('weixin_shop_lists')->count();
            //微信昨日店铺
            $wstore_y = DB::table('weixin_shop_lists')
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->count();
            //微信昨天流水
            $wtotal_y = DB::table('wx_pay_orders')
                        ->where('status', 'SUCCESS')
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->sum('total_fee');
            $wtotal_y +=Order::whereBetween('type',[201,203])
                        ->where('pay_status',1)
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->sum('total_amount');
//
            //平安银行
            //平安总交易量
            $ptotal_amount = DB::table('pingan_trade_queries')->where('status','like', '%SUCCESS')->sum('total_amount');
            $ptotal_amount +=Order::whereBetween('type',[301,307])
                            ->where('pay_status',1)
                            ->sum('total_amount');
            //平安总店铺数
            $pstores = DB::table('pingan_stores')->count();
            //平安昨日店铺
            $pstore_y = DB::table('pingan_stores')
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->count();
            //平安昨天流水
            $ptotal_y = DB::table('pingan_trade_queries')
                        ->where('status','like', '%SUCCESS')
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->sum('total_amount');
            $ptotal_y +=Order::whereBetween('type',[301,307])
                        ->where('pay_status',1)
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->sum('total_amount');
            //浦发银行
            //浦发总交易量
            $ftotal_amount = Order::whereBetween('type',[601,602])
                            ->where('pay_status',1)
                            ->sum('total_amount');
            //浦发总店铺数
            $fstores = DB::table('pufa_stores')->count();
            //浦发昨日店铺
            $fstore_y = DB::table('pufa_stores')
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->count();
            //浦发昨天流水
            $ftotal_y  = Order::whereBetween('type',[601,602])
                        ->where('pay_status',1)
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->sum('total_amount');

            //总交易量
            $ttotal_amount = $atotal_amount+$wtotal_amount+$ptotal_amount+$ftotal_amount;
            //总店铺数
            $tstores = $astores+$wstores+$pstores+$fstores;
            //昨日店铺
            $tstore_y = $astore_y+$wstore_y+$pstore_y+$fstore_y;
            //昨天流水
            $ttotal_y = $atotal_y+$wtotal_y+$ptotal_y+$ftotal_y;
        } else {
            //支付宝

            //支付宝总交易量
            //口碑
            $atotal_amount = DB::table('alipay_trade_queries')
                            ->join('alipay_shop_lists','alipay_shop_lists.store_id','alipay_trade_queries.store_id')
                            ->where('alipay_shop_lists.user_id',Auth::user()->id)
                            ->where('alipay_trade_queries.status','like', '%SUCCESS')
                            ->sum('total_amount');
            //当面付
            $atotal_amount+=DB::table('alipay_trade_queries')
                            ->join('alipay_app_oauth_users','alipay_app_oauth_users.store_id','alipay_trade_queries.store_id')
                            ->where('alipay_app_oauth_users.promoter_id',Auth::user()->id)
                            ->where('alipay_trade_queries.status','like', '%SUCCESS')
                            ->sum('total_amount');
            //新订单
            $atotal_amount+=DB::table('orders')
                            ->join('alipay_app_oauth_users','alipay_app_oauth_users.store_id','orders.store_id')
                            ->where('alipay_app_oauth_users.promoter_id',Auth::user()->id)
                            ->where('orders.pay_status',1)
                            ->whereBetween('orders.type',[101,106])
                            ->sum('orders.total_amount');
            $atotal_amount+=DB::table('orders')
                            ->join('alipay_app_oauth_users','alipay_app_oauth_users.store_id','orders.store_id')
                            ->where('alipay_app_oauth_users.promoter_id',Auth::user()->id)
                            ->where('orders.pay_status',1)
                            ->whereBetween('orders.type',[101,106])
                            ->sum('orders.total_amount');
            //支付宝总店铺数
            //口碑
            $astores = DB::table('alipay_shop_lists')
                        ->where('user_id',Auth::user()->id)
                        ->where('audit_status', 'AUDIT_SUCCESS')
                        ->count();
            //当面付
            $astores+= DB::table('alipay_app_oauth_users')
                        ->where('promoter_id',Auth::user()->id)
                        ->count();
            //支付宝昨日店铺
            //口碑
            $astore_y = DB::table('alipay_shop_lists')
                        ->where('user_id',Auth::user()->id)
                        ->where('audit_status', 'AUDIT_SUCCESS')
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->count();
            //当面付
            $astore_y += DB::table('alipay_app_oauth_users')
                        ->where('promoter_id',Auth::user()->id)
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->count();
            //支付宝昨天流水
            //口碑
            $atotal_y = DB::table('alipay_trade_queries')
                        ->join('alipay_shop_lists','alipay_shop_lists.store_id','alipay_trade_queries.store_id')
                        ->where('alipay_shop_lists.user_id',Auth::user()->id)
                        ->where('alipay_trade_queries.status','like', '%SUCCESS')
                        ->where('alipay_trade_queries.updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('alipay_trade_queries.updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->sum('total_amount');
            //当面付
            $atotal_y += DB::table('alipay_trade_queries')
                        ->join('alipay_app_oauth_users','alipay_app_oauth_users.store_id','alipay_trade_queries.store_id')
                        ->where('alipay_app_oauth_users.promoter_id',Auth::user()->id)
                        ->where('alipay_trade_queries.updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('alipay_trade_queries.updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->sum('total_amount');
            //新订单
            $atotal_y+=DB::table('orders')
                        ->join('alipay_app_oauth_users','alipay_app_oauth_users.store_id','orders.store_id')
                        ->where('alipay_app_oauth_users.promoter_id',Auth::user()->id)
                        ->where('orders.pay_status',1)
                        ->whereBetween('orders.type',[101,106])
                        ->where('orders.updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('orders.updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->sum('orders.total_amount');
            $atotal_y+=DB::table('orders')
                        ->join('alipay_app_oauth_users','alipay_app_oauth_users.store_id','orders.store_id')
                        ->where('alipay_app_oauth_users.promoter_id',Auth::user()->id)
                        ->where('orders.pay_status',1)
                        ->whereBetween('orders.type',[101,106])
                        ->where('orders.updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('orders.updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->sum('orders.total_amount');
            //微信统计

            //微信总交易量
            $wtotal_amount = DB::table('wx_pay_orders')
                            ->join('weixin_shop_lists','weixin_shop_lists.store_id','wx_pay_orders.mch_id')
                            ->where('weixin_shop_lists.user_id',Auth::user()->id)
                            ->where('wx_pay_orders.status', 'SUCCESS')
                            ->sum('total_fee');
            $wtotal_amount +=DB::table('orders')
                            ->join('weixin_shop_lists','weixin_shop_lists.store_id','orders.store_id')
                            ->where('weixin_shop_lists.user_id',Auth::user()->id)
                            ->whereBetween('orders.type',[201,203])
                            ->where('orders.pay_status',1)
                            ->sum('orders.total_amount');
            //微信总店铺数
            $wstores = DB::table('weixin_shop_lists')->where('user_id',Auth::user()->id)->count();
            //微信昨日店铺
            $wstore_y = DB::table('weixin_shop_lists')
                        ->where('user_id',Auth::user()->id)
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->count();
            //微信昨天流水
            $wtotal_y = DB::table('wx_pay_orders')
                        ->join('weixin_shop_lists','weixin_shop_lists.store_id','wx_pay_orders.mch_id')
                        ->where('weixin_shop_lists.user_id',Auth::user()->id)
                        ->where('wx_pay_orders.status', 'SUCCESS')
                        ->where('wx_pay_orders.updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('wx_pay_orders.updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->sum('total_fee');
            $wtotal_y +=DB::table('orders')
                        ->join('weixin_shop_lists','weixin_shop_lists.store_id','orders.store_id')
                        ->where('weixin_shop_lists.user_id',Auth::user()->id)
                        ->whereBetween('orders.type',[201,203])
                        ->where('orders.updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('orders.updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->where('orders.pay_status',1)
                        ->sum('orders.total_amount');
//
            //平安银行
            //平安总交易量
            $ptotal_amount = DB::table('pingan_trade_queries')
                            ->join('pingan_stores','pingan_stores.external_id','pingan_trade_queries.store_id')
                            ->where('pingan_stores.user_id',Auth::user()->id)
                            ->where('pingan_trade_queries.status','like', '%SUCCESS')
                            ->sum('total_amount');
            $ptotal_amount +=DB::table('orders')
                            ->join('pingan_stores','pingan_stores.external_id','orders.store_id')
                            ->where('pingan_stores.user_id',Auth::user()->id)
                            ->whereBetween('orders.type',[301,307])
                            ->where('orders.pay_status',1)
                            ->sum('orders.total_amount');
            //平安总店铺数
            $pstores = DB::table('pingan_stores')->where('pingan_stores.user_id',Auth::user()->id)->count();
            //平安昨日店铺
            $pstore_y = DB::table('pingan_stores')
                        ->where('pingan_stores.user_id',Auth::user()->id)
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->count();
            //平安昨天流水
            $ptotal_y = DB::table('pingan_trade_queries')
                        ->join('pingan_stores','pingan_stores.external_id','pingan_trade_queries.store_id')
                        ->where('pingan_stores.user_id',Auth::user()->id)
                        ->where('pingan_trade_queries.status','like', '%SUCCESS')
                        ->where('pingan_trade_queries.updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('pingan_trade_queries.updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->sum('total_amount');
            $ptotal_y +=DB::table('orders')
                        ->join('pingan_stores','pingan_stores.external_id','orders.store_id')
                        ->where('pingan_stores.user_id',Auth::user()->id)
                        ->whereBetween('orders.type',[301,307])
                        ->where('orders.pay_status',1)
                        ->where('orders.updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('orders.updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->sum('orders.total_amount');

            //浦发银行
            //浦发总交易量
            $ftotal_amount =DB::table('orders')
                            ->join('pufa_stores','pufa_stores.store_id','orders.store_id')
                            ->where('pufa_stores.user_id',Auth::user()->id)
                            ->whereBetween('orders.type',[601,602])
                            ->where('orders.pay_status',1)
                            ->sum('orders.total_amount');
            //浦发总店铺数
            $fstores = DB::table('pufa_stores')->where('user_id',Auth::user()->id)->count();
            //浦发昨日店铺
            $fstore_y = DB::table('pufa_stores')
                        ->where('user_id',Auth::user()->id)
                        ->where('updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->count();
            //浦发昨天流水
            $ftotal_y  = DB::table('orders')
                        ->join('pufa_stores','pufa_stores.store_id','orders.store_id')
                        ->where('pufa_stores.user_id',Auth::user()->id)
                        ->whereBetween('orders.type',[601,602])
                        ->where('orders.pay_status',1)
                        ->where('orders.updated_at', '>', date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day')))
                        ->where('orders.updated_at', '<', date('Y-m-d' . '  ' . ' 23:59:59', strtotime('-1 day')))
                        ->sum('orders.total_amount');

            //总交易量
            $ttotal_amount = $atotal_amount+$wtotal_amount+$ptotal_amount+$ftotal_amount;
            //总店铺数
            $tstores = $astores+$wstores+$pstores+$fstores;
            //昨日店铺
            $tstore_y = $astore_y+$wstore_y+$pstore_y+$fstore_y;
            //昨天流水
            $ttotal_y = $atotal_y+$wtotal_y+$ptotal_y+$ftotal_y;

        }
        return view('admin.alipayopen.home', compact('data', 'ttotal_amount', 'tstores','ttotal_y','tstore_y', 'atotal_amount', 'astores','atotal_y','astore_y', 'wtotal_amount', 'wstores','wtotal_y','wstore_y', 'ptotal_amount', 'pstores','ptotal_y','pstore_y','ftotal_amount', 'fstores','ftotal_y','fstore_y'));
    }
}
