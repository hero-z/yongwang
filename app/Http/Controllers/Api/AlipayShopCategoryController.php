<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/2
 * Time: 17:51
 */

namespace App\Http\Controllers\Api;


use Alipayopen\Sdk\Request\AlipayOfflineMarketShopCategoryQueryRequest;
use App\Models\AlipayShopCategory;
use App\Models\ProvinceCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlipayShopCategoryController extends BaseController
{
    /**获取支付宝开放分类列表接口入数据库并更新
     * @param Request $request
     */
    public function query(Request $request)
    {
        $category_id = $request->get('category_id', '');//分类id 默认为空
        $aop = $this->AopClient();
        $aop->method = "alipay.offline.market.shop.category.query";
        $aop->version = "2.0";

        $requests = new AlipayOfflineMarketShopCategoryQueryRequest();
        $requests->setBizContent("{" .
            "    \"category_id\":\"$category_id\"," .
            "    \"op_role\":\"ISV\"" .
            "  }");
        try {
            $result = $aop->execute($requests);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            $shop_category_config_infos = $result->$responseNode->shop_category_config_infos;
            foreach ($shop_category_config_infos as $k => $v) {
                $category = AlipayShopCategory::where('category_id', $v->id)->first();
                //更新数据
                if ($category) {
                    $data = [
                        "category_id" => $v->id,
                        "category_name" => $v->nm,
                        "link" => $v->link,
                        "level" => $v->level
                    ];
                    $re = AlipayShopCategory::where('category_id', $v->id)->update($data);
                } else {
                    $data = [
                        "category_id" => $v->id,
                        "category_name" => $v->nm,
                        "link" => $v->link,
                        "level" => $v->level
                    ];
                    $re = AlipayShopCategory::create($data);
                }

            }
            echo '更新分类成功';

        } else {
            echo "失败";
        }
    }

    public function getCategory(Request $request){
        $category_id = $request->get('category_id', '');//分类id 默认为空
       if($category_id){
           $category = AlipayShopCategory::where('category_id',$category_id)->first();
       }else{
           $category = AlipayShopCategory::all();
       }
      return json_encode($category);
    }
    public function getNewCategory(Request $request){
        $category_id = $request->get('category_id', '');//分类id 默认为空
        if($category_id){
            $category = DB::table('pingan_category')->where('category_id',$category_id)->first();
        }else{
            $category = DB::table('pingan_category')->get();
        }
        return json_encode($category);
    }
}