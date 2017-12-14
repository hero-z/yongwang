<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/2/26
 * Time: 17:39
 */

namespace App\Http\Controllers\Merchant;

use App\Merchant;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayShopLists;
use App\Models\MerchantShops;
use App\Models\PinganStore;
use App\Models\WeixinShopList;
use DB;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

use App\Http\Controllers\Controller;

class mobileOrderController extends Controller
{

    //商户账单流水
    public function orderls(Request $request){
        $data=$request->only('shop_branch','shop_cashier','pay_source','status','store_type','time','time_start','time_end');
        //过滤无效搜索
        foreach($data as $k=>$v){
            if($v=='0'||is_null($v)){
                $data[$k]='';
            }
        }
        //获取搜索条件
        $shop_branch=$data['shop_branch'];
        $shop_cashier=$data['shop_cashier'];
        $pay_source=$data['pay_source'];
        $status=$data['status'];
        $store_type=$data['store_type'];
        $time=$data['time'];
        $time_start=$data['time_start'];
        $time_end=$data['time_end'];

        $list='';//数据列表
        $shoplists=[];//分店列表
        $userlists=[];//收银员列表
        $totalje=0;//总金额
        $store_ids=[];//店铺id集合
        $merchant_ids=[];//收银员id集合
        $paylists=[];//支付方式列表


        //管理员,收银员搜索选项
        $merchant_id=auth()->guard('merchant')->user()->id;
        $mid=DB::table('merchants')->where('id',$merchant_id)->first();
        if($mid->type==0){
            //如果是管理员,分配分店列表信息
            $shoplistsorce=MerchantShops::where('merchant_id',$merchant_id)->select('store_id','store_name');
            $shoplists=$shoplistsorce->get();
            //总店分店flag
            $flag=true;
            $first='';
            $second='';
            $third='';
            foreach ($shoplists as $v){
                $head=substr($v->store_id,0,1);
                switch($head){
                    case 'o':
                        $parent = AlipayAppOauthUsers::where('store_id',$v->store_id)->first();
                        if($parent->pid==0){
                            $pid=$parent->id;
                            $first = AlipayAppOauthUsers::where('pid', $pid)->where('is_delete',0)->select('store_id','auth_shop_name as store_name');
                        }else{
                            $flag=false;
                        }
                        break;
                    case 'w':
                        $parent = WeixinShopList::where('store_id',$v->store_id)->first();
                        if($parent->pid==0){
                            $pid=$parent->id;
                            $second = WeixinShopList::where('pid', $pid)->where('is_delete',0)->select('store_id','store_name');
                        }else{
                            $flag=false;
                        }
                        break;
                    case 'p':
                        $parent = PinganStore::where('external_id',$v->store_id)->first();
                        if($parent->pid==0){
                            $pid=$parent->id;
                            $third = PinganStore::where('pid', $pid)->where('is_delete',0)->select('external_id as store_id','alias_name as store_name');
                        }else{
                            $flag=false;
                        }
                        break;
                }
            }
            if($flag){
                $result =self::checkEmpty([$first,$second,$third]);
                $shoplists=empty($result)?'':$result->get();
                $shoplists=self::shopNameEnd($shoplists);

                $result =self::checkEmpty([$shoplistsorce]);
                $shoplistss=empty($result)?'':$result->get();
                $shoplistmain=self::shopNameEnd($shoplistss);
            }else{
                $firsto='';
                $firsts='';
                $second='';
                $third='';
                $shoplists=$shoplistsorce->get();
                foreach ($shoplists as $v){
                    $head=substr($v->store_id,0,1);
                    switch($head){
                        case 'o':
                            $firsto = AlipayAppOauthUsers::where('store_id',$v->store_id)->select('store_id','auth_shop_name as store_name');
                            break;
                        case 's':
                            $firsts = AlipayShopLists::where('store_id',$v->store_id)->select('store_id','main_shop_name as store_name');
                            break;
                        case 'w':
                            $second = WeixinShopList::where('store_id',$v->store_id)->select('store_id','store_name');
                            break;
                        case 'p':
                            $third = PinganStore::where('external_id',$v->store_id)->select('external_id as store_id','alias_name as store_name');
                            break;
                    }
                }

                $result =self::checkEmpty([$firsto,$firsts,$second,$third]);
                $shoplistss=empty($result)?'':$result->get();
                $shoplists=self::shopNameEnd($shoplistss);

                $shoplistmain=[];
            }

            //取出store_id
            foreach ($shoplists as $v){
                $store_ids[]=$v->store_id;
            }
            foreach ($shoplistmain as $v){
                $store_ids[]=$v->store_id;
            }
            $userlists_merchatids = DB::table('merchants')
                ->join('merchant_shops', 'merchants.id', '=', 'merchant_shops.merchant_id')
                ->whereIn('merchant_shops.store_id',$store_ids)
                ->select('merchants.id','merchants.name')
                ->get();

            //取出merchant_id
            if($userlists_merchatids){
                foreach($userlists_merchatids as $v){
                    $merchant_ids[]=$v->id;
                }
            }
            $merchant_ids=array_unique($merchant_ids);
            //分配收银员列表信息
            if(!$shop_branch){
                $userlists=Merchant::whereIn('id',$merchant_ids)->select('id','name')->get();
            }else{
                $firsto=$firsts=$second=$third='';
                $head=substr($shop_branch,0,1);
                switch($head){
                    case 'o':
                        $pid = AlipayAppOauthUsers::where('store_id',$shop_branch)->first()->id;
                        $firsto = AlipayAppOauthUsers::where('pid', $pid)->orwhere('id', $pid)->where('is_delete',0)->select('store_id','auth_shop_name as store_name');
                        break;
                    case 's':
                        $pid = AlipayShopLists::where('store_id',$shop_branch)->first()->id;
                        $firsts = AlipayShopLists::where('id', $pid)->where('is_delete',0)->select('store_id','main_shop_name as store_name');
                        break;
                    case 'w':
                        $pid = WeixinShopList::where('store_id',$shop_branch)->first()->id;
                        $second = WeixinShopList::where('pid', $pid)->orwhere('id', $pid)->where('is_delete',0)->select('store_id','store_name');
                        break;
                    case 'p':
                        $pid = PinganStore::where('external_id',$shop_branch)->first()->id;
                        $third = PinganStore::where('pid', $pid)->orwhere('id', $pid)->where('is_delete',0)->select('external_id as store_id','alias_name as store_name');
                        break;
                }
                $result =self::checkEmpty([$firsto,$firsts,$second,$third]);
                $result=empty($result)?'':$result->get();
                $result=self::shopNameEnd($result);
                $ids=[];
                foreach ($result as $v){
                    $ids[]=$v->store_id;
                }
                $ids=array_unique($ids);
                $userlists = DB::table('merchants')
                    ->join('merchant_shops', 'merchants.id', '=', 'merchant_shops.merchant_id')
                    ->whereIn('merchant_shops.store_id',$ids)
                    ->select('merchants.id','merchants.name')
                    ->get();
            }
        }else{
            //普通收银员
            $shop_branch=-1;
            $shop_cashier=$merchant_id;
        }

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
        $request->session()->put('userlists',$userlists);


        $sqlCollection=self::sqlCollection($pay_source,$store_type,$shop_branch,$shop_cashier,$store_ids,$merchant_ids,$status,$time,$time_start,$time_end);
        $totalje=$sqlCollection[0]['je'];
        $result=self::checkEmpty($sqlCollection[0]['res']);
        $list=empty($result)?'':$result->orderby('updated_at','desc')->distinct()->get();
        $counts=empty($result)?'':$result->get()->count();
        $dataPaginator=self::dataPaginator($request,$list);
        $datapage=$dataPaginator['datapage'];
        $paginator=$dataPaginator['paginator'];

        //存放数据
        $request->session()->put('list',$list);

        if($mid->type==0){
            return view('merchant.setWays.mobileOrder', compact('datapage', 'paginator','exportdata','shoplists','shoplistmain','userlists','paylists','shop_branch','shop_cashier','pay_source','status','store_type','time','time_start','time_end','totalje','counts'));
        }else{
            return view('merchant.setWays.mobileOrders', compact('datapage', 'paginator','exportdata','shoplists','shoplistmain','userlists','paylists','shop_branch','shop_cashier','pay_source','status','store_type','time','time_start','time_end','totalje','counts'));
        }

    }
    //封装搜索条件
    public function searchWhere($ttno,$shopBranch,$shopCashier,$status,$time,$time_start,$time_end){

        $where=[];
        //账单涉及的表
        $tradeTable=[1=>'alipay_trade_queries',2=>'wx_pay_orders',3=>'pingan_trade_queries',4=>'merchant_orders'];

        //是否选择分店,收银员||收银员选项
        if($shopBranch&&$shopCashier)
        {
            $where[]=[$tradeTable[$ttno].'.store_id','=',$shopBranch];
            $where[]=[$tradeTable[$ttno].'.merchant_id','=',$shopCashier];
        }elseif(!$shopCashier&&$shopBranch){
            $where[]=[$tradeTable[$ttno].".store_id",$shopBranch];
        }elseif($shopCashier&&!$shopBranch){
            $where[]=[$tradeTable[$ttno].'.merchant_id','=',$shopCashier];
        }

        if($shopBranch<0){
            $where=[];
            $where[]=[$tradeTable[$ttno].'.merchant_id','=',$shopCashier];
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
    public function sqlCollection($sourcePay,$storeType,$shopBranch,$shopCashier,$storeIds,$merchantIds,$status,$time,$time_start,$time_end){
        $totalje=0;
        $result=[];
        $res=[];
        $je=0;
        //扫码枪
        if($sourcePay==1||$sourcePay==''){
            $sqlwhere=[];
            $searchWhere=self::searchWhere(4,$shopBranch,$shopCashier,$status,$time,$time_start,$time_end);
            switch ($storeType){
                case 1:
                    $sqlwhere[]=['type','moalipay'];
                    break;
                case 2:
                    $sqlwhere[]=['type','mweixin'];
                    break;
                case 3:
                    $sqlwhere[]=['type','mpalipay'];
                    break;
                case 4:
                    $sqlwhere[]=['type','mpweixin'];
                    break;
                case 5:
                    $sqlwhere[]=['type','money'];
                    break;
            }
            $result=self::checkIds(4,$shopBranch,$shopCashier,$storeIds,$merchantIds,$searchWhere,$sqlwhere);
            $res[]=$result['res'];
            $je+=$result['je'];
        }
        //二维码
        if($sourcePay==2||$sourcePay==''){

            $storetypeswitch=[1=>'oalipay',2=>'salipay',4=>'alipay',5=>'weixin',6=>'jd',7=>'bestpay'];
            //支付宝
            if($storeType==1||$storeType==2||$storeType==''){
                //拼装sqlwhere支付方式
                $sqlwhere=[];
                if($storeType)
                    $sqlwhere[]=['type',$storetypeswitch[$storeType]];
                $searchWhere=self::searchWhere(1,$shopBranch,$shopCashier,$status,$time,$time_start,$time_end);
                $result=self::checkIds(1,$shopBranch,$shopCashier,$storeIds,$merchantIds,$searchWhere,$sqlwhere);
                $res[]=$result['res'];
                $je+=$result['je'];
            }
            //微信
            if($storeType==3||$storeType==''){
                $sqlwhere=[];
                $searchWhere=self::searchWhere(2,$shopBranch,$shopCashier,$status,$time,$time_start,$time_end);
                $result=self::checkIds(2,$shopBranch,$shopCashier,$storeIds,$merchantIds,$searchWhere,$sqlwhere);
                $res[]=$result['res'];
                $je+=$result['je'];
            }
            //平安
            if($storeType==4||$storeType==5||$storeType==6||$storeType==7||$storeType==''){
                //拼装sqlwhere支付方式
                $sqlwhere=[];
                if($storeType)
                    $sqlwhere[]=['type',$storetypeswitch[$storeType]];
                $searchWhere=self::searchWhere(3,$shopBranch,$shopCashier,$status,$time,$time_start,$time_end);
                $result=self::checkIds(3,$shopBranch,$shopCashier,$storeIds,$merchantIds,$searchWhere,$sqlwhere);
                $res[]=$result['res'];
                $je+=$result['je'];
            }
        }
        return [compact('res','je')];

    }
    //检测store_ids,merchant_ids
    public function checkIds($type,$shopBranch,$shopCashier,$storeIds,$merchantIds,$searchWhere,$sqlwhere){

        $res=[];
        $je=0;
        $whereList=$searchWhere['whereList'];
        $whereJe=$searchWhere['whereJe'];
        $tradeTable=[1=>'alipay_trade_queries',2=>'wx_pay_orders',3=>'pingan_trade_queries',4=>'merchant_orders'];
        //字段区别
        switch($type){
            case 2:
                $total_fee=$total_feee='total_fee';
                break;
            default:
                $total_fee='total_amount as total_fee';
                $total_feee='total_amount';
                break;
        }
//        所有订单


        if(empty($shopBranch)&&empty($shopCashier)){
            $res=DB::table($tradeTable[$type])
                ->whereIn('store_id',$storeIds)
                ->where($whereList)
                ->where($sqlwhere)
                ->select("out_trade_no",$total_fee,"status","type","updated_at");
            $je=DB::table($tradeTable[$type])
                ->whereIn('store_id',$storeIds)
                ->where($whereJe)
                ->where($sqlwhere)
                ->sum($total_feee);
        }else{
            $res=DB::table($tradeTable[$type])
                ->where($whereList)
                ->where($sqlwhere)
                ->select("out_trade_no",$total_fee,"status","type","updated_at");
            $je=DB::table($tradeTable[$type])
                ->where($whereJe)
                ->where($sqlwhere)
                ->sum($total_feee);
        }
        return compact('res','je');
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
    //Ajax获取收银员列表
    public function dataCashier(Request $request)
    {
        $id=$request->id;
        $firsto=$firsts=$second=$third='';
        if($id){
            $head=substr($id,0,1);
            switch($head){
                case 'o':
                    $pid = AlipayAppOauthUsers::where('store_id',$id)->first()->id;
                    $firsto = AlipayAppOauthUsers::where('pid', $pid)->orwhere('id', $pid)->where('is_delete',0)->select('store_id','auth_shop_name as store_name');
                    break;
                case 's':
                    $pid = AlipayShopLists::where('store_id',$id)->first()->id;
                    $firsts = AlipayShopLists::where('id', $pid)->where('is_delete',0)->select('store_id','main_shop_name as store_name');
                    break;
                case 'w':
                    $pid = WeixinShopList::where('store_id',$id)->first()->id;
                    $second = WeixinShopList::where('pid', $pid)->orwhere('id', $pid)->where('is_delete',0)->select('store_id','store_name');
                    break;
                case 'p':
                    $pid = PinganStore::where('external_id',$id)->first()->id;
                    $third = PinganStore::where('pid', $pid)->orwhere('id', $pid)->where('is_delete',0)->select('external_id as store_id','alias_name as store_name');
                    break;
            }
            $result =self::checkEmpty([$firsto,$firsts,$second,$third]);
            $result=empty($result)?'':$result->get();
            $ids=[];
            foreach ($result as $v){
                $ids[]=$v->store_id;
            }
            $ids=array_unique($ids);
            $userlists = DB::table('merchants')
                ->join('merchant_shops', 'merchants.id', '=', 'merchant_shops.merchant_id')
                ->whereIn('merchant_shops.store_id',$ids)
                ->select('merchants.id','merchants.name')
                ->get();
        }else{
            $userlists=$request->session()->get('userlists');
        }
        return json_encode($userlists);
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
    //导出Excel
    public function expexceldata(Request $request){
        $list=$request->session()->get('list');
        $head=['订单号','金额','状态','支付方式','更新时间'];
        $body=[$head];
        $typeformat=['oalipay'=>'当面付','salipay'=>'口碑','alipay'=>'支付宝','weixin'=>'微信','jd'=>'京东','bestpay'=>'翼支付','moalipay'=>'支付宝机具','mpalipay'=>'支付宝机具','mweixin'=>'微信机具','mpweixin'=>'微信机具','money'=>'现金'];
        $statusformat=['40004'=>'取消订单','10003'=>'等待付款',''=>'失败','SUCCESS'=>'成功','TRADE_SUCCESS'=>'成功','JD_SUCCESS'=>'成功'];
        foreach($list as $k=>$v){
            $body[]=[$v->out_trade_no." ",$v->total_fee,$statusformat[$v->status].'  ',str_pad($typeformat[$v->type],15),$v->updated_at];
        }
        $cellData = $body;
        Excel::create(iconv('utf-8','gbk',date('Y-m-d日').'账单统计'),function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }
}