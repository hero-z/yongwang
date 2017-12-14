<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/4/6
 * Time: 15:07
 */

namespace App\Api\Controllers\Sms;


use App\Merchant;
use App\Models\SmsCode;
use App\Models\SmsConfig;
use Illuminate\Http\Request;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SmsController extends BaseController
{
    //发送
    public function send(Request $request)
    {
        $phone = $request->get('phone');
        $product = Merchant::where('phone', $phone)->first();
        if ($product) {
            $product = $product->name;
        } else {
            return json_encode([
                'message' => '商户信息不存在',
                'status_code' => 0
            ]);
        }
        $code = rand(100000, 999999);
        //存库
        SmsCode::where('code', $phone)->delete();
        SmsCode::create([
            'code' => $phone,
            'value' => $code,
        ]);
        $client = $this->AopCient();
        $req = new AlibabaAliqinFcSmsNumSend;
        $config = SmsConfig::where('id', 1)->first();
        if ($config) {
            $req->setRecNum($phone)
                ->setSmsParam([
                    'code' => $code,
                    'product' => '店铺:' . $product . '的',
                ])
                ->setSmsFreeSignName($config->SignName);
            $req->setSmsTemplateCode($config->TemplateCode);
            $return = $client->execute($req);
            try {
                if ($return->result->success) {
                    return json_encode([
                        'message' => '发送成功',
                        'status_code' => 1
                    ]);
                }
            } catch (\Exception $exception) {
                return json_encode([
                    'message' => $return->sub_msg,
                    'status_code' => 0
                ]);
            }
        } else {
            throw new BadRequestHttpException('配置信息不存在');
        }

    }
}