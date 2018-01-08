<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2018/1/5
 * Time: 17:14
 */

namespace App\Http\Controllers\Statistics;


use App\Http\Controllers\Controller;
use App\Merchant;
use App\Models\Bill;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ManageController extends Controller
{
    protected $paylists= [101=>'官方翼支付机具'];
    protected $head=['店铺ID','代理商/ID','商户账号/ID','系统订单号','订单号','订单金额(元)','实收金额(元)','支付状态','退款金额(元)','支付类型','备注','更新日期'];
    protected $billstatusformat=[1=>'支付成功',
        2=>'等待支付',
        3=>'交易失败',
        4=>'订单作废',
        5=>'关闭交易'
    ];
    public function billQuery(Request $request)
    {
        $info='';
        try{
            $admin_id=$request->admin_id;
//            $merchant_id=$request->merchant_id;
            $paytype=$request->paytype;
            $paystatus=$request->paystatus;
            $time=$request->time;
            $time_start=$request->time_start;
            $time_end=$request->time_end;
            $export=$request->export;
            $total_amount=$request->total_amount;
            $where=[];
            $user=Auth::user();
            $paylist=$this->paylists;
            $users=$agents=[];
            $root=$user->hasRole('admin');
            if($root){
                $users=$this->getAgents();
            }else{
                $users=[$user->id=>$user->name];
                if(!$admin_id){
                    $admin_id=$user->id;
                }
            }
            if($admin_id){
                $where[]=['bills.admin_id',$admin_id];
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
            $collect=Bill::where($where);
            if($export){
                try{
                    $admininfos=User::pluck('name','id')->toArray();
                    $merchantinfos=Merchant::pluck('name','id')->toArray();
                    $head=$this->head;
                    $body=[$head];
                    $lists=$collect->limit(10000)->get();
                    if($lists){
                        foreach($lists as $k=>$v){
                            $adminstr=$merchantstr=$bill_statusstr=$paystr='';
                            if(array_key_exists($v->admin_id,$admininfos)){
                                $adminstr=$admininfos[$v->admin_id];
                            }
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
                                $adminstr.'/'.$v->admin_id,
                                $merchantstr.'/'.$v->merchant_id,
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
            $admins=$merchants=[];
            if($list){
                foreach ($list as $v) {
                    $admins[]=$v->admin_id;
                    $merchants[]=$v->merchant_id;
                }
                $admins=User::whereIn("id",$admins)->pluck('name',"id")->toArray();
                $merchants=Merchant::whereIn("id",$merchants)->pluck('name',"id")->toArray();
            }
            return view('statistics.billquery',compact('list','paylist','users','admins','merchants','admin_id','paytype','paystatus','count','time','time_start','time_end'));
        }catch (\Exception $e){
            $error=$e->getMessage();
            $line=$e->getLine();
            $info=$error.$line;
            Log::info($e);
        }
        return view('admin.webank.error',compact('info'));
    }
    /**获取代理商信息
     * @param null $adminId
     * @return array
     */
    protected function getAgents($adminId=null)
    {
        $agentsinfo=User::pluck('name','id')->toArray();
        return $agentsinfo;
    }
}