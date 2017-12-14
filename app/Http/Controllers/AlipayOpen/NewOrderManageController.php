<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/20
 * Time: 9:18
 */

namespace App\Http\Controllers\AlipayOpen;

use App\Models\PufaStores;
use App\Models\UnionPayStore;
use DB;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Excel;
use Maatwebsite\Excel\Classes\PHPExcel;
use Mockery\CountValidator\Exception;

class NewOrderManageController extends AlipayOpenController
{
    protected $scanninggun= [103=>'当面付机具',105=>'口碑机具',202=>'微信机具',305=>'平安支付宝机具',306=>'平安微信机具',307=>'平安京东机具',402=>'银联机具',603=>'浦发支付宝机具',604=>'浦发微信机具',701=>'现金'];
    protected $qrcodelists=[101=>'当面付',102=>'口碑',104=>'当面付固定金额',106=>'口碑固定金额',201=>'微信',203=>'微信固定金额',301=>'平安支付宝',302=>'平安微信',303=>'平安京东',304=>'平安翼支付',401=>'银联固定金额',501=>'民生支付宝',502=>'民生微信',503=>'民生QQ钱包',601=>'浦发支付宝',602=>'浦发微信','801'=>'微众支付宝','802'=>'微众微信'];
    protected $shopTable=[1=>'alipay_app_oauth_users',2=>'alipay_shop_lists',3=>'weixin_shop_lists',4=>'pingan_stores',5=>'pufa_stores',6=>'union_pay_stores',7=>'ms_stores'];
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
        $allstore_names=[];
        $store_ids=[];

        //管理员权限  获取员工列表、商店列表
        if (Auth::user()->hasRole('admin')){
            //获取员工列表
            $userlists = DB::table('users')->get();
            $requestid=$request->users;
        }else{
            $userlists = DB::table('users')->where('id', '=', Auth::user()->id)->get();
            $requestid=Auth::user()->id;
        }
         $shoplists=self::getdplists($requestid);
//        $shoplists=$this->getStore($requestid);
        //放入store_id

        $store_user=[];
        foreach ($shoplists as $v){
            $store_ids[]=$v->store_id;
            $allstore_names[$v->store_id]=$v->store_name;

            //存入推官员id
            $store_user[$v->store_id]=$v->user_id;
        }

        // 查出所有的推官员--归属员工
        $alluser=DB::table('users')->select('id','name')->get();
        $user_id=[];
        if(!$alluser->isEmpty())
        {
            foreach($alluser as $user)
            {
                $user_id[$user->id]=$user->name;
            }
        }

