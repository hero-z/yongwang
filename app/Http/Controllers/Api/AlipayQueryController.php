<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/11
 * Time: 17:30
 */

namespace App\Http\Controllers\Api;


use Alipayopen\Sdk\Request\AlipayOfflineMarketApplyorderBatchqueryRequest;
use Alipayopen\Sdk\Request\AlipayOfflineMarketShopBatchqueryRequest;
use Alipayopen\Sdk\Request\AlipayOfflineMarketShopQuerydetailRequest;
use Alipayopen\Sdk\Request\AlipayOfflineMarketShopSummaryBatchqueryRequest;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Null_;

class AlipayQueryController extends BaseController
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
        $aop->method = "alipay.offline.market.shop.summary.batchquery";
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
        $page_no = $request->get('page_no', 1);
        $aop = $this->AopClient();
        $aop->method = "alipay.offline.market.shop.batchquery";
        $requests = new AlipayOfflineMarketShopBatchqueryRequest();
        $requests->setBizContent("{" .
            "    \"page_no\":\"1\"" .
            "  }");
        $result = $aop->execute($requests);
        echo '<pre>';
        var_dump($result);
        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            echo "成功";
        } else {
            echo "失败";
        }
    }

    //(业务流水批量查询接口)
    public function ApplyOrderBatchQuery(Request $request)
    {
        $apply_ids = $request->get('apply_ids', '');
        $biz_type = $request->get('biz_type', 'SHOP');
        $op_role = $request->get('biz_type', 'ISV');
        $aop = $this->AopClient();
        $aop->method = "alipay.offline.market.applyorder.batchquery";
        $requests = new AlipayOfflineMarketApplyorderBatchqueryRequest();
        $requests->setBizContent("{" .
            "    \"biz_type\":\"SHOP\"," .
            "    \"op_role\":\"ISV\"," .
            "    \"op_id\":\"2088521296141762\"," .
            "    \"page_no\":1," .
            "    \"page_size\":50" .
            "  }");
        $result = $aop->execute($requests);
        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        return $result->$responseNode->biz_order_infos;
    }

    //查询单个门店信息接口查询
    public function ShopQueryDetail(Request $request)
    {
        $shop_id = $request->get('shop_id');
        if ($shop_id) {
            $aop = $this->AopClient();
            $aop->method = "alipay.offline.market.shop.querydetail";
            $requests = new AlipayOfflineMarketShopQuerydetailRequest();
            $requests->setBizContent("{" .
                "\"shop_id\":\"" . $shop_id . "\"" .
                "  }");
            $result = $aop->execute($requests, Null, '201612BBebd79b6493aa44219c18dd0074fc4X82');//需要app_auth_token
            dd($result);
        }

    }
}