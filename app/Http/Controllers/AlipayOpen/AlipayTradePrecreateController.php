<?php

namespace App\Http\Controllers\AlipayOpen;

use Alipayopen\Sdk\Request\AlipayTradePrecreateRequest;
use App\Models\AlipayAppOauthUsers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Config;
/**预下单 生成固定二维码
 * Class AlipayTradePrecreateController
 * @package App\Http\Controllers
 */
class AlipayTradePrecreateController extends AlipayOpenController
{
    //会生成qr码
    public function TradePrecreateQrCode(Request $request)
    {
        $user_id=$request->get('user_id');//商户user_id
        try{
            $user=AlipayAppOauthUsers::where('user_id',$user_id)->first()->toArray();
            $app_auth_token=$user['app_auth_token'];
        }catch (\Exception $exception){
            echo '参数出错！请联系客服！';
        }
        /*
         *$total_amount=$request->get('total_amount');//总金额
       $subject=$request->get('subject',$user['auth_shop_name']);//订单标题
       $hb_fq_num=$request->get('hb_fq_num',3);//花呗分期数
       $hb_fq_seller_percent=$request->get('hb_fq_seller_percent',100);//花呗分期数*/
        //1.实例化公共参数
        $aop = $this->AopClient();
        $aop->method="alipay.trade.precreate";
        //2.调用接口
        $requests = new AlipayTradePrecreateRequest();
        $requests->setBizContent("{" .
            "    \"out_trade_no\":\"".'U'.date('YmdHis',time())."\"," .
            "    \"seller_id\":\"".$user_id."\"," .
            "    \"total_amount\":8," .
            "    \"extend_params\":{" .
            "      \"sys_service_provider_id\":\"".Config::get('alipayopen.pid')."\"," .
            "      \"hb_fq_num\":\"3\"," .//花呗分期数
            "      \"hb_fq_seller_percent\":\"100\"" .
            "    }," .
            "    \"discountable_amount\":8.88," .
            "    \"undiscountable_amount\":80," .
            "    \"subject\":\"Iphone6 16G\"," .
            "    \"body\":\"Iphone6 16G\"" .
            "  }");
        $result =$aop->execute($requests,NULL,$app_auth_token);
        dd($result);
        $qr=$result->alipay_trade_precreate_response;
        $code_url=$qr->qr_code;
        return view('admin.weixin.createorder', compact('code_url'));
        /*
    +"alipay_trade_precreate_response": {#171 ▼
    +"code": "10000"
    +"msg": "Success"
    +"out_trade_no": "20150320010101002"
    +"qr_code": "https://qr.alipay.com/bax00233srk2vl50llmg0040"
  }
  +"sign": "ah2O6zyU0+Li7fJAsLwvook5YvGf0Ea2Y4bKmFeESUZDedhf6I3rl1j15Sl158kHqIw5c7d9chxBNqtaWunw3gj85AYcpzGwe+42s00SU5tfkyI29NXO1lgtEzlBRZALR+uASo7RdXAfP6fs+2Y8RZNj5XIGqrDT5OJvbRZm2HI="*/
    }
}
