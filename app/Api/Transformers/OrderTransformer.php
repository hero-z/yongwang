<?php
namespace App\Api\Transformers;

use App\Models\MerchantOrders;
use League\Fractal\TransformerAbstract;

class NewsTransformer extends TransformerAbstract
{
    //映射允许输出字段
    public function transform(MerchantOrders $newsModel)
    {
        return [
            'out_trade_no' => $newsModel['out_trade_no'],
            'total_amount' => $newsModel['total_amount'],
        ];

    }

}