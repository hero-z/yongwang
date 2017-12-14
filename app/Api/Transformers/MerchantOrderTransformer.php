<?php
namespace App\Api\Transformers;

use App\Models\Order;
use League\Fractal\TransformerAbstract;

class MerchantOrderTransformer extends TransformerAbstract
{
    //映射允许输出字段
    public function transform(Order $newsModel)
    {
        return [
            'trade_no' => $newsModel['trade_no'],
            'out_trade_no' => $newsModel['out_trade_no'],
            'total_amount' => $newsModel['total_amount'],
            'status' => $newsModel['status'],
            'pay_status'=> $newsModel['pay_status'],
            'merchant_id' => $newsModel['merchant_id'],
            'type'=>$newsModel['type'],
            'created_at'=> $newsModel['created_at'],
        ];

    }

}