<?php
/**
 * Created by PhpStorm.
 * User: hero
 * Date: 2017/4/12
 * Time: 18:56
 */
namespace App\Http\Controllers\WxCard;



use App\Models\PageSets;
use App\Models\WeixinCardMerchant;
use App\Models\WeixinPayConfig;
use App\Models\WeixinPayNotify;
use App\Models\WXNotify;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Mockery\CountValidator\Exception;
use Symfony\Component\HttpFoundation\Request;

class WxCardController extends BaseController{
    public function index(){

        $datapage=WeixinCardMerchant::paginate(9);
        return view('admin.weixin.card.list',compact('datapage'));
    }

    public function operate(){

//        $app=$this->WxCard();
//        $card=$app->card;
//        $cardid=self::createCard($app)['card_id'];
        //领取单张卡券
//        $cards = [
//            'action_name' => 'QR_CARD',
//            'expire_seconds' => 1800,
//            'action_info' => [
//                'card' => [
//                    'card_id' => 'ppnT0syk7ES38Sa7xv2YiQvq18lY',
//                    'is_unique_code' => false,
//                    'outer_id' => 1,
//                ],
//            ],
//        ];
//        $result = $card->QRCode($cards);


//        $cardId = 'ppnT0syk7ES38Sa7xv2YiQvq18lY';
//        $result = $card->getDepositedCount($cardId);


//        $info=self::uploadsImage($app,public_path('uploads'.'/test.jpg'),2);


//        $result=$card->getCategories();
//        dd($result);
//        $data=json_decode('{"merchant_id":417226211,"create_time":1492408915,"update_time":1492408915,"brand_name":"\\u6d4b\\u8bd52","logo_url":"http:\/\/mmbiz.qpic.cn\/mmbiz_jpg\/wpdbfRcxTibP2U2n2S6jJyB1HuMm09uUiaPOdyjr87libVKxibsfSeUQicCzIbQgiahVXM7g2wsH7G4s51hkk6ibhWrbQ\/0?","status":"CHECKING","begin_time":1492408915,"end_time":1523894400,"primary_category_id":3,"secondary_category_id":318}');
//        $updata=[];
//        foreach($data as $k=>$v){
//            if($k=='create_time'){
//                $updata['created_at']=date('Y-m-d H:i:s',$v);
//                continue;
//            }
//            if($k=='update_time'){
//                $updata['updated_at']=date('Y-m-d H:i:s',$v);
//                continue;
//            }
//            if($k=='begin_time'){
//                $updata['begin_time']=date('Y-m-d H:i:s',$v);
//                continue;
//            }
//            if($k=='end_time'){
//                $updata['end_time']=date('Y-m-d H:i:s',$v);
//                continue;
//            }
//            $updata[$k]=$v;
//        }
//        $result=DB::table('weixin_card_merchant')->insert($updata);
//        $app=$this->WxCard();
//        $accresstoken=$app->access_token->getToken();
//        $url='https://api.weixin.qq.com/card/submerchant/submit?access_token='.$accresstoken;
//        $info=json_encode(['media_id'=>'jlcdCpoNosGqSZ_oNIo8vxVzanG0LR08L8TYAYsfLDg']);
//        dd($info);
//        $result=$this->http_post($url,$info);

//        $accresstoken=$app->access_token->getToken();
//        $url='https://api.weixin.qq.com/cgi-bin/material/get_material?access_token='.$accresstoken;
//        $info=json_encode(['media_id'=>'jlcdCpoNosGqSZ_oNIo8vwrgm7UIBKUuPG4xv0-218Q']);
////        dd($info);
//        $result=$this->http_post($url,$info);
//
//        $material=$app->material;
//        $result=$material->get('jlcdCpoNosGqSZ_oNIo8vwrgm7UIBKUuPG4xv0-218Q');
//        dd($result);


        $config = WeixinPayConfig::where('id', 1)->first();//微信支付参数 app_id
        $WeixinPayNotifyStore = WeixinPayNotify::where('store_id', 'w1419589702')->first();
        $options = [
            'app_id' => $config->app_id,
            'secret' => $config->secret,
            'token' => '18851186776',
            'payment' => [
                'merchant_id' => $config->merchant_id,
                'key' => $config->key,
                'cert_path' => $config->cert_path, // XXX: 绝对路径！！！！
                'key_path' => $config->key_path,      // XXX: 绝对路径！！！！
                'notify_url' => $config->notify_url,       // 你也可以在下单时单独设置来想覆盖它
            ],
        ];
        $app = new Application($options);
        $userService = $app->user;
        $user = $userService->get('opnT0s8pVcxE_TOUyeuCuwsuPIEo');//买家open_id
        dd($user);
        $template = PageSets::where('id', 1)->first();
        $notice = $app->notice;
        $userIds = $WeixinPayNotifyStore->receiver;
        $open_ids = explode(",", $userIds);
        $templateId = $template->string1;
        $url = $WeixinPayNotifyStore->linkTo;
        $color = $WeixinPayNotifyStore->topColor;
        $andData = array(
            "keyword1" => 10,
//                                    "keyword2" => '微信(' . $user->nickname . ')',
            "keyword2" => '微信支付',
            "keyword3" => '2017-4-26 00:00:00',
            "keyword4" => '11111111111',
            "remark" => '祝' . $WeixinPayNotifyStore->store_name . '生意红火',
        );

        foreach ($open_ids as $v) {
            $s = WXNotify::where('open_id', $v)->where('store_id', 'w1419589702')->first();
            if ($s) {
                if ($s->status) {
                    try {
                        $notice->uses($templateId)->withUrl($url)->andData($andData)->andReceiver($v)->send();
                    } catch (\Exception $exception) {
                        Log::info($exception);
                        continue;
                    }
                }
            } else {
                continue;
            }
        }
    }
    //子商户列表
    public function addsubmerchant(){
        $app=$this->WxCard();
        $card=$app->card;
        $category=$card->getCategories();
        return view('admin.weixin.card.addsubmerchant',compact('category','secondv'));
    }
    //删除子商户
    public function delsubmerchant(Request $request){
        try{
            $info=WeixinCardMerchant::where('id',$request->id)->first();
            $logo_url_local=$info->logo_url_local;
            $logo_url_mediaid=$info->logo_url_mediaid;
            $protocol=$info->protocol;
            $license=$info->license;
            $idcard=$info->idcard;
            //删除素材
            $app=$this->WxCard();
            $material = $app->material;
            $material->delete($logo_url_mediaid);
            //删除本地图片
            unlink(public_path().$logo_url_local);
            unlink(public_path().$protocol);
            if($license)
                unlink(public_path().$license);
            if($idcard)
                unlink(public_path().$idcard);
            WeixinCardMerchant::where('id',$request->id)->delete();
        }catch(Exception $e){

        }
        return json_encode([

        ]);

    }
    //生成子商户
    public function postmerchantdata(Request $request){
        $brand_name=$request->brand_name;
        $app_id=$request->app_id;
        $logo_url_local=$request->logo_url;
        $protocol=$request->protocol;
        $license=$request->agreement_media_id;
        $idcard=$request->operator_media_id;
        $end_time=strtotime($request->end_time);
        $primary_category_id=$request->primary_category_id;
        $secondary_category_id=$request->secondary_category_id;
        $type=$request->type;
        //上传微信服务器
        $app=$this->WxCard();
        try{
            $data['brand_name']=$brand_name;

//            logo_url
            $path=public_path().$logo_url_local;
            $result=self::uploadsImage($app,$path,1);
            $logo_url_mediaid=$result['media_id'];
            $logo_url=$result['url'];
            $data['logo_url']=$logo_url;
            //protocol
            $path=public_path().$protocol;
            $result=self::uploadsImage($app,$path,2);
            $data['protocol']=$result['media_id'];
            $data['end_time']=$end_time;
            $data['primary_category_id']=$primary_category_id;
            $data['secondary_category_id']=$secondary_category_id;
            if($type=='2'){
                $path=public_path().$license;
                $result=self::uploadsImage($app,$path,2);
                $data['agreement_media_id']=$result['media_id'];

                $path=public_path().$idcard;
                $result=self::uploadsImage($app,$path,2);
                $data['operator_media_id']=$result['media_id'];
            }
            if($app_id){
                $data['app_id']=$app_id;
            }

            $result=self::createSubMerchantlocal(json_encode(['info'=>$data]));
            $reg='/\{.*[\n]*.*\}/';
            preg_match($reg,$result,$result);
            $responseresult=json_decode($result[0]);
            if(!$responseresult->errcode){
                $data=$responseresult->info;
                $updata=[];
                foreach($data as $k=>$v){
                    if($k=='create_time'){
                        $updata['created_at']=date('Y-m-d H:i:s',$v);
                        continue;
                    }
                    if($k=='update_time'){
                        $updata['updated_at']=date('Y-m-d h:i:s',$v);
                        continue;
                    }
                    if($k=='begin_time'){
                        $updata['begin_time']=date('Y-m-d H:i:s',$v);
                        continue;
                    }
                    if($k=='end_time'){
                        $updata['end_time']=date('Y-m-d H:i:s',$v);
                        continue;
                    }
                    $updata[$k]=$v;
                }
                $id=WeixinCardMerchant::insertGetId($updata);
                $updatalocal=[
                    'brand_name'=>$brand_name,
                    'app_id'=>$app_id,
                    'logo_url'=>$logo_url,
                    'logo_url_local'=>$logo_url_local,
                    'logo_url_mediaid'=>$logo_url_mediaid,
                    'license'=>$license,
                    'idcard'=>$idcard,
                    'protocol'=>$protocol,
                ];
                WeixinCardMerchant::where('id',$id)->update($updatalocal);
            }else{
                return json_encode([
                    'errcode'=>1,
                    'errmsg'=>$responseresult->errmsg
                ]);
            }
            return json_encode([
                'errcode'=>0,
                'errmsg'=>'成功提交'
            ]);
            //保持数据库
        }catch(Exception $e){
            return json_encode([
                'errcode'=>1,
                'errmsg'=>$e->getMessage()
            ]);
        }
    }
    //获取子商户二级类目
    public function getsecondcategory(Request $request){
        $firstv=$request->get('firstv');
        $app=$this->WxCard();
        $card=$app->card;
        $category=$card->getCategories();
        $secondv=[];
        foreach($category['category'] as $v){
            if($v['primary_category_id']==$firstv){
                $secondv=$v['secondary_category'];
                break;
            }
        }
        return json_encode($secondv);
    }
    //wxcarduploads
    public function wxcarduploads(Request $request){
        $type=$request->get('type');
        $file = Input::file('image');
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
            $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
            $file->move(public_path() . '/uploads/wxcard' . '/', $newName);
            $path =public_path() . '/uploads/wxcard' . '/'.$newName;
//            //上传微信服务器
//            $app=$this->WxCard();
//            try{
//                $result=self::uploadsImage($app,$path,2);
//                $media_id=$result['media_id'];
//                $create_at=$result['create_at'];
//            }catch(Exception $e){
//                return json_encode([
//                    'status'=>0,
//                    'error'=>$e->getMessage()
//                ]);
//            }
//            dd($result);
        }
        $data = [
            'image_url' => '/uploads/wxcard/' . $newName,
            'status' => 1,
        ];
        return json_encode($data);
    }
    //上次永久素材图片
    public function uploadsImage(Application $app,$url,$type){
        if($type==1)
            $material = $app->material;
        else
            $material = $app->material_temporary;
        $result = $material->uploadImage($url);  // 请使用绝对路径写法！除非你正确的理解了相对路径（好多人是没理解对的）！
        return $result;
    }
    //获取颜色
    public function getColors(Application $app){
        $card=$app->card;
        $colors=$card->getColors()['colors'];
        return $colors;
    }
    //创建卡券
    public function createCard(Application $app){
        $card = $app->card;
        $cardType = 'GROUPON';
        $baseInfo = [
            'logo_url' => 'http://mmbiz.qpic.cn/mmbiz/2aJY6aCPatSeibYAyy7yct9zJXL9WsNVL4JdkTbBr184gNWS6nibcA75Hia9CqxicsqjYiaw2xuxYZiaibkmORS2oovdg/0',
            'brand_name' => '测试商户造梦空间',
            'code_type' => 'CODE_TYPE_QRCODE',
            'title' => '测试',
            'sub_title' => '测试副标题',
            'color' => 'Color010',
            'notice' => '测试使用时请出示此券',
            'service_phone' => '15311931577',
            'description' => "测试不可与其他优惠同享\n如需团购券发票，请在消费时向商户提出\n店内均可使用，仅限堂食",
            'date_info' => [
                'type' => 'DATE_TYPE_FIX_TERM',
                'fixed_term' => 90, //表示自领取后多少天内有效，不支持填写0
                'fixed_begin_term' => 0, //表示自领取后多少天开始生效，领取后当天生效填写0。
            ],
            'sku' => [
                'quantity' => '10', //自定义code时设置库存为0
            ],
            'location_id_list' => ['461907340'],  //获取门店位置poi_id，具备线下门店的商户为必填
            'get_limit' => 1,
            'use_custom_code' => false, //自定义code时必须为true
            'bind_openid' => false,
            'can_share' => true,
            'can_give_friend' => false,
            'center_title' => '顶部居中按钮',
            'center_sub_title' => '按钮下方的wording',
            'center_url' => 'http://www.qq.com',
            'custom_url_name' => '立即使用',
            'custom_url' => 'http://www.qq.com',
            'custom_url_sub_title' => '6个汉字tips',
            'promotion_url_name' => '更多优惠',
            'promotion_url' => 'http://www.qq.com',
            'source' => '造梦空间',
        ];
        $especial = [
            'deal_detail' => 'deal_detail',
        ];
        $result = $card->create($cardType, $baseInfo, $especial);
        return $result;
    }
    //创建二维码

    //生成子商户
    public function createSubMerchantlocal($info){
        $app=$this->WxCard();
        $accresstoken=$app->access_token->getToken();
        $url='https://api.weixin.qq.com/card/submerchant/submit?access_token='.$accresstoken;
        $result=$this->http_post($url,$info);
        return $result;
    }
}