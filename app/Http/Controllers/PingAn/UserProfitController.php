<?php
/**
 * Created by PhpStorm.
 * User: hero
 * Date: 2017/7/3
 * Time: 17:52
 */

namespace App\Http\Controllers\PingAn;


use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayShopLists;
use App\Models\Order;
use App\Models\PinganStore;
use App\Models\PufaStores;
use App\Models\UnionPayStore;
use App\Models\WeBankStore;
use App\Models\WeixinShopList;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;

class UserProfitController
{
    public function orderToprofit($orderId,$profit,$serviceRate,$merchantRate){
        $order=Order::where('id',$orderId)->first();
        $msg='';
        $uid='';
        $istdata=[];
        $istdata['order_id']=$orderId;
        $istdata['service_id']=0;
        $istdata['agent_id']=0;
        $istdata['employee_id']=0;
        $istdata['service_rate']=$serviceRate;
        $istdata['merchant_rate']=$merchantRate;
        $istdata['agent_rate']=0;
        $istdata['employee_rate']=0;
        try{
            if($order){
                if($order->pay_status==1){
                    $store_id=$order->store_id;
                    $head = substr($store_id, 0, 1);
                    switch ($head) {
                        case 'o':
                            $store = AlipayAppOauthUsers::where('store_id', $store_id)->first();
                            $istdata['order_from']='olipay';
                            break;
                        case 's':
                            $store = AlipayShopLists::where('store_id', $store_id)->select('store_id');
                            $istdata['order_from']='slipay';
                            break;
                        case 'w':
                            $store = WeixinShopList::where('store_id', $store_id)->first();
                            $istdata['order_from']='weixin';
                            break;
                        case 'p':
                            $store = PinganStore::where('external_id', $store_id)->first();
                            $istdata['order_from']='pingan';
                            break;
                        case 'f':
                            $store = PufaStores::where('store_id', $store_id)->first();
                            $istdata['order_from']='pufa';
                            break;
                        case 'u':
                            $store = UnionPayStore::where('store_id', $store_id)->first();
                            $istdata['order_from']='unionpay';
                            break;
                        case 'm':
                            $store = DB::table('ms_stores')->where('store_id',$store_id)->first();
                            $istdata['order_from']='ms';
                            break;
                        case 'b':
                            $store = WeBankStore::where('store_id',$store_id)->first();
                            $istdata['order_from']='webank';
                            break;
                        default:
                            $store='';
                    }
                    if ($store) {
                        $uid=$store->user_id;
                    }
                    $user=User::where('id',$uid)->first();
                    if($user&&$uid){
                        $level=$user->level;
                        //区分层级
                        if($level==1){
                            $istdata['service_id']=$uid;
//                            $istdata['service_rate']=$user->rate;
                        }else if($level==2){
                            $parent=User::where('id',$user->pid)->first();
                            $istdata['agent_id']=$uid;
                            $istdata['agent_rate']=$user->rate;
                            if($parent){
                                $istdata['service_id']=$parent->id;
//                                $istdata['service_rate']=$parent->rate;
                            }
                        }else if($level==3){
                            $parent=User::where('id',$user->pid)->first();
                            $istdata['employee_id']=$uid;
                            $istdata['employee_rate']=$user->rate;
                            if($parent){
                                $istdata['agent_id']=$parent->id;
                                $istdata['agent_rate']=$parent->rate;
                                $gparent=User::where('id',$parent->pid)->first();
                                if($gparent){
                                    $istdata['service_id']=$gparent->id;
//                                    $istdata['service_rate']=$gparent->rate;
                                }
                            }
                        }
                        $profits=self::profitsplit($istdata['service_rate'],$istdata['agent_rate'],$istdata['employee_rate'],$istdata['merchant_rate'],$profit);
                        $istdata['total_profit']=$profit;
                        $istdata['service_profit']=$profits[0];
                        $istdata['agent_profit']=$profits[1];
                        $istdata['employee_profit']=$profits[2];
                        $istdata['status']=1;
                        $time=date('YmdHis');
                        $istdata['created_at']=$time;
                        $istdata['updated_at']=$time;
                        $ist=DB::table('user_profit')->insert($istdata);
                        if($ist){
                            return json_encode([
                                'code'=>1,
                                'msg'=>'SUCCESS',
                            ]);
                        }else{
                            $msg='分润入库失败';
                        }
                    }else{
                        $msg='未查询到归属员工,或者员工不存在';
                    }
                }else{
                    $msg='订单状态不符合';
                }
            }else{
                $msg='订单不存在';
            }
        }catch (Exception $e){
            Log::info($e->getMessage());
            $msg=$e->getMessage();
        }
        return json_encode([
            'code'=>0,
            'msg'=>$msg,
        ]);
    }
    public function profitsplit($serviceRate,$agentRate,$employeeRate,$merchantRate,$totalProfit){
        $service_profit=0;
        $agent_profit=0;
        $employee_profit=0;
        if($merchantRate>=$employeeRate&&$employeeRate>=$agentRate&&$agentRate>=$serviceRate&&$serviceRate>0){
            $prate=$merchantRate-$serviceRate;
            if($prate>0){
//                $service_profit=round($totalProfit*($agentRate-$serviceRate)/$prate,2);
                $agent_profit=round($totalProfit*($employeeRate-$agentRate)/$prate,2);
                $employee_profit=round($totalProfit*($merchantRate-$employeeRate)/$prate,2);
                $service_profit=$totalProfit-$employee_profit-$agent_profit;
            }else{
                //都是一个费率,服务商自己算
                $service_profit=round($totalProfit,2);
            }
        }elseif($employeeRate==0&&$merchantRate>=$agentRate&&$agentRate>=$serviceRate&&$serviceRate>0){
            //如果员工没有设置费率,代理商自己算
            $prate=$merchantRate-$serviceRate;
            if($prate>0){
//                $service_profit=round($totalProfit*($agentRate-$serviceRate)/$prate,2);
                $agent_profit=round($totalProfit*($merchantRate-$agentRate)/$prate,2);
                $service_profit=$totalProfit-$agent_profit;
            }else{
                //都是一个费率,服务商自己算
                $service_profit=round($totalProfit,2);
            }
        }else{
            //没有规则费率,服务商自己算
            $service_profit=round($totalProfit,2);
        }
        return [$service_profit,$agent_profit,$employee_profit];
    }
}