        if($shop){
            $store_ids=[$shop];
        }
        //支付列表选项
        if($pay_source){
            switch ($pay_source){
                case 1:
                    $paylists=$this->scanninggun;
                    break;
                case 2:
                    $paylists=$this->qrcodelists;
            }
        }
        $sqlCollection=self::sqlCollection(1,$store_ids,$pay_source,$store_type,$users,$shop,$status,$time,$time_end,$time_start);
//        $totalje=$sqlCollection['totalje'];
        $result=self::checkEmpty($sqlCollection['result']);
        
        
        $counts=empty($result)?0:$result->count('id');
//        $list=empty($result)?'':$result->orderby('updated_at','desc')->get();
        $list=empty($result)?'':$result->orderby('updated_at','desc')->paginate(9);
        //存放数据
//        $request->session()->put('adminlist',$list);
        $request->session()->put('allStoreNames',$allstore_names);
        return view('admin.alipayopen.newdatalists', compact('user_id','store_user','list','totalje','userlists','shoplists','paylists','users','shop','pay_source','status','store_type','time','time_start','time_end','counts','allstore_names'));
    }
    public function gettotalamount(Request $request){
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

        $totalje=0;//总金额
        $store_ids=[];

        //管理员权限  获取员工列表、商店列表
        if (Auth::user()->hasRole('admin')){
            //获取员工列表
            $requestid=$request->users;
        }else{
            $requestid=Auth::user()->id;
        }
        $shoplists=self::getdplists($requestid);
        //放入store_id
        foreach ($shoplists as $v){
            $store_ids[]=$v->store_id;
        }
        if($shop){
            $store_ids=[$shop];
        }
        $sqlCollection=self::sqlCollection(2,$store_ids,$pay_source,$store_type,$users,$shop,$status,$time,$time_end,$time_start);
        $result=self::checkEmpty($sqlCollection['result']);
        $totalje+=empty($result)?0:$result->sum('orders.total_amount');
        return json_encode(['totalje'=>$totalje]);
    }
    public function getlists(Request $request){
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

        $totalje=0;//总金额
        $store_ids=[];

        //管理员权限  获取员工列表、商店列表
        if (Auth::user()->hasRole('admin')){
            //获取员工列表
            $requestid=$request->users;
        }else{
            $requestid=Auth::user()->id;
        }
        $shoplists=self::getdplists($requestid);
        //放入store_id
        $store_user=[];
        foreach ($shoplists as $v){
            $store_ids[]=$v->store_id;
            $allstore_names[$v->store_id]=$v->store_name;

            //存入推官员id
            $store_user[$v->store_id]=$v->user_id;
        }

        // 查出所有的推官员--归属员工
        $alluser=DB::table('users')->select('id','name')->get();
        $user_id=[];
        if(!$alluser->isEmpty())
        {
            foreach($alluser as $user)
            {
                $user_id[$user->id]=$user->name;
            }
        }
        if($shop){
            $store_ids=[$shop];
        }
        $sqlCollection=self::sqlCollection(1,$store_ids,$pay_source,$store_type,$users,$shop,$status,$time,$time_end,$time_start);
        $result=self::checkEmpty($sqlCollection['result']);
        $list=empty($result)?'':$result->orderby('updated_at','desc')->distinct()->get();

        return [$list,$store_user,$user_id];
    }
    //封装搜索条件
    public function searchWhere($status,$time,$time_start,$time_end){
        $where=[];
        try{
            //初始化时间
            if(!$time_start&&!$time_end&&!$time){
                $time=1;
            }
            //是否有订单状态搜索
            if($status){
                if($status=="9"){
                }else{
                    $where[]=['orders.pay_status',$status];
                }
            }else{
                $where[]=['orders.pay_status',1];
            }
            //快速日期选择
            if($time){
                switch ($time){
                    case 1:
                        $time_start=date('Y-m-d' . ' ' . ' 23:59:59', strtotime('-7 day'));
                        $time_end=date('Y-m-d H:i:s',time());
                        break;
                    case 2:
                        $time_start=date('Y-m-d' . ' ' . ' 00:00:00',time());
                        $time_end=date('Y-m-d H:i:s',time());
                        break;
                    case 3:
                        $time_start=date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day'));
                        $time_end=date('Y-m-d' . ' ' . ' 23:59:59', strtotime('-1 day'));
                        break;
                    case 4:
                        $firstday = date("Y-m-01" . ' ' . ' 00:00:00',time());
                        $lastday = date("Y-m-d H:i:s",strtotime("$firstday +1 month"));
                        $time_start=$firstday;
                        $time_end=$lastday;
                        break;
                    case 5:
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
                $times=date("Y-m-d H:i:s",strtotime($time_start));
                $where[]=['orders.updated_at','>',$times];
            }
            if($time_end)
            {
                $timee=date("Y-m-d H:i:s",strtotime($time_end));
                $where[]=['orders.updated_at','<',$timee];
            }
            $whereList=$where;
            $where[]=['orders.pay_status',1];
            $whereJe=$where;
            return compact('whereList','whereJe');
        }catch (Exception $e){
            die('获取搜索条件失败');
        }
    }
    //封装结果集
    public function sqlCollection($listje,$storeIds,$sourcePay,$storeType,$users,$shop,$status,$time,$time_end,$time_start){
        $totalje=0;
        $result=[];
        try{
            if($storeType){
                $searchWhere=self::searchWhere($status,$time,$time_start,$time_end);
                $res=self::checkIds([$storeType],$users,$shop,$storeIds,$searchWhere,$listje);
                $result=$res['res'];
                $totalje+=$res['je'];
            }else{
                if($sourcePay==1){
                    $searchWhere=self::searchWhere($status,$time,$time_start,$time_end);
                    $storeTypes=array_keys($this->scanninggun);
                    $res=self::checkIds($storeTypes,$users,$shop,$storeIds,$searchWhere,$listje);
                    $result=$res['res'];
                    $totalje+=$res['je'];
                }elseif($sourcePay==2){
                    $searchWhere=self::searchWhere($status,$time,$time_start,$time_end);
                    $storeTypes=array_keys($this->qrcodelists);
                    $res=self::checkIds($storeTypes,$users,$shop,$storeIds,$searchWhere,$listje);
                    $result=$res['res'];
                    $totalje+=$res['je'];
                }else{
                    $searchWhere=self::searchWhere($status,$time,$time_start,$time_end);
                    $res=self::checkIds([],$users,$shop,$storeIds,$searchWhere,$listje);
                    $result=$res['res'];
                    $totalje+=$res['je'];
                }
            }
            return compact('result','totalje');
        }catch (Exception $e){
            die('封装结果集失败');
        }
    }
    //store_ids
    public function checkIds($storeTypes,$users,$shop,$storeIds,$searchWhere,$listje){
        $res=[];
        $je=0;
        $whereList=$searchWhere['whereList'];
        $whereJe=$searchWhere['whereJe'];
        if(!$storeTypes){
            $storeTypes=array_merge(array_keys($this->scanninggun),array_keys($this->qrcodelists));
        }
        try{
            $res[]=DB::table('orders')
                ->whereIn('orders.type',$storeTypes)
                ->whereIn('orders.store_id',$storeIds)
                ->when($listje==1, function ($query) use ($whereList) {
                    return $query->where($whereList);
                })
                ->when($listje==2, function ($query) use ($whereJe) {
                    return $query->where($whereJe);
                })
                ->select("orders.out_trade_no","orders.store_id",'orders.store_id',"orders.total_amount","orders.merchant_id","orders.remark","orders.pay_status", "orders.type", "orders.updated_at");
            return compact('res','je');
        }catch (Exception $e){
            die('检测结果集失败');
        }
    }
    //检测结果集
    public function checkEmpty($arr){
        $listarr=[];
        try{
            foreach($arr as $v){
                if($v==''||$v==[]){
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
            die('检测空集失败');
        }
    }
    //封装分页
    public function dataPaginator(Request $request,$list){
        try{
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
        }catch (Exception $e){
            die('分页失败');
        }

    }
    //Ajax获取店铺列表
    public function datadp(Request $request)
    {
        $id=$request->id;
        $list=self::getdplists($id);
        return json_encode($list);
    }
    //Ajax获取支付方式
    public function dataPaylist(Request $request)
    {
        try{
            $result=$paylists='';
            $id=$request->id;
            if($id){
                switch ($id){
                    case 1:
                        $paylists=$this->scanninggun;
                        break;
                    case 2:
                        $paylists=$this->qrcodelists;
                }
                foreach ($paylists as $k=>$v){
                    $result[]=['id'=>$k,'value'=>$v];
                }
            }
            return json_encode($result);
        }catch (Exception $e){
            die('获取支付方式失败');
        }
    }
    //shoplists添加后缀
    public function shopNameEnd($shoplists){
        try{
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
                    case 'u':
                        $shoplists[$k]->store_name=$v->store_name.'(银联)';
                        break;
                    case 'f':
                        $shoplists[$k]->store_name=$v->store_name.'(浦发)';
                        break;
                    case 'm':
                        $shoplists[$k]->store_name=$v->store_name.'(民生-厦门)';
                        break;
                    default:
                        $shoplists[$k]->store_name=$v->store_name;
                        break;
                }
            }
            return $shoplists;
        }catch (Exception $e){
            die('店铺后缀添加失败');
        }
    }
    //导出Excel
    public function expexceldata(Request $request){
        try{
            $res=self::getlists($request);
            $list=$res[0];
            $store_user=$res[1];
            $user_id=$res[2];
            $storenames=$request->session()->get('allStoreNames');
            $head=['订单号','店铺名','金额','状态','支付类型','员工','备注','更新时间','收银员名称'];
            $body=[$head];
            $username='店铺收款';
            $store_name='';
            $statusformat=[1=>'成功',2=>'取消订单',3=>'等待支付',4=>'订单关闭',5=>'已退款'];
            foreach($list as $k=>$v){
                if(isset($store_user[$v->store_id])&&isset($user_id[$store_user[$v->store_id]]))
                {
                    $username= ($user_id[$store_user[$v->store_id]]);
                }
                $statusstr='未操作';
                $paystr='';
                if(array_key_exists($v->pay_status,$statusformat)){
                    $statusstr=$statusformat[$v->pay_status];
                }
                if(array_key_exists($v->type,$this->scanninggun)){
                    $paystr=$this->scanninggun[$v->type];
                }
                if(array_key_exists($v->type,$this->qrcodelists)){
                    $paystr=$this->qrcodelists[$v->type];
                }
                if(array_key_exists($v->store_id,$storenames)){
                    $store_name=$storenames[$v->store_id];
                }
                $body[]=[$v->out_trade_no." ",$store_name." ",$v->total_amount,$statusstr.'  ',$paystr,$username,$v->remark,$v->updated_at,$v->merchant_id];
            }
            $cellData = $body;
            /*$cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_memcache;
            $cacheSettings = array( 'memcacheServer'  => 'localhost',
                'memcachePort'    => 11211,
                'cacheTime'       => 600
            );
            \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
//            $cacheSettings = array();
//            \PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);
            $excelObj = new PHPExcel();
            $excelObj->createSheet(iconv('utf-8','gbk',date('Y-m-d日').'账单统计'),$cellData,$head);*/
            Excel::create(iconv('utf-8','gbk',date('Y-m-d日').'账单统计'),function($excel) use ($cellData){
                $excel->sheet('score', function($sheet) use ($cellData){
                    $sheet->rows($cellData);
                });
            })->export('xls');
        }catch (Exception $e){
            die('导出数据失败');
        }
    }
    //店铺列表
    public function getdplists($id){
        $list=[];
        $res=[];
        try{
            //获取店铺列表
            $res[] = DB::table('alipay_shop_lists')
                ->when($id, function ($query) use ($id) {
                    return $query->where('user_id', $id);
                })
                ->select('store_id', 'main_shop_name as store_name','user_id');
            $res[]= DB::table('alipay_app_oauth_users')
                ->when($id, function ($query) use ($id) {
                    return $query->where('promoter_id', $id);
                })
                ->select('store_id', 'auth_shop_name as store_name','promoter_id as user_id');
            //微信
            $res[] = DB::table('weixin_shop_lists')
                ->when($id, function ($query) use ($id) {
                    return $query->where('user_id', $id);
                })
                ->select('store_id', 'store_name','user_id');
            //银联
            $res[]=DB::table('union_pay_stores')
                ->when($id, function ($query) use ($id) {
                    return $query->where('user_id', $id);
                })
                ->select('store_id','alias_name as store_name','user_id');
            //浦发
            $res[]=DB::table('pufa_stores')
                ->when($id, function ($query) use ($id) {
                    return $query->where('user_id', $id);
                })
                ->select('store_id','merchant_short_name as store_name','user_id');
            //平安
            $res[]=DB::table('pingan_stores')
                ->when($id, function ($query) use ($id) {
                    return $query->where('user_id', $id);
                })
                ->select('external_id as store_id', 'alias_name as store_name','user_id');
            //民生
            $res[]=DB::table('ms_stores')
                ->when($id, function ($query) use ($id) {
                    return $query->where('user_id', $id);
                })
                ->select('store_id','store_short_name as store_name','user_id');
            $result=self::checkEmpty($res);
            $list=empty($result)?'':$result->get();

            if($list)
                $list=self::shopNameEnd($list);
            return $list;
        }catch (Exception $e){
            die('获取收银员列表失败');
        }
    }
}