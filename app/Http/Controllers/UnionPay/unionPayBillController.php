<?php
namespace App\Http\Controllers\UnionPay;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\UnionPayStoreCart;
use App\Models\UnionPayStore;
class unionPayBillController extends BaseController{
    public function unionPayBill(){
        //所有收银员
        $cashier=DB::table("merchants")->select("name","id")->get();
        foreach($cashier as $v){
            $cashier[$v->id]=$v->name;
        }
        $array=[401,402];
        $list=DB::table("orders")
            ->join("union_pay_stores","orders.store_id","=","union_pay_stores.store_id")
            ->where("union_pay_stores.user_id","=",auth()->user()->id)
            ->whereIn("orders.type",$array)
            ->orderBy("orders.updated_at","desc")
            ->select("orders.remark","orders.created_at","orders.type","orders.out_trade_no","orders.pay_status","orders.store_id","orders.merchant_id","orders.total_amount","union_pay_stores.alias_name")
            ->paginate(9);
        if(Auth::user()->hasRole('admin')){
            $list=DB::table("orders")
                ->join("union_pay_stores","orders.store_id","=","union_pay_stores.store_id")
                ->whereIn("orders.type",$array)
                ->orderBy("orders.updated_at","desc")
                ->select("orders.remark","orders.created_at","orders.type","orders.out_trade_no","orders.pay_status","orders.store_id","orders.merchant_id","orders.total_amount","union_pay_stores.alias_name")
                ->paginate(9);
        }
        return view("admin.UnionPay.store.order",compact("list","cashier"));
    }
    //加载收款设置页
    public function setUnionPayCard(Request $request){
        $auth = Auth::user()->can('setUnionpayCard');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $name=$request->get("name");
        $store_id=$request->get("store_id");
        $store= DB::table("union_pay_store_carts")
            ->where('store_id', $store_id)
            ->first();
     return view("admin.UnionPay.store.setUnionPayCard",compact("store","name"));
    }
    //收款设置
    public function setCard(Request $request){
        $out_merchant_id=$request->get("out_merchant_id");
        $id=$request->get("id");
        $bank_card_no=$request->get("bank_card_no");
        $bank_card_name=$request->get("bank_card_name");
        $datas['bank_card_no']= $bank_card_no;
        $datas['bank_card_name']=$bank_card_name;
        $aop = $this->AopClient();
        $aop->method = "fshows.paycompany.liquidation.merchant.bindcard";

            $pay = [
                'out_merchant_id' => $out_merchant_id,
                'bank_card_no' => $bank_card_no,
            ];
            $data = array('content' => json_encode($pay));
            $response = $aop->execute($data);
        if(preg_match("/^(\d{16}|\d{19})$/",$bank_card_no)){
            if(json_decode($response)->error_message=="请先通过审核!"){
                if(DB::table("union_pay_store_carts")->where("id",$id)->update($datas)){
                    return json_encode([
                        "success"=>"1"
                    ]);
                }else{
                    return json_encode([
                        "success"=>0,
                        "error_message"=>"保存失败,请输入正确的银行卡号"
                    ]);
                }
            }elseif(json_decode($response)->success){
                if(DB::table("union_pay_store_carts")->where("id",$id)->update($datas)){
                    return json_encode([
                        "success"=>"1"
                    ]);
                }else{
                    return json_encode([
                        "success"=>0,
                        "error_message"=>"保存失败"
                    ]);
                }

            }else{
                return json_encode([
                    "success"=>0,
                    "error_message"=>json_decode($response)->error_message
                ]);
            }
        }else{
            return json_encode([
                "success"=>0,
                "error_message"=>"保存失败,请输入正确的银行卡号"
            ]);
        }

    }

    //商户资料
    public function unionpayInfo(Request $request){
        $auth = Auth::user()->can('unionpayStoreInfo');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $id=$request->get("id");
        $files=DB::table("union_pay_stores")->where("id",$id)->select("local_image")->first();
        $image=explode(";",$files->local_image);
        $count=count($image)-1;
        return view("admin.UnionPay.store.unionpayInfo",compact("image","count"));
    }
    //开启或关闭收款
    public function unionPayStatus(Request $request){
        $auth = Auth::user()->can('unionpayOpen');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $type = $request->get('type');
        try {
            UnionPayStore::where('id', $request->get('id'))->update([
                'pay_status' => $type
            ]);
        } catch (\Exception $exception) {
            return json_encode([
                'success' => 0,
            ]);
        }
        return json_encode([
            'success' => 1,
        ]);
    }
    //银联商户软删除
    public function unionpayChangeStatus(Request $request){
        $auth = Auth::user()->can('changeUnionpay');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
       $id=$request->get('id');
        $data['is_delete']=1;
        if(DB::table("union_pay_stores")->where("id",$id)->update($data)){
            return json_encode([
                "success"=>"1"
            ]);
        }else{
            return json_encode([
                "success"=>0
            ]);
        }
    }
    //加载银联还原页
    public function unionRestoreIndex(Request $request){
        $auth = Auth::user()->can('unionpayRestore');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $shopname=$request->get("shopname");
        $where=[];
        if($shopname){
            $where[]=['alias_name','like',"%".$shopname."%"];
        }
        $data =UnionPayStore::where('user_id', Auth::user()->id)->where($where)->where('is_delete', 1)->orderBy('created_at', 'desc')->paginate(8);
        if (Auth::user()->hasRole('admin')) {
            $data =UnionPayStore::where('is_delete', 1)->where($where)->orderBy('created_at', 'desc')->paginate(8);
        }
        return view("admin.UnionPay.store.unionRestoreIndex", ['data' => $data,"shopname"=>$shopname]);
    }
    //还原
    public function unionRestore(Request $request){
        $id = $request->id;
        $data['is_delete'] = 0;
        if (UnionPayStore::where("id", $id)->update($data)) {
            return redirect("/admin/UnionPay/unionRestoreIndex");
        }
    }
    //彻底删除
    public function deleteUnionPay(Request $request){
        $auth = Auth::user()->can('deleteUnionpay');
        if (!$auth) {
            return json_encode(['success' => 0]);
        }else{
            $id = $request->get("id");
            try {
                $data = UnionPayStore::where('id', $id)->first();
                $store_id=$data->store_id;
                if (UnionPayStore::where("id", $id)->delete()&&UnionPayStoreCart::where("store_id",$store_id)->delete()) {
                    return json_encode(['success' => 1]);
                }
            } catch (\Exception $exception) {
                return json_encode(['success' => 0]);
            }
        }
    }
    public function unionSelected(Request $request){
        $s = $request->get("data");
        $data['is_delete'] = 0;
        //dd($s);
        if($s){
            foreach ($s as $v) {
                UnionPayStore::where("id", $v)->update($data);
            }
        }
        return redirect("/admin/UnionPay/unionRestoreIndex");
    }
}
?>