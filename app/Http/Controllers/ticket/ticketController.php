<?php

namespace App\Http\Controllers\ticket;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Push\AopClient;
use App\Models\PushConfig;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ticketController extends Controller
{
    public function index()
    {
        $list = DB::table("push_print_shop_lists")->where("type","yilianyun")->paginate(8);
        return view("admin.ticket.index", compact('list'));
    }
    //商户版打印机列表\
    public function merchineLists(){
        $info=DB::table("merchant_shops")
            ->where("merchant_id",auth()->guard('merchant')->user()->id)
            ->select("store_id")
            ->get();
       $array=[];
        foreach($info as $v){
            $array[]=$v->store_id;
        }
        $list=DB::table("push_print_shop_lists")->whereIn("store_id",$array)->paginate(8);

        return view("admin.ticket.merchineLists",compact("list"));
    }
    //商户版添加打印机
    public function setMerchine(){
        $auth = Auth::user()->can('addMerchine');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
       $list=DB::table("merchant_shops")
           ->where("merchant_id",auth()->guard('merchant')->user()->id)
           ->select("store_id", "store_name","store_type")
           ->get();
        return view("merchant.setMerchine",compact("list"));
    }
    //商户版添加U印打印机
    public function setUPrint(){
        $auth = Auth::user()->can('addMerchine');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $list=DB::table("merchant_shops")
            ->where("merchant_id",auth()->guard('merchant')->user()->id)
            ->select("store_id", "store_name","store_type")
            ->get();
        return view("admin.ticket.setUprint",compact("list"));
    }
    //U印设备列表
    public function UprintIndex(){
        $list = DB::table("push_print_shop_lists")->where("type","Uprint")->paginate(8);
        return view("admin.ticket.UprintIndex",compact("list"));
    }
    //添加U印
    public function addUprint(Request $request){
        $auth = Auth::user()->can('addMerchine');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        //支付宝当面付店铺
        $oali = DB::table('alipay_app_oauth_users')
            ->where("promoter_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("store_id", "auth_shop_name as name")
            ->get();
        //支付宝口碑电铺
        $sali = DB::table("alipay_shop_lists")
            ->where("user_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("store_id", "main_shop_name as name")
            ->get();
        //微信店铺
        $weixin = DB::table("weixin_shop_lists")
            ->where("user_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("store_id", "store_name as name")
            ->get();
        //平安店铺
        $pingan = DB::table("pingan_stores")
            ->where("user_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("external_id as store_id", "alias_name as name")
            ->get();
        //浦发店铺
        $pufa=DB::table('pufa_stores')
            ->where('user_id',Auth::user()->id)
            ->where("is_delete",0)
            ->select('store_id',"merchant_short_name as name")
            ->get();
        //银联店铺
        $unionpay=DB::table('union_pay_stores')
            ->where('user_id',Auth::user()->id)
            ->where("is_delete",0)
            ->select('store_id',"alias_name as name")
            ->get();
        if (Auth::user()->hasRole('admin')) {
            //支付宝当面付店铺
            $oali = DB::table('alipay_app_oauth_users')
                ->where("is_delete",0)
                ->select("store_id", "auth_shop_name as name")
                ->get();
            //支付宝口碑电铺
            $sali = DB::table("alipay_shop_lists")
                ->where("is_delete",0)
                ->select("store_id", "main_shop_name as name")
                ->get();
            //微信店铺
            $weixin = DB::table("weixin_shop_lists")
                ->where("is_delete",0)
                ->select("store_id", "store_name as name")
                ->get();
            //平安店铺
            $pingan = DB::table("pingan_stores")
                ->where("is_delete",0)
                ->select("external_id as store_id", "alias_name as name")
                ->get();
            //浦发店铺
            $pufa=DB::table('pufa_stores')
                ->where("is_delete",0)
                ->select('store_id',"merchant_short_name as name")
                ->get();
            //银联店铺
            $unionpay=DB::table('union_pay_stores')
                ->where("is_delete",0)
                ->select('store_id',"alias_name as name")
                ->get();
        }
        return view("admin.ticket.addUprint", compact("oali", "sali", "weixin", "pingan","pufa","unionpay"));
    }
    //添加u印
    public function insertUprint(Request $request){
        $Uprint=$request->get("merchine");
        $list = explode("*", $Uprint);
        if(count($list)>=2){
            $data['mname'] = $request->get("mname");
            $data['machine_code'] = $request->get("merchine_code");
            $data['store_name'] = $list[1];
            $data['store_id'] = $list[0];
            $data['type']="Uprint";
            $data['phone']=$request->get("phone");
            $data['number']=$request->get("number");
            $data['code']=$request->get("code");
            $data['code_description']=$request->get("code_description");
            $data['created_at']=date("Y-m-d H:i:s");
            $info=DB::table("push_print_shop_lists")->where("store_id",$data['store_id'])->where("type","Uprint")->first();
            if($info){
                return back()->with("warnning", "请勿重复添加");
            }else{
                if(DB::table("push_print_shop_lists")->insert($data)){
                    return back()->with("warnning", "保存成功");
                }else{
                    return back()->with("warnning", "保存失败");
                }

            }
        }else{
            return back()->with('warnning', "请输入参数");
        }
    }
    //删除U印
    public function deleteUprint(Request $request){
        $id=$request->get("id");
        try{
            if(DB::table("push_print_shop_lists")->where("id",$id)->delete()){
                return json_encode(['success'=>1]);
            }else{
                return json_encode(['success'=>1]);
            }
        }catch(\Exception $e){
            return json_encode(['success'=>1]);
        }
    }
   //修改打印设备
    public function editUprint(Request $request){
        $auth = Auth::user()->can('editMerchine');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $id=$request->get("id");
        $list=DB::table("push_print_shop_lists")->where("id",$id)->first();
        //支付宝当面付店铺
        $oali = DB::table('alipay_app_oauth_users')
            ->where("promoter_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("store_id", "auth_shop_name as name")
            ->get();
        //支付宝口碑电铺
        $sali = DB::table("alipay_shop_lists")
            ->where("user_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("store_id", "main_shop_name as name")
            ->get();
        //微信店铺
        $weixin = DB::table("weixin_shop_lists")
            ->where("user_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("store_id", "store_name as name")
            ->get();
        //平安店铺
        $pingan = DB::table("pingan_stores")
            ->where("user_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("external_id as store_id", "alias_name as name")
            ->get();
        //浦发店铺
        $pufa=DB::table('pufa_stores')
            ->where('user_id',Auth::user()->id)
            ->where("is_delete",0)
            ->select('store_id',"merchant_short_name as name")
            ->get();
        //银联店铺
        $unionpay=DB::table('union_pay_stores')
            ->where('user_id',Auth::user()->id)
            ->where("is_delete",0)
            ->select('store_id',"alias_name as name")
            ->get();
        if (Auth::user()->hasRole('admin')) {
            //支付宝当面付店铺
            $oali = DB::table('alipay_app_oauth_users')
                ->where("is_delete",0)
                ->select("store_id", "auth_shop_name as name")
                ->get();
            //支付宝口碑电铺
            $sali = DB::table("alipay_shop_lists")
                ->where("is_delete",0)
                ->select("store_id", "main_shop_name as name")
                ->get();
            //微信店铺
            $weixin = DB::table("weixin_shop_lists")
                ->where("is_delete",0)
                ->select("store_id", "store_name as name")
                ->get();
            //平安店铺
            $pingan = DB::table("pingan_stores")
                ->where("is_delete",0)
                ->select("external_id as store_id", "alias_name as name")
                ->get();
            //浦发店铺
            $pufa=DB::table('pufa_stores')
                ->where("is_delete",0)
                ->select('store_id',"merchant_short_name as name")
                ->get();
            //银联店铺
            $unionpay=DB::table('union_pay_stores')
                ->where("is_delete",0)
                ->select('store_id',"alias_name as name")
                ->get();
        }

        return view("admin.ticket.editUprint", compact("oali", "sali", "weixin", "pingan","pufa","unionpay","list"));

    }
   public function updateUprint(Request  $request){
       $id=$request->get("id");
       $Uprint=$request->get("merchine");
       $list = explode("*", $Uprint);
       if(count($list)>=2){
           $data['mname'] = $request->get("mname");
           $data['machine_code'] = $request->get("merchine_code");
           $data['store_name'] = $list[1];
           $data['store_id'] = $list[0];
           $data['type']="Uprint";
           $data['phone']=$request->get("phone");
           $data['code_description']=$request->get("code_description");
           $data['number']=$request->get("number");
           $data['code']=$request->get("code");
           $data['updated_at']=date("Y-m-d H:i:s");
               if(DB::table("push_print_shop_lists")->where("id",$id)->update($data)){
                   return back()->with("warnning", "保存成功");
               }else{
                   return back()->with("warnning", "保存失败");
           }
       }else{
           return back()->with('warnning', "请输入参数");
       }
   }




    public function addMerchine()
    {
        $auth = Auth::user()->can('addMerchine');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        //支付宝当面付店铺
        $oali = DB::table('alipay_app_oauth_users')
            ->where("promoter_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("store_id", "auth_shop_name as name")
            ->get();
        //支付宝口碑电铺
        $sali = DB::table("alipay_shop_lists")
            ->where("user_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("store_id", "main_shop_name as name")
            ->get();
        //微信店铺
        $weixin = DB::table("weixin_shop_lists")
            ->where("user_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("store_id", "store_name as name")
            ->get();
        //平安店铺
        $pingan = DB::table("pingan_stores")
            ->where("user_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("external_id as store_id", "alias_name as name")
            ->get();
        //浦发店铺
        $pufa=DB::table('pufa_stores')
            ->where('user_id',Auth::user()->id)
            ->where("is_delete",0)
            ->select('store_id',"merchant_short_name as name")
            ->get();
        //银联店铺
        $unionpay=DB::table('union_pay_stores')
            ->where('user_id',Auth::user()->id)
            ->where("is_delete",0)
            ->select('store_id',"alias_name as name")
            ->get();
        if (Auth::user()->hasRole('admin')) {
            //支付宝当面付店铺
            $oali = DB::table('alipay_app_oauth_users')
                ->where("is_delete",0)
                ->select("store_id", "auth_shop_name as name")
                ->get();
            //支付宝口碑电铺
            $sali = DB::table("alipay_shop_lists")
                ->where("is_delete",0)
                ->select("store_id", "main_shop_name as name")
                ->get();
            //微信店铺
            $weixin = DB::table("weixin_shop_lists")
                ->where("is_delete",0)
                ->select("store_id", "store_name as name")
                ->get();
            //平安店铺
            $pingan = DB::table("pingan_stores")
                ->where("is_delete",0)
                ->select("external_id as store_id", "alias_name as name")
                ->get();
            //浦发店铺
            $pufa=DB::table('pufa_stores')
                ->where("is_delete",0)
                ->select('store_id',"merchant_short_name as name")
                ->get();
            //银联店铺
            $unionpay=DB::table('union_pay_stores')
                ->where("is_delete",0)
                ->select('store_id',"alias_name as name")
                ->get();
        }
        return view("admin.ticket.addMerchine", compact("oali", "sali", "weixin", "pingan","pufa","unionpay"));
    }

    //添加打印设备
    public function insertMerchine(Request $request)
    {
        $merchine = $request->get("merchine");
        $list = explode("*", $merchine);
        if(count($list)>=2) {
            $data['mname'] = $request->get("mname");
            $data['msign'] = $request->get("msign");
            $data['machine_code'] = $request->get("merchine_code");
            $data['store_name'] = $list[1];
            $data['store_id'] = $list[0];
            $data['phone']=$request->get("phone");
            $data['type']="yilianyun";
            $data['created_at']=date("Y-m-d H:i:s");
            $info=DB::table("push_print_shop_lists")->where("store_id",$data['store_id'])->where("type","yilianyun")->first();
            //提交到易联云接口
            $push = new AopClient();
            $config = PushConfig::where('id', 1)->first();
            $add = $push->action_addprint($config->push_id, $request->get("merchine_code"), $config->push_user_name, $request->get("mname"), '', $config->push_key, $request->get("msign"));
          if($info){
              return back()->with("warnning", "请勿重复添加");
          }elseif ($add == 1) {
                if (DB::table("push_print_shop_lists")->insert($data)) {
                    return back()->with("warnning", "保存成功");
                }
            } elseif ($add == 2) {
                if (DB::table("push_print_shop_lists")->insert($data)) {
                    return back()->with("warnning", "保存成功");
                }
            } elseif ($add == 3 && $add == 4) {
                return back()->with("warnning", "添加失败");
            } elseif ($add == 5) {
                return back()->with("warnning", "用户验证失败");
            } elseif ($add == 6) {
                return back()->with('warnning', "非法终端号");
            }
        }else{
            return back()->with('warnning', "请输入参数");
        }
    }
    //删除打印设备
    public function deleteMerchine(request $request){
        $auth = Auth::user()->can('deleteMerchine');
        if (!$auth) {
            return json_encode(['success'=>0]);
        }
         $id=$request->get("id");
        $list=DB::table("push_print_shop_lists")->where("id",$id)->first();
        $info=DB::table("push_print_shop_lists")->where("machine_code",$list->machine_code)->get();
     if(count($info)>1){
         if(DB::table("push_print_shop_lists")->where("id",$id)->delete()){
             return json_encode(['success'=>1]);
         }else{
             return json_encode(['success'=>0]);
         }
     }else{
         //提交到易联云接口
         $push=new AopClient();
         $config=PushConfig::where("id",1)->first();
         $delete=$push->action_removeprinter($config->push_id,$list->machine_code,$config->push_key,$list->msign);
         if($delete==1){
             if(DB::table("push_print_shop_lists")->where("id",$id)->delete()){
                 return json_encode(['success'=>1]);
             }
         }else{
             return json_encode(['success'=>0]);
         }

     }

    }
    //修改设备
    public function editMerchine(Request $request){
        $auth = Auth::user()->can('editMerchine');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
       $id=$request->get("id");
        $list=DB::table("push_print_shop_lists")->where("id",$id)->first();
        //支付宝当面付店铺
        $oali = DB::table('alipay_app_oauth_users')
            ->where("promoter_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("store_id", "auth_shop_name as name")
            ->get();
        //支付宝口碑电铺
        $sali = DB::table("alipay_shop_lists")
            ->where("user_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("store_id", "main_shop_name as name")
            ->get();
        //微信店铺
        $weixin = DB::table("weixin_shop_lists")
            ->where("user_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("store_id", "store_name as name")
            ->get();
        //平安店铺
        $pingan = DB::table("pingan_stores")
            ->where("user_id", Auth::user()->id)
            ->where("is_delete",0)
            ->select("external_id as store_id", "alias_name as name")
            ->get();
        //浦发店铺
        $pufa=DB::table('pufa_stores')
            ->where('user_id',Auth::user()->id)
            ->where("is_delete",0)
            ->select('store_id',"merchant_short_name as name")
            ->get();
        //银联店铺
        $unionpay=DB::table('union_pay_stores')
            ->where('user_id',Auth::user()->id)
            ->where("is_delete",0)
            ->select('store_id',"alias_name as name")
            ->get();
        if (Auth::user()->hasRole('admin')) {
            //支付宝当面付店铺
            $oali = DB::table('alipay_app_oauth_users')
                ->where("is_delete",0)
                ->select("store_id", "auth_shop_name as name")
                ->get();
            //支付宝口碑电铺
            $sali = DB::table("alipay_shop_lists")
                ->where("is_delete",0)
                ->select("store_id", "main_shop_name as name")
                ->get();
            //微信店铺
            $weixin = DB::table("weixin_shop_lists")
                ->where("is_delete",0)
                ->select("store_id", "store_name as name")
                ->get();
            //平安店铺
            $pingan = DB::table("pingan_stores")
                ->where("is_delete",0)
                ->select("external_id as store_id", "alias_name as name")
                ->get();
            //浦发店铺
            $pufa=DB::table('pufa_stores')
                ->where("is_delete",0)
                ->select('store_id',"merchant_short_name as name")
                ->get();
            //银联店铺
            $unionpay=DB::table('union_pay_stores')
                ->where("is_delete",0)
                ->select('store_id',"alias_name as name")
                ->get();
        }
        return view("admin.ticket.editMerchine", compact("oali", "sali", "weixin", "pingan","pufa","unionpay","list"));
    }
    public function updateMerchine(Request $request){
        $id=$request->get("id");
        $merchine = $request->get("merchine");
        $list = explode("*", $merchine);
        if(count($list)>=2) {
            $data['mname'] = $request->get("mname");
            $data['msign'] = $request->get("msign");
            $data['machine_code'] = $request->get("merchine_code");
            $data['store_name'] = $list[1];
            $data['store_id'] = $list[0];
            $data['phone']=$request->get("phone");
            $data['updated_at']=date("Y-m-d H:i:s");
            //提交到易联云接口
            $push = new AopClient();
            $config = PushConfig::where('id', 1)->first();
            $add = $push->action_addprint($config->push_id, $request->get("merchine_code"), $config->push_user_name, $request->get("mname"), '', $config->push_key, $request->get("msign"));
            if ($add == 1) {
                $info=DB::table("push_print_shop_lists")->where("id",$id)->first();
                //提交到易联云接口
                $config=PushConfig::where("id",1)->first();
                $delete=$push->action_removeprinter($config->push_id,$info->machine_code,$config->push_key,$info->msign);
                if ($delete) {
                    if(DB::table("push_print_shop_lists")->where("id",$id)->update($data)){
                        return back()->with("warnning", "修改成功");
                    }
                }
            } elseif ($add == 2) {
                if(DB::table("push_print_shop_lists")->where("id",$id)->update($data)){
                    return back()->with("warnning", "修改成功");
                }
            } elseif ($add == 3 && $add == 4) {
                return back()->with("warnning", "修改失败");
            } elseif ($add == 5) {
                return back()->with("warnning", "用户验证失败");
            } elseif ($add == 6) {
                return back()->with('warnning', "非法终端号");
            }
        }else{
            return back()->with('warnning', "请输入参数");
        }
    }
    //加载设备配置页
    public function merchineConfig(){
        $list=DB::table("push_configs")->first();
        return view("admin.ticket.merchineConfig",compact("list"));

    }
    //配置
    public function updateConfig(Request $request){
        $id=$request->get("id");
        $data=$request->except("_token");
       if(DB::table("push_configs")->where("id",$id)->update($data)){
           return redirect("/admin/ticket/merchineConfig");
       }
        return redirect("/admin/ticket/merchineConfig");
    }
}
