<?php
namespace App\Api\Controllers\Admin;
use App\Api\Transformers\PermissionTransformer;
use App\Models\Permission;

class PermissionController extends BaseController{
    //父类
    public function index(){
        $data=Permission::where('pid',0)->get();
        return $this->collection($data, new PermissionTransformer());
    }
    //子类
    public function ChildPermission(){
        $datas=Permission::where("pid","!=",0)->get();
        return $this->collection($datas, new PermissionTransformer());
    }
}
?>