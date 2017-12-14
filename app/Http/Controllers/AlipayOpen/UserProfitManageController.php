<?php
/**
 * Created by PhpStorm.
 * User: hero
 * Date: 2017/7/4
 * Time: 15:38
 */

namespace App\Http\Controllers\AlipayOpen;



use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\CountValidator\Exception;
use Symfony\Component\HttpFoundation\Request;

class UserProfitManageController
{
    public function userprofit(Request $request){
        $level=Auth::user()->level;
        $where=[];
        $users=[];
        $time=$request->time;
        $time_start=$request->time_start;
        $time_end=$request->time_end;
        //初始化时间
        if(!$time_start&&!$time_end&&!$time){
            $time=1;
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
            $where[]=['user_profit.updated_at','>',$times];
        }
        if($time_end)
        {
            $timee=date("Y-m-d H:i:s",strtotime($time_end));
            $where[]=['user_profit.updated_at','<',$timee];
        }
        $time=$request->time;
        $time_start=$request->time_start;
        $time_end=$request->time_end;
        if($level==1&&Auth::user()->can('userprofit')){
            if(!Auth::user()->hasRole('admin')){
                $where[]=['user_profit.service_id',Auth::user()->id];
            }
            $list=DB::table('user_profit')
                ->join('orders','orders.id','user_profit.order_id')
                ->where($where)
                ->select('orders.total_amount','orders.out_trade_no','orders.pay_status','user_profit.service_id','user_profit.agent_id','user_profit.employee_id','user_profit.service_rate','user_profit.agent_rate','user_profit.employee_rate','user_profit.merchant_rate','user_profit.total_profit','user_profit.service_profit','user_profit.agent_profit','user_profit.employee_profit','user_profit.order_from','user_profit.updated_at','user_profit.status');
        }elseif($level==2){
            $list=DB::table('user_profit')
                ->join('orders','orders.id','user_profit.order_id')
                ->where($where)
                ->where('user_profit.agent_id',Auth::user()->id)
                ->select('orders.total_amount','orders.out_trade_no','orders.pay_status','user_profit.service_id','user_profit.agent_id','user_profit.employee_id','user_profit.service_rate','user_profit.agent_rate','user_profit.employee_rate','user_profit.merchant_rate','user_profit.total_profit','user_profit.service_profit','user_profit.agent_profit','user_profit.employee_profit','user_profit.order_from','user_profit.updated_at','user_profit.status');
        }else{
            $list=DB::table('user_profit')
                ->join('orders','orders.id','user_profit.order_id')
                ->where($where)
                ->where('user_profit.employee_id',Auth::user()->id)
                ->select('orders.total_amount','orders.out_trade_no','orders.pay_status','user_profit.service_id','user_profit.agent_id','user_profit.employee_id','user_profit.service_rate','user_profit.agent_rate','user_profit.employee_rate','user_profit.merchant_rate','user_profit.total_profit','user_profit.service_profit','user_profit.agent_profit','user_profit.employee_profit','user_profit.order_from','user_profit.updated_at','user_profit.status');

        }
        $userlists = DB::table('users')->select('id','name')->get();
        if(!$userlists->isEmpty())
        {
            foreach($userlists as $user)
            {
                $users[$user->id]=$user->name;
            }
        }
        $counts=$list->count();
        $list=$list->orderby('user_profit.updated_at','desc')->paginate(8);
        return view('admin.profit.list',compact('list','users','counts','time','time_start','time_end'));
    }
    public function profitsplit(Request $request){
        $level=Auth::user()->level;
        $uid=Auth::user()->id;
        $where=[];
        $users=[];
        $time=$request->time;
        $time_start=$request->time_start;
        $time_end=$request->time_end;
        //初始化时间
        if(!$time_start&&!$time_end&&!$time){
            $time=1;
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
            $where[]=['user_profit.updated_at','>',$times];
        }
        if($time_end)
        {
            $timee=date("Y-m-d H:i:s",strtotime($time_end));
            $where[]=['user_profit.updated_at','<',$timee];
        }
        $time=$request->time;
        $time_start=$request->time_start;
        $time_end=$request->time_end;
        $list=[];
        if($level==1){
            if(!Auth::user()->hasRole('admin')){
                $where[]=['user_profit.service_id',$uid];
            }
            $user=User::where('level',1)
                ->when(!Auth::user()->hasRole('admin'),function($q)use($uid){
                    return $q->where('id',$uid);
                })
                ->get();
            if($user){
                foreach ($user as $v) {
                    $res=self::getprofit($v->id,$v->level,$where);
                    if($res){
                        $total=round($res->sum('total_profit'),2);
                        $self=round($res->sum('service_profit'),2);
                        $count=($res->count());
                        $list[0][$v->id]=['total'=>$total,'self'=>$self,'count'=>$count,'name'=>$v->name];
                        $children=User::where('level',2)->where('pid',$v->id)->get();
                        if($children){
                            foreach ($children as $vv) {
                                $ress=self::getprofit($vv->id,$vv->level,$where);
                                if($ress){
                                    $totals=round($ress->sum('total_profit')-$ress->sum('service_profit'),2);
                                    $selfs=round($ress->sum('agent_profit'),2);
                                    $counts=($ress->count());
                                    $list[1][$v->id."**".$vv->id]=['total'=>$totals,'self'=>$selfs,'count'=>$counts,'name'=>$vv->name];
                                }else{
                                    $list[1][$v->id."**".$vv->id]=['total'=>0,'self'=>0,'count'=>0,'name'=>$vv->name];
                                }
                                $gchildren=User::where('level',3)->where('pid',$vv->id)->get();
                                if($gchildren){
                                    foreach ($gchildren as $vvv) {
                                        $resss=self::getprofit($vvv->id,$vvv->level,$where);
                                        if($resss){
                                            $totalss=round($resss->sum('employee_profit'),2);
                                            $selfss=round($resss->sum('employee_profit'),2);
                                            $countss=($resss->count());
                                            $list[2][$vv->id."**".$vvv->id]=['total'=>$totalss,'self'=>$selfss,'count'=>$countss,'name'=>$vvv->name];
                                        }else{
                                            $list[2][$vv->id."**".$vvv->id]=['total'=>0,'self'=>0,'count'=>0,'name'=>$vvv->name];
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $list[0][$v->id]=['total'=>0,'self'=>0,'count'=>0,'name'=>$v->name];
                    }

                }
            }
        }elseif($level==2){
            $user=User::where('level',2)->where('id',$uid)->get();
            if($user){
                foreach ($user as $v) {
                    $res=self::getprofit($v->id,$v->level,$where);
                    if($res){
                        $total=round($res->sum('total_profit')-$res->sum('service_profit'),2);
                        $self=round($res->sum('agent_profit'),2);
                        $count=($res->count());
                        $list[0][$v->id]=['total'=>$total,'self'=>$self,'count'=>$count,'name'=>$v->name];
                        $children=User::where('level',3)->where('pid',$v->id)->get();
                        if($children){
                            foreach ($children as $vv) {
                                $ress=self::getprofit($vv->id,$vv->level,$where);
                                if($ress){
                                    $totals=round($ress->sum('employee_profit'),2);
                                    $selfs=round($ress->sum('employee_profit'),2);
                                    $counts=($ress->count());
                                    $list[1][$v->id."**".$vv->id]=['total'=>$totals,'self'=>$selfs,'count'=>$counts,'name'=>$vv->name];
                                }else{
                                    $list[1][$v->id."**".$vv->id]=['total'=>0,'self'=>0,'count'=>0,'name'=>$vv->name];
                                }
                            }
                        }
                    }else{
                        $list[0][$v->id]=['total'=>0,'self'=>0,'count'=>0,'name'=>$v->name];
                    }

                }
            }
        }else{
            $res=self::getprofit($uid,$level,$where);
            if($res){
                $total=round($res->sum('employee_profit'),2);
                $self=round($res->sum('employee_profit'),2);
                $count=($res->count());
                $list[0][$uid]=['total'=>$total,'self'=>$self,'count'=>$count,'name'=>Auth::user()->name];
            }else{
                $list[0][$uid]=['total'=>0,'self'=>0,'count'=>0,'name'=>Auth::user()->name];
            }
        }
        return view('admin.profit.split',compact('list','time','time_start','time_end'));
    }
    public function getprofit($uid,$level,$where){
        try{
            if($level==1){
                $mark='service_id';
            }elseif($level==2){
                $mark='agent_id';
            }else{
                $mark='employee_id';
            }
            $profits=DB::table('user_profit')->where($mark,$uid)->where($where)->select('total_profit','service_profit','agent_profit','employee_profit');
            return $profits;
        }catch (Exception $e){
            throw $e;
        }
    }
}