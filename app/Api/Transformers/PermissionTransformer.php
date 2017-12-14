<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/5/23
 * Time: 11:33
 */

namespace App\Api\Transformers;
use App\Models\Permission;
use League\Fractal\TransformerAbstract;

class PermissionTransformer extends TransformerAbstract
{

    //映射允许输出字段
    public function transform(Permission $newsModel)
    {
        return [
            'id' => $newsModel['id'],
            "pid"=> $newsModel['pid'],
            "name"=>$newsModel['name'],
            "display_name"=>$newsModel['display_name'],
        ];

    }

}