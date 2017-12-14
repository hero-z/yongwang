<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/20
 * Time: 9:18
 */

namespace App\Http\Controllers\AlipayOpen;

use DB;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Excel;
use Mockery\CountValidator\Exception;

class DatacountController extends AlipayOpenController
{

    public function datalist(Request $request)
    {
        $data=$request->only('users','shop','pay_source','status','store_type','time','time_start','time_end');
        //过滤无效搜索
        foreach($data as $k=>$v){
            if($v=='0'||is_null($v)){
                $data[$k]='';
            }
        }
        //获取搜索条件
        $users=$data['users'];
        $shop=$data['shop'];
        $pay_source=$data['pay_source'];
        $status=$data['status'];
        $store_type=$data['store_type'];
        $time=$data['time'];
        $time_start=$data['time_start'];
        $time_end=$data['time_end'];

        $list='';//数据列表
        $userlists=[];//员工列表
        $shoplists=[];//商店列表
        $totalje=0;//总金额
        $paylists=[];//支付方式列表

        //管理员权限  获取员工列表、商店列表
        if (Auth::user()->hasRole('admin')){
            //获取员工列表
            $userlists = DB::table('users')->get();
            if($request->users){
                //获取店铺列表
                //支付宝
                $firsts = DB::table('alipay_shop_lists')->where('user_id',$request->users)->select('store_id', 'main_shop_name as store_name');
                $firsto= DB::table('alipay_app_oauth_users')->where('promoter_id',$request->users)->select('store_id', 'auth_shop_name as store_name');
                //微信
                $seconds = DB::table('weixin_shop_lists')->where('user_id',$request->users)->select('store_id', 'store_name');
                //平安
                $shoplists = DB::table('pingan_stores')
                    ->where('user_id',$request->users)
                    ->select('external_id as store_id', 'alias_name as store_name')
                    ->union($firsts)
                    ->union($firsto)
                    ->union($seconds)
                    ->get()->toArray();
            }
        }else{
            $userlists = DB::table('users')->where('id', '=', Auth::user()->id)->get();
            //获取店铺列表
            //支付宝
            $firsts = DB::table('alipay_shop_lists')->where('user_id',Auth::user()->id)->select('store_id', 'main_shop_name as store_name');
            $firsto = DB::table('alipay_app_oauth_users')->where('promoter_id',Auth::user()->id)->select('store_id', 'auth_shop_name as store_name');
            //微信
            $seconds = DB::table('weixin_shop_lists')->where('user_id',Auth::user()->id)->select('store_id', 'store_name');
            //平安
            $shoplists = DB::table('pingan_stores')
                ->where('user_id',Auth::user()->id)
                ->select('external_id as store_id', 'alias_name as store_name')
                ->union($firsts)
                ->union($firsto)
                ->union($seconds)
                ->get()->toArray();
        }
        $shoplists=self::shopNameEnd($shoplists);

        //支付列表选项
        if($pay_source){
            switch ($pay_source){
                case 1:
                    $paylists=[1=>'支付宝',2=>'微信',3=>'平安支付宝',4=>'平安微信',5=>'现金'];
                    break;
                case 2:
                    $paylists=[1=>'当面付',2=>'口碑',3=>'微信',4=>'平安支付宝',5=>'平安微信',6=>'平安京东',7=>'平安翼支付'];
            }
        }

        $sqlCollection=self::sqlCollection($pay_source,$store_type,$users,$shop,$status,$time,$time_end,$time_start);
        $totalje=$sqlCollection['totalje'];
        $result=self::checkEmpty($sqlCollection['result']);
        $list=empty($result)?'':$result->orderby('updated_at','desc')->distinct()->get();
        $counts=empty($result)?0:$result->get()->count();
        $dataPaginator=self::dataPaginator($request,$list);
        $datapage=$dataPaginator['datapage'];
        $paginator=$dataPaginator['paginator'];

        //存放数据
        $request->session()->put('adminlist',$list);

        return view('admin.alipayopen.datalists', compact('datapage', 'paginator','totalje','userlists','shoplists','paylists','users','shop','pay_source','status','store_type','time','time_start','time_end','counts'));
    }

