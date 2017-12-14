<?php
/**
 * Created by PhpStorm.
 * User: hero
 * Date: 2017/6/14
 * Time: 17:07
 */

namespace App\Http\Controllers\WeBank;


use App\Merchant;
use App\Models\QrListInfo;
use App\Models\WeBankStore;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;
use Comodojo\Zip\Zip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;
use Symfony\Component\HttpFoundation\Request;

class ManageController extends BaseController
{
    public function paystatus(Request $request){
        $store_id=$request->id;
        $type=$request->type;
        $wbstore=WeBankStore::where('store_id',$store_id)->first();
        if($wbstore){
            WeBankStore::where('store_id',$store_id)->update(['pay_status'=>$type]);
            return json_encode(['success'=>1]);
        }
        return json_encode(['success'=>0,'msg'=>'修改失败']);
    }
    public function deletestore(Request $request){
        $auth = Auth::user()->can('Delwebank');
        if (!$auth) {
            return json_encode([
                'success' => 0,
                "erro_message" => "您没有权限操作!"
            ]);
        }
        $id = $request->get('id');
        $list = WeBankStore::where("store_id", $id)->first();
        if (DB::table("merchant_shops")->where("store_id", $list->store_id)->first()) {
            return json_encode([
                'success' => 0,
                "erro_message" => "请先解除店铺绑定!"
            ]);
        } else {
            WeBankStore::where('store_id', $id)->update(['is_delete' => 1]);
            return json_encode(['success' => 1]);
        }
    }
    public function restore(Request $request){
        $auth = Auth::user()->can('webankRestore');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        //所有收银员
        $users=[];
        $user=DB::table("users")->select("name","id")->get();
        foreach($user as $v){
            $users[$v->id]=$v->name;
        }
        $alias_name=$request->alias_name;
        $wheresql=[];
        if($alias_name){
            $wheresql[]=['alias_name','like','%'.$alias_name.'%'];
        }
        $data = WeBankStore::where('user_id', Auth::user()->id)->where('is_delete', 1)->where($wheresql)->orderBy('created_at', 'desc')->paginate(8);
        if (Auth::user()->hasRole('admin')) {
            $data = WeBankStore::where('is_delete', 1)->where($wheresql)->orderBy('created_at', 'desc')->paginate(8);
        }
//        dd($data);
        return view("admin.webank.restore", ['data' =>$data,'alias_name'=>$alias_name,'users'=>$users]);
    }
    public function storeback(Request $request){
        $store_id = $request->id;
        $data['is_delete'] = 0;
        if (DB::table("we_bank_stores")->where("store_id", $store_id)->update($data)) {
            return redirect(route('webankRestore'));
        }
    }
    public function allstoreback(Request $request){
        $s = $request->get("data");
        //dd($s);
        $data['is_delete'] = 0;
        if($s){
            foreach ($s as $v) {
                DB::table("we_bank_stores")->where("id", $v)->update($data);
            }
        }
        return redirect(route('webankRestore'));
    }
    public function merchantfile(Request $request){
        $store_id=$request->id;
        $store=WeBankStore::where('store_id',$store_id)->first();
        if(!$store){
            die('ID不合法!');
        }
        $store=$store->toArray();
        return view('admin.webank.editfile',compact('store'));
    }
    public function merchantfilepost(Request $request){
        $store_id=$request->store_id;
        $data=$request->except('store_id','_token');
        $store=WeBankStore::where('store_id',$store_id)->first();
        $info='出错了';
        try{
            if($store){
                $res=WeBankStore::where('store_id',$store_id)->update($data);
                return json_encode([
                    'status'=>1
                ]);
            }{
                $info='店铺不存在';
            }
        }catch (Exception $e){
            Log::info($e);
            $info=$e;
        }
        return json_encode([
            'status'=>0,
            'msg'=>$info
        ]);
    }
    public function orderlist(Request $request){
        //所有收银员
        $cashier=DB::table("merchants")->select("name","id")->get();
        foreach($cashier as $v){
            $cashier[$v->id]=$v->name;
        }
        $array=[801,802,803,804];
        if (Auth::user()->hasRole('admin')) {
            $data = DB::table("orders")
                ->join("we_bank_stores", "orders.store_id", "=", "we_bank_stores.store_id")
                ->whereIn("orders.type",$array)
                ->select("orders.remark",'orders.out_trade_no', "orders.store_id", 'we_bank_stores.alias_name', "orders.created_at", "orders.updated_at", "orders.total_amount", "orders.type", "orders.pay_status","orders.merchant_id")
                ->orderBy("orders.created_at", "desc")
                ->get();
        } else {
            $data = DB::table("orders")
                ->join("we_bank_stores", "orders.store_id", "=", "we_bank_stores.store_id")
                ->whereIn("orders.type",$array)
                ->where("we_bank_stores.user_id",auth()->user()->id)
                ->select("orders.remark",'orders.out_trade_no', "orders.store_id", 'we_bank_stores.alias_name', "orders.created_at", "orders.updated_at", "orders.total_amount", "orders.type", "orders.pay_status","orders.merchant_id")
                ->orderBy("orders.created_at", "desc")
                ->get();
        }
        $res=StoreController::dataPaginator($request,$data);
        $datapage=$res['datapage'];
        $paginator=$res['paginator'];
        return view('admin.webank.orderlist', compact('datapage', 'paginator',"cashier"));
    }
    public function cashierlist(Request $request){
        $store_id = $request->get('store_id');
        $store_name = $request->get('store_name');
        $data = DB::table('merchant_shops')
            ->join('merchants', 'merchants.id', 'merchant_shops.merchant_id')
            ->where('merchant_shops.store_id', $store_id)
            ->select('merchants.*', 'merchant_shops.store_id', 'merchant_shops.store_type')
            ->get();
        $res=StoreController::dataPaginator($request,$data);
        $datapage=$res['datapage'];
        $paginator=$res['paginator'];
        return view('admin.webank.Cashier.list', compact('datapage','paginator','store_id','store_name'));
    }
    public function cashierqr(Request $request){
        $store_id = $request->get('store_id');
        $merchant_id = $request->get('cashier_id');
        $qrinfo = DB::table('wb_qr_list_infos')->where('store_id', $store_id)->first();
        $code_number='';
        if ($qrinfo) {
            $code_number = $qrinfo->code_number;
            if (!$code_number){
                dd('收款码不存在！请检查商户是否入驻成功');
            };
        } else {
            dd('收款码有误！请检查商户是否入驻成功');
        }
        $store_name = WeBankStore::where('store_id', $store_id)->first()->alias_name;
        $merchant_name=Merchant::where('id',$merchant_id)->first()->name;
        $code_url = url('/admin/webank/webankQrCode?code_number=' . $code_number . '&merchant_id=' . $merchant_id);
        return view('admin.webank.Cashier.qr', compact('code_url', 'merchant_name', 'store_name'));
    }
    public function qrlist(){
        $lists =DB::table('wb_qr_lists as a')->join('users as b','b.id','a.user_id')->where('a.user_id', Auth::user()->id)->select('a.*','b.name')->orderby('created_at','desc')->paginate(8);
        return view('admin.webank.Code.qrlist', compact('lists'));
    }
    public function createqr(Request $request){
        //生成的批次
        $cno = time();
        //生成数量
        $num = $request->get('num', 100);
        //
        $timenow=date('YmdHis');
        try {
            DB::table('wb_qr_lists')->insert([
                'cno' => $cno,
                'user_id' => Auth::user()->id,
                'num' => $num,
                'created_at'=>$timenow,
                'updated_at'=>$timenow
            ]);
        } catch (\Exception $exception) {
            return json_encode([
                'status' => 0,
                'msg' => '插入数据库表wb_qr_lists失败'
            ]);
        }

        for ($i = 1; $i <= $num; $i++) {
            $code_number = time() . rand(1000, 9999);//编号
            $url = url('/admin/webank/webankQrCode?code_number=' . $code_number.'&code_from=1');//生成的url准备生成二维码;
            try {
                DB::table('wb_qr_list_infos')->insert([
                    'user_id' => Auth::user()->id,
                    'code_number' => $code_number,
                    'cno' => $cno,
                    'created_at'=>$timenow,
                    'updated_at'=>$timenow
                ]);
            } catch (\Exception $exception) {
                return json_encode([
                    'status' => 0,
                    'msg' => '插入数据库表wb_qr_list_infos失败'
                ]);
            }
            try {
                //生成二维码文件
                if (!is_dir(public_path('QrCode/' . $cno . '/'))) {
                    mkdir(public_path('QrCode/' . $cno . '/'), 0777);
                }
                $renderer = new Png();
                $renderer->setHeight(500);
                $renderer->setWidth(500);
                $writer = new Writer($renderer);
                $writer->writeFile($url, public_path('QrCode/' . $cno . '/' . $code_number . '.png'));
            } catch (\Exception $exception) {
                return json_encode([
                    'status' => 0,
                    'msg' => '生成二维码失败！请检测文件权限'
                ]);

            }
        }
        return json_encode([
            'status' => 1,
            'msg' => '生成二维码成功'
        ]);
    }
    public function downloadqr(Request $request){
        $cno = $request->get('cno');
        try {
            $zip = Zip::create($cno . '.zip');;
            $zip->add(public_path() . '/QrCode/' . $cno . '/', true);
        } catch (\Exception $exception) {
            return '打包失败';
        }

        return redirect(url('/' . $cno . '.zip'));
    }
}