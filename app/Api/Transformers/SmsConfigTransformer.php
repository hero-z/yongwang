<?php
namespace App\Api\Transformers;

use App\Merchant;
use League\Fractal\TransformerAbstract;

class SmsConfigTransformer extends TransformerAbstract
{
    //映射允许输出字段
    public function transform(Merchant $Merchant)
    {
        return [
            'app_key' => $Merchant['app_key'],
            'app_secret' => $Merchant['app_secret'],
            'SignName' => $Merchant['SignName'],
            'TemplateCode' => $Merchant['TemplateCode']
        ];

    }

}