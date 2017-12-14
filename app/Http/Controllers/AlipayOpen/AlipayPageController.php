<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2016/12/21
 * Time: 18:23
 */

namespace App\Http\Controllers\AlipayOpen;


use Alipayopen\Sdk\Request\AlipayPassInstanceAddRequest;
use Illuminate\Http\Request;

class AlipayPageController extends AlipayOpenController
{
    /**
     * 支付成功页面
     */
    public function PaySuccess(Request $request)
    {
       // $out_trade_no=$request->get("out_trade_no");
//        $ao=new AlipayOpenController();
//        $aop = $ao->AopClient();
//        $aop->method="alipay.pass.instance.add";
//        $requests = new AlipayPassInstanceAddRequest ();
//        $serialNumber=session("user_data")[0]->user_id.date("YmdHis");
//        $requests->setBizContent("{" .
//            "    \"tpl_id\":\"2017041720293314003454371\"," .
//            " \"recognition_type\": \"1\",".
//            " \"recognition_info\": {".
//           " \"partner_id\": \"2088521373760507\",".
//            " \"out_trade_no\": \"" .$out_trade_no."\"
//                },".
//            "\"tpl_params\": {
//            \"code\" : \"".$serialNumber."\",
//            \"channelID\": \"2016120503886618\",
//             \"serialNumber\":\"".$serialNumber."\"
//      }" .
//
//            "  }");
//        $result = $aop->execute($requests,null,"201704BBf5c1496fdf664af2b185db72d1ed4X50");
//        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
//        $resultCode = $result->$responseNode->code;
//        if(!empty($resultCode)&&$resultCode == 10000){
//
//            echo "成功";
//        } else {
//            echo "失败";
//        }
        $price=$request->get('price');
        return view('admin.alipayopen.page.paysuccess',compact('price'));

    }
    public function OrderErrors(Request $request){

        $code=$request->get('code');
        return view('admin.alipayopen.page.ordererrors',compact('code'));
    }

}