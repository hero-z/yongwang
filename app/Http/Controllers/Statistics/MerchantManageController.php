<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2018/1/7
 * Time: 18:19
 */

namespace App\Http\Controllers\Statistics;


use App\Http\Controllers\Controller;
use App\Merchant;
use App\Models\Bill;
use App\Models\Paipai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class MerchantManageController extends Controller
{
    protected $paylists= [101=>'官方翼支付机具'];
    protected $head=['店铺ID','商户账号/ID','设备号','系统订单号','订单号','订单金额(元)','实收金额(元)','支付状态','退款金额(元)','支付类型','备注','更新日期'];
    protected $billstatusformat=[1=>'支付成功',
        2=>'等待支付',
        3=>'交易失败',
        4=>'订单作废',
        5=>'关闭交易'
    ];
    public function query(Request $request)
    {
        $wheresql=[];
        $paystatus=$request->paystatus;
        if(!$paystatus){
            $paystatus=1;
        }
        if($paystatus!=9){
            $wheresql[]=['pay_status',$paystatus];
        }
        $merchant=Auth::guard('merchant')->user();
        $merchantids=[];
        if($merchant->pid==0){
            $merchantids=Merchant::where("pid",$merchant->id)->pluck("id")->toArray();
        }
        $bills=Bill::where('type',101)
            ->where($wheresql)
            ->when(1,function ($query)use($merchant,$merchantids){
                if($merchant->pid==0&&!empty($merchantids)){
                    $merchantids[]=$merchant->id;
                    return $query->whereIn('merchant_id',$merchantids);
                }else{
                    return $query->where('merchant_id',$merchant->id);
                }
            })->orderBy('updated_at','desc')->paginate(8);
        $merchants=[];
        if($bills){
            foreach ($bills as $v) {
                $merchants[]=$v->merchant_id;
            }
            $merchants=Merchant::whereIn("id",$merchants)->pluck('name',"id")->toArray();
        }
        return view('merchant.bestpay.bill',compact('merchants','paystatus','bills'));
    }

    public function billQuery(Request $request)
    {
        $info='';
        try{
            $merchant_id=$request->merchant_id;
            $device_no=$request->device_no;
            $paytype=$request->paytype;
            $paystatus=$request->paystatus;
            $time=$request->time;
            $time_start=$request->time_start;
            $time_end=$request->time_end;
            $export=$request->export;
            $total_amount=$request->total_amount;
            $where=[];
            $user=Auth::guard('merchant')->user();
            $paylist=$this->paylists;
            $users=[];
            if($user->pid==0){
                $users=Merchant::where("pid",$user->id)->pluck('name',"id")->toArray();
                $users[$user->id]=$user->name;
            }else{
                $users=[$user->id=>$user->name];
            }
            $devices=Paipai::whereIn('m_id',array_keys($users))->pluck('name','device_no')->toArray();
            if($device_no){
                $where[]=['bills.device_no',$device_no];
            }
            //快速日期选择
            $timee=$times='';
            if($time){
                switch ($time){
                    case 1:
                        $times=date('Y-m-d' . ' ' . ' 23:59:59', strtotime('-7 day'));
                        $timee=date('Y-m-d H:i:s',time());
                        break;
                    case 2:
                        $times=date('Y-m-d' . ' ' . ' 00:00:00',time());
                        $timee=date('Y-m-d H:i:s',time());
                        break;
                    case 3:
                        $times=date('Y-m-d' . ' ' . ' 00:00:00', strtotime('-1 day'));
                        $timee=date('Y-m-d' . ' ' . ' 23:59:59', strtotime('-1 day'));
                        break;
                    case 4:
                        $firstday = date("Y-m-01" . ' ' . ' 00:00:00',time());
                        $lastday = date("Y-m-d H:i:s",strtotime("$firstday +1 month"));
                        $times=$firstday;
                        $timee=$lastday;
                        break;
                    case 5:
                        $firstday = date("Y-m-01" . ' ' . ' 00:00:00',time());
                        $lastday = date("Y-m-d H:i:s",strtotime("$firstday -1 month"));
                        $times=$lastday;
                        $timee=$firstday;
                        break;
                }
            }
            //时间搜索
            if($time_start)
            {
                $times=date("Y-m-d H:i:s",strtotime($time_start));
            }
            if($time_end)
            {
                $timee=date("Y-m-d H:i:s",strtotime($time_end));
            }
            if($times){
                $where[]=['bills.updated_at','>',$times];
            }
            if($timee){
                $where[]=['bills.updated_at','<',$timee];
            }
            //是否有订单状态搜索
            if($paystatus){
                if($paystatus=="9"){
                }else{
                    $where[]=['bills.pay_status',$paystatus];
                }
            }else{
                $where[]=['bills.pay_status',1];
            }
            //支付方式
            if($paytype){
                $where[]=['bills.type',$paytype];
            }
            $collect=Bill::where($where)->when(1,function ($query)use($merchant_id,$user,$users){
                if($merchant_id){
                    return $query->where('merchant_id',$merchant_id);
                }else{
                    if($user->pid==0&&!empty($merchantids)){
                        $merchantids=array_keys($users);
                        $merchantids[]=$user->id;
                        return $query->whereIn('merchant_id',$merchantids);
                    }else{
                        return $query->where('merchant_id',$user->id);
                    }
                }
            });
            if($export){
                try{
                    $merchantinfos=Merchant::pluck('name','id')->toArray();
                    $head=$this->head;
                    $body=[$head];
                    $lists=$collect->limit(10000)->get();
                    if($lists){
                        foreach($lists as $k=>$v){
                            $merchantstr=$bill_statusstr=$paystr='';
                            if(array_key_exists($v->merchant_id,$merchantinfos)){
                                $merchantstr=$merchantinfos[$v->merchant_id];
                            }
                            if(array_key_exists($v->pay_status,$this->billstatusformat)){
                                $bill_statusstr=$this->billstatusformat[$v->pay_status];
                            }
                            if(array_key_exists($v->type,$this->paylists)){
                                $paystr=$this->paylists[$v->type];
                            }
                            $body[]=[
                                $v->store_id,
                                $merchantstr.'/'.$v->merchant_id,
                                $v->device_no,
                                $v->out_trade_no,
                                $v->trade_no,
                                $v->total_amount,
                                $v->receipt_amount,
                                $bill_statusstr,
                                $v->refund_amount,
                                $paystr,
                                $v->remark_str,
                                $v->updated_at
                            ];
                        }
                    }
                    $cellData = $body;
                    Excel::create(iconv('utf-8','gbk',date('Y-m-d日').'账单统计'),function($excel) use ($cellData){
                        $excel->sheet('score', function($sheet) use ($cellData){
                            $sheet->rows($cellData);
                        });
                    })->export('xls');
                }catch (\Exception $e){
                    die('导出数据失败');
                }
            }
            if($total_amount){
                //统计金额
                $total=round($collect->sum('total_amount'),2);
                $receipt=round($collect->sum('receipt_amount'),2);
                return json_encode([
                    'success'=>1,
                    'data'=>[$total,$receipt],
                ]);
            }
            $count=$collect->count('bills.id');
            $list=$collect
                ->orderBy('bills.updated_at','DESC')
                ->paginate(8);
            $merchants=[];
            if($list){
                foreach ($list as $v) {
                    $merchants[]=$v->merchant_id;
                }
                $merchants=Merchant::whereIn("id",$merchants)->pluck('name',"id")->toArray();
            }
            return view('statistics.merchantbillquery',compact('list','paylist','users','devices','device_no','merchants','merchant_id','paytype','paystatus','count','time','time_start','time_end'));
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
            $info=$error.$line;
            Log::info($e);
        }
        return view('admin.webank.error',compact('info'));
    }
}