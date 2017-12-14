<?php
namespace App\Http\Controllers\alipass;

use Alipayopen\Sdk\Request\AlipayPassInstanceAddRequest;
use Alipayopen\Sdk\Request\AlipayPassTemplateAddRequest;
use App\Http\Controllers\AlipayOpen\AlipayOpenController;
use App\Http\Controllers\Controller;
use Alipayopen\Sdk\AopClient;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class alipassController extends Controller
{
    public function index()
    {
        $list=DB::table("alipay_pass")
            ->join("alipay_app_oauth_users","alipay_pass.store_id","=","alipay_app_oauth_users.store_id")
            ->select("alipay_pass.*","alipay_app_oauth_users.auth_shop_name")
            ->paginate(8);
        return view("admin.alipass.index",compact("list"));
    }

    public function addAlipass(Request $request)
    {
        $list = DB::table("alipay_app_oauth_users")->get();
        return view("admin.alipass.addAlipass", compact("list"));
    }

    //创建卡券
    public function createAlipass(Request $request)
    {
        $ao = new AlipayOpenController();
        $aop = $ao->AopClient();
        $aop->method = "alipay.pass.template.add";
        $requests = new AlipayPassTemplateAddRequest();
        $unique_id =time().rand(10000,99999).date("YmdHis");
        $logo = url($request->get("logo"));
        $strip_image = url($request->get("strip_image"));
        $title = $request->get("title");
        $status = $request->get("status");
        $description = $request->get("description");
        $strip = $request->get("strip");
        $store=$request->get("store");
        $address = $request->get("address");
        $a = $request->get("backgroundColor");
        $backgroundColor = $this->hex2rgb($a);
        $startDate = $request->get("startDate");
        $endDate = $request->get("endDate");
        $type=$request->get("type");
        $number=$request->get("number");
        $store_id=DB::table("alipay_app_oauth_users")->where("app_auth_token",$store)->first()->store_id;
        if(round($number)!=$number||$number==""||$number<1){
                return json_encode([
                    "sub_msg"=>"卡券数量必须为1以上的整数"
                ]);
                dd();
            }
        $discount=$request->get("discount");
        if($type=="discount"){
            if(round($discount,1)!=$discount||$discount==""||$discount<1||$discount>9.9){
                return json_encode([
                    "sub_msg"=>"请输入正确的折扣格式"
                ]);
                dd();
            }else{
                $full=1;
                $reduce=1;
            }
        }

        if($type=="fto"){
            $full=$request->get("full");
            $reduce=$request->get("reduce");
         if(round($full)!=$full||$full==""||$full<1){
             return json_encode([
                 "sub_msg"=>"请输入正确的满减额"
             ]);
             dd();
         }
          if(round($reduce,2)!=$reduce||$reduce==""||$reduce<0||$reduce==0){
              return json_encode([
                  "sub_msg"=>"请输入正确的满减额啊"
              ]);
              dd();
          }
            if($reduce>$full){
                return json_encode([
                    "sub_msg"=>"所减额度过大"
                ]);
                dd();
            }
            $discount=1;
        }
        if($type=="free"){
            $full=1;
            $reduce=1;
            $discount=1;
        }
        $requests->setBizContent("{\"unique_id\":\"".$unique_id."\",
        \"tpl_content\":{\"logo\":\"".$logo."\",
        \"strip\":\"".$strip_image."\",
        \"icon\":\"".$logo."\",
        \"content\":{\"evoucherInfo\":{\"title\":\"".$title."\",
        \"type\":\"coupon\",
        \"product\":\"free\",
        \"startDate\":\"".$startDate."\",
        \"endDate\":\"".$endDate."\",
        \"operation\":[{\"format\":\"barcode\",
        \"message\":\"\$code\$\",
        \"messageEncoding\":\"UTF-8\",
        \"altText\":\"\$code\$\"}],
        \"einfo\":{\"logoText\":\"".$title."\",
        \"headFields\":[{\"key\":\"status\",
        \"label\":\"状态\",
        \"value\":\"".$status."\",
        \"type\":\"text\"}],
        \"primaryFields\":[{\"key\":\"strip\",
        \"label\":\"\",
        \"value\":\"".$strip."\",
        \"type\":\"text\"}],
        \"secondaryFields\":[{\"key\":\"validDate\",
        \"label\":\"有效期至：\",
        \"value\":\"".$endDate."\",
        \"type\":\"text\"}],
        \"auxiliaryFields\":[],
        \"backFields\":[{\"key\":\"description\",
        \"label\":\"详情描述\",
        \"value\":\"".$description."\",
        \"type\":\"text\"},
        {\"key\":\"shops\",
        \"label\":\"可用门店\",
        \"value\":\"".$address."\",
        \"type\":\"text\"},
        {\"key\":\"disclaimer\",
        \"label\":\"负责声明\",\"value\":\"除特殊注明外，本优惠不能与其他优惠同时享受；本优惠最终解释权归商家所有，如有疑问请与商家联系。提示：为了使您得到更好的服务，请在进店时出示本券。\",
        \"type\":\"text\"}]},
        \"remindInfo\":{\"offset\":\"2\"}},
        \"merchant\":{\"mname\":\"hodewu\",
        \"mtel\":\"\",\"minfo\":\"\"},
        \"platform\":{\"channelID\":\"\$channelID\$\",
        \"webServiceUrl\":\"\"},
        \"style\":{\"backgroundColor\":\"RGB(".$backgroundColor["r"].",".$backgroundColor["g"].",".$backgroundColor["b"].")\"},
        \"fileInfo\":{\"formatVersion\":\"2\",
        \"canShare\":true,
        \"canBuy\":false,
        \"canPresent\":true,
        \"serialNumber\":\"\$serialNumber\$\",
        \"supportTaxi\":\"true\",
        \"taxiSchemaUrl\":\"alipays://platformapi/startapp?appId=20000130&sourceId=20000030&showTitleBar=YES&showToolBar=NO&showLoading=NO&safePayEnabled=YES&readTitle=YES&backBehavior=back&url=/www/index.html\"},
        \"appInfo\":{\"app\":{},
        \"label\":\"\",
        \"message\":\"\"},
        \"source\":\"alipassprod\",
        \"alipayVerify\":[]}}}");
        $result = $aop->execute($requests, "", $store);
        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            $datas=json_decode($result->$responseNode->result);
            $data['tpl_id']=$datas->tpl_id;
            $data['store_id']=$store_id;
            $data['number']=$number;
            $data['type']=$type;
            $data['logo']=$request->get("logo");
            $data['strip_image']=$request->get("strip_image");
            $data['title']=$title;
            $data['status']=$status;
            $data['description']=$description;
            $data['strip']=$strip;
            $data['address']=$address;
            $data['stock_number']=$number;
            $data['startDate']=$startDate;
            $data['endDate']=$endDate;
            $data['unique_id']=$unique_id;
            $data['discount']=$discount;
            $data['full']=$full;
            $data['reduce']=$reduce;
            $data['created_at']=date("Y-m-d H:i:s");
            $data['user_id']=Auth::user()->id;
            DB::table("alipay_pass")->insert($data);
            return json_encode([
                "success"=>"1"
            ]);
        } else {
           return json_encode([
                "sub_msg"=>$result->$responseNode->sub_msg
            ]);

        }
    }

    //RGBA格式转RGB
    public function hex2rgb($hexColor)
    {
        $color = str_replace('#', '', $hexColor);
        if (strlen($color) > 3) {
            $rgb = array(
                'r' => hexdec(substr($color, 0, 2)),
                'g' => hexdec(substr($color, 2, 2)),
                'b' => hexdec(substr($color, 4, 2))
            );
        } else {
            $color = $hexColor;
            $r = substr($color, 0, 1) . substr($color, 0, 1);
            $g = substr($color, 1, 1) . substr($color, 1, 1);
            $b = substr($color, 2, 1) . substr($color, 2, 1);
            $rgb = array(
                'r' => hexdec($r),
                'g' => hexdec($g),
                'b' => hexdec($b)
            );
        }
        return $rgb;
    }
    //使用卡券
    public function  updatecardTemplete(){

    }
}
