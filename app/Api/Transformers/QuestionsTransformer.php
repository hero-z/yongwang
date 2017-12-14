<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/5/23
 * Time: 11:33
 */

namespace App\Api\Transformers;


use App\Models\Question;
use League\Fractal\TransformerAbstract;

class QuestionsTransformer extends TransformerAbstract
{

    //映射允许输出字段
    public function transform(Question $newsModel)
    {
        return [
            'category_id' => $newsModel['category_id'],
            'title' => $newsModel['title'],
            'summary' => $newsModel['summary'],
            'content'=> $newsModel['content'],
            'user_id' => $newsModel['user_id'],
        ];

    }

}