    //封装搜索条件
    public function searchWhere($ttno,$stno,$users,$shop,$status,$time,$time_end,$time_start){
        $where=[];
        //账单涉及的表
        $tradeTable=[1=>'alipay_trade_queries',2=>'wx_pay_orders',3=>'pingan_trade_queries',4=>'merchant_orders'];
        //店铺表
        $shopTable=[1=>'alipay_app_oauth_users',2=>'alipay_shop_lists',3=>'weixin_shop_lists',4=>'pingan_stores',5=>'merchant_shops'];
        //字段区别
        switch($ttno){
            default:
                $store_id='store_id';
                break;
        }
        switch($stno){
            case 1:
                $user_id='promoter_id';
                break;
            case 5:
                $user_id='merchant_id';
                break;
            default:
                $user_id='user_id';
                break;
        }
        //管理员员工归属选项
        if (Auth::user()->hasRole('admin')){
            //是否选择店铺
            if($users&&$shop)
            {
                $where[]=[$tradeTable[$ttno].'.'.$store_id,'=',$shop];
            }elseif(!$shop&&$users){
                $where[]=[$shopTable[$stno].'.'.$user_id,$users];
            }
        }else{
            //是否选择店铺
            if($shop)
            {
                $where[]=[$tradeTable[$ttno].'.'.$store_id,'=',$shop];
            }else{
                $where[]=[$shopTable[$stno].'.'.$user_id,Auth::user()->id];
            }
        }
        //是否有订单状态搜索
        if($status){
            if($status=='1'){
                $where[]=[$tradeTable[$ttno].'.status','=',''];
            }
        }else{
            $where[]=[$tradeTable[$ttno].'.status','like','%SUCCESS'];
        }
        //快速日期选择
        if($time){
            switch ($time){
                case 1:
                    $time_start=date('Y-m-d' . ' ' . ' 00:00:00',time());
                    $time_end=date('Y-m-d H:i:s',time());
                    break;
                case 2:
                    $time_start=date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day'));
                    $time_end=date('Y-m-d' . ' ' . ' 23:59:59', strtotime('-1 day'));
                    break;
                case 3:
                    $firstday = date("Y-m-01" . ' ' . ' 00:00:00',time());
                    $lastday = date("Y-m-d H:i:s",strtotime("$firstday +1 month"));
                    $time_start=$firstday;
                    $time_end=$lastday;
                    break;
                case 4:
                    $firstday = date("Y-m-01" . ' ' . ' 00:00:00',time());
                    $lastday = date("Y-m-d H:i:s",strtotime("$firstday -1 month"));
                    $time_start=$lastday;
                    $time_end=$firstday;
                    break;
            }
        }
        //时间搜索
        if($time_start)
        {
            $where[]=[$tradeTable[$ttno].'.updated_at','>',$time_start];
        }
        if($time_end)
        {
            $where[]=[$tradeTable[$ttno].'.updated_at','<',$time_end];
        }
        $whereList=$where;
        $where[]=[$tradeTable[$ttno].'.status','like','%SUCCESS'];
        $whereJe=$where;
        return compact('whereList','whereJe');
    }
    //封装结果集
    public function sqlCollection($sourcePay,$storeType,$users,$shop,$status,$time,$time_end,$time_start){
        $totalje=0;
        $result=[];

        //扫码枪
        if($sourcePay==1||$sourcePay==''){
            $res=[];
            $je=0;
            $sqlwhere=[];
            switch ($storeType){
                case 1:
                    $sqlwhere[]=['merchant_orders.type','moalipay'];
                    break;
                case 2:
                    $sqlwhere[]=['merchant_orders.type','mweixin'];
                    break;
                case 3:
                    $sqlwhere[]=['merchant_orders.type','mpalipay'];
                    break;
                case 4:
                    $sqlwhere[]=['merchant_orders.type','mpweixin'];
                    break;
                case 5:
                    $sqlwhere[]=['merchant_orders.type','money'];
                    break;
            }
            //当面付
            $searchWhere=self::searchWhere(4,1,$users,$shop,$status,$time,$time_start,$time_end);
            $whereList=$searchWhere['whereList'];
            $whereJe=$searchWhere['whereJe'];
            $res=DB::table('merchant_orders')
                ->join('alipay_app_oauth_users','alipay_app_oauth_users.store_id','merchant_orders.store_id')
                ->where($whereList)
                ->where($sqlwhere)
                ->select("merchant_orders.out_trade_no as out_trade_no","alipay_app_oauth_users.auth_shop_name as store_name","merchant_orders.total_amount as total_fee","merchant_orders.status as status","merchant_orders.updated_at as updated_at");

            $je=DB::table('merchant_orders')
                ->join('alipay_app_oauth_users','alipay_app_oauth_users.store_id','merchant_orders.store_id')
                ->where($whereJe)
                ->where($sqlwhere)
                ->select('out_trade_no','total_amount')
                ->distinct()
                ->get();
            $je=self::totalJe($je);
            $result[]=$res;
            $totalje+=$je;
            //微信
            $res=[];
            $je=0;
            $searchWhere=self::searchWhere(4,3,$users,$shop,$status,$time,$time_start,$time_end);
            $whereList=$searchWhere['whereList'];
            $whereJe=$searchWhere['whereJe'];
            $res=DB::table('merchant_orders')
                ->join('weixin_shop_lists','weixin_shop_lists.store_id','merchant_orders.store_id')
                ->where($whereList)
                ->where($sqlwhere)
                ->select(['merchant_orders.out_trade_no as out_trade_no','weixin_shop_lists.store_name as store_name','merchant_orders.total_amount as total_fee','merchant_orders.status as status','merchant_orders.updated_at as updated_at']);

            $je=DB::table('merchant_orders')
                ->join('weixin_shop_lists','weixin_shop_lists.store_id','merchant_orders.store_id')
                ->where($whereJe)
                ->where($sqlwhere)
                ->select('out_trade_no','total_amount')
                ->distinct()
                ->get();
            $je=self::totalJe($je);
            $result[]=$res;
            $totalje+=$je;
            //平安
            $res=[];
            $je=0;
            $searchWhere=self::searchWhere(4,4,$users,$shop,$status,$time,$time_start,$time_end);
            $whereList=$searchWhere['whereList'];
            $whereJe=$searchWhere['whereJe'];
            $res=DB::table('merchant_orders')
                ->join('pingan_stores','pingan_stores.external_id','merchant_orders.store_id')
                ->where($whereList)
                ->where($sqlwhere)
                ->select(['merchant_orders.out_trade_no as out_trade_no','pingan_stores.name as store_name','merchant_orders.total_amount as total_fee','merchant_orders.status as status','merchant_orders.updated_at as updated_at']);

            $je=DB::table('merchant_orders')
                ->join('pingan_stores','pingan_stores.external_id','merchant_orders.store_id')
                ->where($whereJe)
                ->where($sqlwhere)
                ->select('out_trade_no','total_amount')
                ->distinct()
                ->get();
            $je=self::totalJe($je);
            $result[]=$res;
            $totalje+=$je;
        }
        //二维码
        if($sourcePay==2||$sourcePay==''){

            //当面付
            if($storeType==1||$storeType==''){
                $res=[];
                $je=0;
                $searchWhere=self::searchWhere(1,1,$users,$shop,$status,$time,$time_end,$time_start);
                $whereList=$searchWhere['whereList'];
                $whereJe=$searchWhere['whereJe'];
                $res=DB::table('alipay_trade_queries')
                    ->join('alipay_app_oauth_users','alipay_app_oauth_users.store_id','alipay_trade_queries.store_id')
                    ->where($whereList)
                    ->select("alipay_trade_queries.out_trade_no as out_trade_no","alipay_app_oauth_users.auth_shop_name as store_name","alipay_trade_queries.total_amount as total_fee","alipay_trade_queries.status as status","alipay_trade_queries.updated_at as updated_at");

                $je=DB::table('alipay_trade_queries')
                    ->join('alipay_app_oauth_users','alipay_app_oauth_users.store_id','alipay_trade_queries.store_id')
                    ->where($whereJe)
                    ->select('out_trade_no','total_amount')
                    ->distinct()
                    ->get();
                $je=self::totalJe($je);
                $result[]=$res;
                $totalje+=$je;
            }
            //口碑
            if($storeType==2||$storeType==''){
                $res=[];
                $je=0;
                $searchWhere=self::searchWhere(1,2,$users,$shop,$status,$time,$time_end,$time_start);
                $whereList=$searchWhere['whereList'];
                $whereJe=$searchWhere['whereJe'];
                $res=DB::table('alipay_trade_queries')
                    ->join('alipay_shop_lists','alipay_shop_lists.store_id','alipay_trade_queries.store_id')
                    ->where($whereList)
                    ->select("alipay_trade_queries.out_trade_no as out_trade_no","alipay_shop_lists.main_shop_name as store_name","alipay_trade_queries.total_amount as total_fee","alipay_trade_queries.status as status","alipay_trade_queries.updated_at as updated_at");

                $je=DB::table('alipay_trade_queries')
                    ->join('alipay_shop_lists','alipay_shop_lists.store_id','alipay_trade_queries.store_id')
                    ->where($whereJe)
                    ->select('out_trade_no','total_amount')
                    ->distinct()
                    ->get();
                $je=self::totalJe($je);
                $result[]=$res;
                $totalje+=$je;
            }
            //微信
            if($storeType==3||$storeType==''){
                $res=[];
                $je=0;
                $searchWhere=self::searchWhere(2,3,$users,$shop,$status,$time,$time_end,$time_start);
                $whereList=$searchWhere['whereList'];
                $whereJe=$searchWhere['whereJe'];
                //微信店铺
                $res=DB::table('wx_pay_orders')
                    ->join('weixin_shop_lists','weixin_shop_lists.store_id','wx_pay_orders.store_id')
                    ->where($whereList)
                    ->select(['wx_pay_orders.out_trade_no as out_trade_no','weixin_shop_lists.store_name as store_name','wx_pay_orders.total_fee as total_fee','wx_pay_orders.status as status','wx_pay_orders.updated_at as updated_at']);

                $je=DB::table('wx_pay_orders')
                    ->join('weixin_shop_lists','weixin_shop_lists.store_id','wx_pay_orders.store_id')
                    ->where($whereJe)
                    ->select('out_trade_no','total_fee as total_amount')
                    ->distinct()
                    ->get();
                $je=self::totalJe($je);
                $result[]=$res;
                $totalje+=$je;
            }
            //平安
            if($storeType==4||$storeType==5||$storeType==6||$storeType==7||$storeType==''){
                $res=[];
                $je=0;
                $storetypeswitch=[1=>'oalipay',2=>'salipay',4=>'alipay',5=>'weixin',6=>'jd',7=>'bestpay'];
                //拼装sqlwhere支付方式
                $sqlwhere=[];
                if($storeType)
                    $sqlwhere[]=['type',$storetypeswitch[$storeType]];
                $searchWhere=self::searchWhere(3,4,$users,$shop,$status,$time,$time_end,$time_start);
                $whereList=$searchWhere['whereList'];
                $whereJe=$searchWhere['whereJe'];
                $res=DB::table('pingan_trade_queries')
                    ->join('pingan_stores','pingan_stores.external_id','pingan_trade_queries.store_id')
                    ->where($whereList)
                    ->where($sqlwhere)
                    ->select(['pingan_trade_queries.out_trade_no as out_trade_no','pingan_stores.name as store_name','pingan_trade_queries.total_amount as total_fee','pingan_trade_queries.status as status','pingan_trade_queries.updated_at as updated_at']);
                $je=DB::table('pingan_trade_queries')
                    ->join('pingan_stores','pingan_stores.external_id','pingan_trade_queries.store_id')
                    ->where($whereJe)
                    ->where($sqlwhere)
                    ->select('out_trade_no','total_amount')
                    ->distinct()
                    ->get();
                $je=self::totalJe($je);
                $result[]=$res;
                $totalje+=$je;
            }
        }
        return compact('result','totalje');
    }
    //检测结果集
    public function checkEmpty($arr){
        $listarr=[];
        try{
            foreach($arr as $v){
                if($v==''){
                    continue;
                }
                $listarr[]=$v;
            }
            $limit=count($listarr);
            if($limit>0){
                $result=$listarr[0];
                if($limit>1){
                    for($i=1;$i<$limit;$i++){
                        $result=$result->union($listarr[$i]);
                    }
                }
                return $result;
            }else{
                return '';
            }
        }catch(Exception $e){

        }
        return '';
    }
    //封装分页
    public function dataPaginator(Request $request,$list){
        if ($list==''||$list->isEmpty()) {
            $paginator = "";
            $datapage = "";
        } else {
            $data = $list->toArray();
            //非数据库模型自定义分页
            $perPage = 9;//每页数量
            if ($request->has('page')) {
                $current_page = $request->input('page');
                $current_page = $current_page <= 0 ? 1 : $current_page;
            } else {
                $current_page = 1;
            }
            $item = array_slice($data, ($current_page - 1) * $perPage, $perPage); //注释1
            $total = count($data);
            $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
            $datapage = $paginator->toArray()['data'];
        }
        return compact('datapage', 'paginator');
    }
    //Ajax获取店铺列表
    public function datadp(Request $request)
    {
        $id=$request->id;
        $list='';
        if($id){
            //获取店铺列表
            //支付宝
            $firsts = DB::table('alipay_shop_lists')->where('user_id',$id)->select('store_id', 'main_shop_name as store_name');
            $firsto = DB::table('alipay_app_oauth_users')->where('promoter_id',$id)->select('store_id', 'auth_shop_name as store_name');
            //微信
            $second = DB::table('weixin_shop_lists')->where('user_id',$id)->select('store_id', 'store_name');
            //平安
            $list = DB::table('pingan_stores')
                ->where('user_id',$id)
                ->select('external_id as store_id', 'alias_name as store_name')
                ->union($firsts)
                ->union($firsto)
                ->union($second)
                ->get()->toArray();
        }
        if($list)
            $list=self::shopNameEnd($list);
        return json_encode($list);
    }
    //Ajax获取支付方式
    public function dataPaylist(Request $request)
    {
        $result=$paylists='';
        $id=$request->id;
        if($id){
            switch ($id){
                case 1:
                    $paylists=[1=>'支付宝',2=>'微信',3=>'平安支付宝',4=>'平安微信',5=>'现金'];
                    break;
                case 2:
                    $paylists=[1=>'当面付',2=>'口碑',3=>'微信',4=>'平安支付宝',5=>'平安微信',6=>'平安京东',7=>'平安翼支付'];
            }
            foreach ($paylists as $k=>$v){
                $result[]=['id'=>$k,'value'=>$v];
            }
        }
        return json_encode($result);
    }
    //shoplists添加后缀
    public function shopNameEnd($shoplists){
        if(empty($shoplists))
            $shoplists=[];
        foreach($shoplists as $k=>$v){
            switch(substr($v->store_id,0,1)){
                case 'o':
                    $shoplists[$k]->store_name=$v->store_name.'(当面付)';
                    break;
                case 's':
                    $shoplists[$k]->store_name=$v->store_name.'(口碑)';
                    break;
                case 'w':
                    $shoplists[$k]->store_name=$v->store_name.'(微信)';
                    break;
                case 'p':
                    $shoplists[$k]->store_name=$v->store_name.'(平安)';
                    break;
                default:
                    $shoplists[$k]->store_name=$v->store_name.'(微信)';
                    break;
            }
        }
        return $shoplists;
    }
    //导出Excel
    public function expexceldata(Request $request){
        $list=$request->session()->get('adminlist');
        $head=['订单号','店名','金额','状态','更新时间'];
        $body=[$head];
        $statusformat=['40004'=>'取消订单','10003'=>'等待付款',''=>'失败','SUCCESS'=>'成功','TRADE_SUCCESS'=>'成功','JD_SUCCESS'=>'成功'];
        foreach($list as $k=>$v){
            $body[]=[$v->out_trade_no." ",$v->store_name,$v->total_fee,$statusformat[$v->status].'  ',$v->updated_at];
        }
        $cellData = $body;
        Excel::create(iconv('utf-8','gbk',date('Y-m-d日').'账单统计'),function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }
    //统计金额
    public function totalJe($data){
        $je=0;
        if($data){
            foreach($data as $v){
                $je+=$v->total_amount;
            }
        }
        return $je;
    }
}