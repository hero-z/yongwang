<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2016/12/4
 * Time: 14:58
 */

namespace App\Http\Controllers\Api;


use App\Models\ProvinceCity;
use Illuminate\Http\Request;

class ProvinceCityController extends BaseController
{

    //获得数据库里面的省的信息的接口
    public function getProvince()
    {
        $Province = ProvinceCity::where("areaParentId", 1)->get()->toArray();
        return json_encode($Province);
    }
    //获得数据库里面的市的信息的接口
    public function getCity(Request $request){
        $areaCode=$request->get('areaCode');//
        $city=ProvinceCity::where("areaParentId",$areaCode)->get()->toArray();
        return json_encode($city);
    }
}