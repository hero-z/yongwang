<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/4/6
 * Time: 15:07
 */

namespace App\Http\Controllers\Api;
use App\Merchant;
use App\Models\SmsConfig;
use Illuminate\Http\Request;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Support\Facades\DB;
class SmsController extends BaseController
{
    //发送短信
    public function send(Request $request)
    {
        $phone = $request->get('phone');
        $product = Merchant::where('phone', $phone)->firsr()->name;
        $code = rand(100000, 999999);
        Cache::put($phone, $code, 1);
        $client = $this->AopCient();
        $req = new AlibabaAliqinFcSmsNumSend;
        $config = SmsConfig::where('id', 1)->first();
        if ($config) {
            $req->setRecNum($phone)
                ->setSmsParam([
                    'code' => $code,
                    'product' => $product,
                ])
                ->setSmsFreeSignName($config->SignName);
            $req->setSmsTemplateCode($config->TemplateCode);
            $return=$client->execute($req);
           dd($return);

        } else {
            throw new BadRequestHttpException('配置信息不存在');
        }

    }
    public function setSms(){
        $auth = Auth::user()->can('setSms');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        try{
            $list=DB::table("sms_configs")->first();
        }catch(\Exception $exception){

        }
      return view("app.setSms",compact("list"));
    }
    public function updateSms(Request $request){
        $data['app_key']=$request->get("app_key");
        $data['app_secret']=$request->get("app_secret");
        $data['SignName']=$request->get("SignName");
        $data['TemplateCode']=$request->get("TemplateCode");
        $id=$request->get("id");
        if(DB::table("sms_configs")->where("id",$id)->update($data)){
            return json_encode(['status' => 1]);
        }
    }
}