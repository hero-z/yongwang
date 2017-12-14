<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/5/23
 * Time: 11:50
 */

namespace App\Api\Controllers\Merchant;


use App\Api\Transformers\QuestionsTransformer;
use App\Models\Question;

class QuestionController extends BaseController
{

    public function getQuestions()
    {
        $user = $this->getMerchantInfo();
        $Questions = Question::all();
        return $this->collection($Questions, new QuestionsTransformer());

    }

}