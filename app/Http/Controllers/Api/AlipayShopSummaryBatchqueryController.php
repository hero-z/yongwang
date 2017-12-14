<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/11
 * Time: 17:30
 */

namespace App\Http\Controllers\Api;


use Alipayopen\Sdk\Request\AlipayOfflineMarketShopBatchqueryRequest;
use Alipayopen\Sdk\Request\AlipayOfflineMarketShopSummaryBatchqueryRequest;
use Illuminate\Http\Request;

class AlipayShopSummaryBatchqueryController extends BaseController
{

    public function index(Request $request)
    {
        $shop_id = $request->get('shop_id', '');
        $shop_status = $request->get('shop_status', '');
        $page_no = $request->get('page_no', 20);
        $page_size = $request->get('page_size', 100);
        $op_role = $request->get('page_size', 'ISV');
        $query_type = $request->get('query_type', 'BRAND_RELATION');
        $related_partner_id = $request->get('related_partner_id');
        $aop = $this->AopClient();
        $requests = new AlipayOfflineMarketShopSummaryBatchqueryRequest();
        $requests->setBizContent("{" .
            "    \"op_role\":\"" . $op_role . "\"," .
            "    \"query_type\":\"" . $query_type . "\"," .
            "    \"related_partner_id\":\"" . $related_partner_id . "\"," .
            //  "    \"shop_id\":\"".$shop_id."\"," .
            //"    \"shop_status\":\"".$shop_status."\"," .
            "    \"page_no\":" . $page_no . "," .
            "    \"page_size\":" . $page_size . "" .
            "  }");
        $result = $aop->execute($requests);
        dd($result);
        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            echo "成功";
        } else {
            echo "失败";
        }
    }

  //查询商户审核通过的门店编号列表
    public function batchquery(Request $request)
    {
     $page_no=$request->get('page_no',"1");
     $aop=$this->AopClient();
        $requests = new AlipayOfflineMarketShopBatchqueryRequest();
        $requests->setBizContent("{" .
            "    \"page_no\":\"".$page_no."\"" .
            "  }");
        $result = $aop->execute($requests);
        dd($result);
        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            echo "成功";
        } else {
            echo "失败";
        }
    }
    //(业务流水批量查询接口
}