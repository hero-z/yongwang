<?php
namespace App\Http\Controllers\NewLand;
use App\Http\Controllers\Controller;
use App\Models\MercRegist;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NewLandController extends Controller
{
    public function newlandIndex(Request $request)
    {
        $shopname=$request->get("shopname");
        $data = MercRegist::where('user_id', Auth::user()->id)->where("is_delete", 0)->where('pid', 0)->orderBy('created_at', 'desc')->get();
        if (Auth::user()->hasRole('admin')) {
            $data = MercRegist::where('pid', 0)->where("is_delete", 0)->orderBy('created_at', 'desc')->get();
        }
        if ($data->isEmpty()) {
            $paginator = "";
            $datapage = "";
        } else {
            $data = $data->toArray();
            //非数据库模型自定义分页
            $perPage = 9;//每页数量
            if ($request->has('page')) {
                $current_page = $request->input('page');
                $current_page = $current_page <= 0 ? 1 : $current_page;
            } else {
                $current_page = 1;
            }
            $item = array_slice($data, ($current_page - 1) * $perPage, $perPage); //注释1
            $total = count($data);
            $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
            $datapage = $paginator->toArray()['data'];
        }
        return view("NewLand.newlandIndex", compact('datapage', 'paginator',"shopname"));
    }

    //添加商户页
    public function addNewland()
    {
        return view("NewLand.addNewLand");
    }

    //执行添加商户
    public function insertNewLand(Request $request)
    {
        $data = $request->except("_token");
        $data['user_id'] = Auth::user()->id;
        $data['store_id'] = "n" . date("YmdHis") . time() . rand(1000, 9999);
        $rules = [
            'store_name' => 'required',
            'merc_id' => 'required',
            "stoe_cnt_tel"=>"required|max:11|min:11"
        ];
        $messages = [
            'required' => '必填项',
            "stoe_cnt_tel.max"=>"手机号必须为11位",
            "stoe_cnt_tel.min"=>"手机号必须为11位"
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator);  //返回一次性错误
        }
        try {
            if (MercRegist::create($data)) {
                return back()->with("message", "添加商户成功");
            } else {
                return back()->with("message", "添加商户失败");
            }
        } catch (\Exception $e) {
            return back()->with('message', $e->getMessage());
        }

    }

    //加载编辑页
    public function editNewLand(Request $request)
    {
        $id = $request->get('id');
        $info = MercRegist::where("id", $id)->first();
        return view("NewLand.editNewLand", compact("info"));
    }
    //更新商户
    public function updateNewLand(Request $request){
        $data['store_name']=$request->get('store_name');
        $data['merc_id']=$request->get('merc_id');
        $data['stoe_cnt_tel']=$request->get("stoe_cnt_tel");
        $id=$request->get('id');
        $rules = [
            'store_name' => 'required',
            'merc_id' => 'required',
            "stoe_cnt_tel"=>"required|max:11|min:11"
        ];
        $messages = [
            'required' => '必填项',
            "stoe_cnt_tel.max"=>"手机号必须为11位",
            "stoe_cnt_tel.min"=>"手机号必须为11位"
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator);  //返回一次性错误
        }
        try{
            if(MercRegist::where("id",$id)->update($data)){
                return back()->with('message',"修改成功");
            }else{
                return back()->with("message","商户信息没有任何改动");
            }
        }catch(\Exception $e){
                return back()->with('message',$e->getMessage());
        }
    }
    //删除商户,软删除
    public function delNewLand(Request $request){
        $id=$request->get('id');
        $data['is_delete']=1;
        //软删除
        try{
            if(MercRegist::where("id",$id)->update($data)){
                return json_encode([
                   "success"=>1,
                    "message"=>"删除成功"
                ]);
            }else{
                return json_encode([
                    "success"=>0,
                    "message"=>"删除失败"
                ]);
            }
        }catch(\Exception $e){
            return json_encode([
                "success"=>0,
                "message"=>$e->getMessage()
            ]);
        }
    }
    //搜索
    public function searchNewLand(Request $request){
        $shopname=$request->get("shopname");
        try{
        if(Auth::user()->hasRole("admin")){
            $data=MercRegist::where("is_delete",0)->where("pid",0)->where("store_name","like","%".$shopname."%")->orderBy("created_at")->get();
        }else{
            $data=MercRegist::where("is_delete",0)->where("user_id",Auth::user()->id)->where("pid",0)->where("store_name",'like',"%".$shopname."%")->orderBy("created_at")->get();
        }
            if ($data->isEmpty()) {
                $paginator = "";
                $datapage = "";
            } else {
                $data = $data->toArray();
                //非数据库模型自定义分页
                $perPage = 9;//每页数量
                if ($request->has('page')) {
                    $current_page = $request->input('page');
                    $current_page = $current_page <= 0 ? 1 : $current_page;
                } else {
                    $current_page = 1;
                }
                $item = array_slice($data, ($current_page - 1) * $perPage, $perPage); //注释1
                $total = count($data);
                $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
                    'path' => Paginator::resolveCurrentPath(),
                    'pageName' => 'page',
                ]);
                $datapage = $paginator->toArray()['data'];
            }
            return view("NewLand.newlandIndex", compact('datapage', 'paginator',"shopname"));
        }catch(\Exception $e){
            return back();
        }

    }
    //加载还原页
    public function NewLandRestore(Request $request){
        $shopname=$request->get("shopname");
        $where=[];
        if($shopname){
            $where[]=["store_name", 'like', '%' . $shopname . '%'];

        }
        $data = MercRegist::where('user_id', Auth::user()->id)->where($where)->where("is_delete", 1)->where('pid', 0)->orderBy('created_at', 'desc')->get();
        if (Auth::user()->hasRole('admin')) {
            $data = MercRegist::where('pid', 0)->where($where)->where("is_delete", 1)->orderBy('created_at', 'desc')->get();
        }
        if ($data->isEmpty()) {
            $paginator = "";
            $datapage = "";
        } else {
            $data = $data->toArray();
            //非数据库模型自定义分页
            $perPage = 9;//每页数量
            if ($request->has('page')) {
                $current_page = $request->input('page');
                $current_page = $current_page <= 0 ? 1 : $current_page;
            } else {
                $current_page = 1;
            }
            $item = array_slice($data, ($current_page - 1) * $perPage, $perPage); //注释1
            $total = count($data);
            $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
            $datapage = $paginator->toArray()['data'];
        }
        return view("NewLand.NewLandRestore", compact('datapage', 'paginator',"shopname"));
    }
    //单个还原
    public function RestoreNewLand(Request $request){
        $id=$request->id;
        $data['is_delete']=0;
        if(MercRegist::where("id",$id)->update($data)){
            return redirect("/admin/newland/NewLandRestore");
        }else{
            return redirect("/admin/newland/NewLandRestore");
        }
    }
    //还原选中
    public function RestoreNewLands(Request $request){
        $id=$request->get('data');
        $data['is_delete']=0;
         foreach($id as $v){
             MercRegist::where("id",$id)->update($data);
        }
        return redirect("/admin/newland/NewLandRestore");
    }
    //彻底删除
    public function deleteNewLand(Request $request){
        $id=$request->get('id');
        try{
          if(MercRegist::where('id',$id)->delete()){
              return json_encode([
                  "message"=>"删除成功",
                 "success"=>1,
              ]);
          }else{
              return json_encode([
                  "message"=>"删除失败",
                  "success"=>0,
              ]);
            }
        }catch(\Exception $e){
            return json_encode([
                "message"=>$e->getMessage(),
                "success"=>0,
            ]);
        }
    }
    //分店管理
    public function NewLandBranchIndex(Request $request){
        $pid=$request->get("pid");
        $datapage = MercRegist::where('pid', $pid)->where('is_delete', 0)->paginate(8);
        $store_name=MercRegist::where('id', $pid)->where('is_delete', 0)->first()['store_name'];
        return view('NewLand.NewLandBranchIndex', compact('datapage', 'store_name','pid'));
    }
    //分店还原页
    public function NewLandBranchRestore(Request $request){
        $pid = $request->get('pid');
        $data = MercRegist::where('pid', $pid)->where('is_delete', 1)->paginate(8);
        return view('NewLand.NewLandBranchRestore', compact('data'));
    }
  //分店单个还原
    public function RestoreNewLandBranch(Request $request){
        $id=$request->id;
        $data['is_delete']=0;
        if(MercRegist::where("id",$id)->update($data)){
            return redirect("/admin/newland/NewLandBranchRestore");
        }else{
            return redirect("/admin/newland/NewLandBranchRestore");
        }
    }
    //还原分店选中
    public function RestoreNewLandBranchs(Request $request){
        $id=$request->get('data');
        $data['is_delete']=0;
        foreach($id as $v){
            MercRegist::where("id",$id)->update($data);
        }
        return redirect("/admin/newland/NewLandBranchRestore");
    }
    //彻底删除
    public function deleteNewLandBranch(Request $request){
        $id=$request->get('id');
        try{
            if(MercRegist::where('id',$id)->delete()){
                return json_encode([
                    "message"=>"删除成功",
                    "success"=>1,
                ]);
            }else{
                return json_encode([
                    "message"=>"删除失败",
                    "success"=>0,
                ]);
            }
        }catch(\Exception $e){
            return json_encode([
                "message"=>$e->getMessage(),
                "success"=>0,
            ]);
        }
    }
    //加载分店添加页
    public function NewLandBranchAdd(Request $request){
        $pid=$request->get('pid');
        return view("NewLand.AddNewLandBranch",compact("pid"));
    }
    //执行添加
    public function AddNewLandBranch(Request $request){
        $data = $request->all();
        $rules = [
            'store_name' => 'required|unique:merc_regists',
            'stoe_cnt_tel' => 'required|min:11|max:11',
        ];
        $messages = [
            'required' => '不能为空',
            'unique' => '店名已经存在',
            'min' => '手机号码长度不够'
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator);  //返回一次性错误
        }
        try {
            $store = MercRegist::where('id', $request->get('pid'))->first();
            $store = $store->toArray();
            $store['store_id'] = 'n' . date('Ymdhis', time()) . rand(10000, 99999);
            $store['pid'] = $data['pid'];
            $store['store_name'] = $data['store_name'];
            $store['stoe_cnt_tel'] = $data['stoe_cnt_tel'];
            $s = MercRegist::where('store_id', $store['store_id'])->first();
            if ($s) {
                MercRegist::where('store_id', $store['store_id'])->updated($store);
            } else {
                MercRegist::create($store);
            }
        } catch (\Exception $exception) {
            return back()->with('errors', '添加失败');
        }
        return redirect('/admin/newland/NewLandBranchIndex?pid=' . $data['pid']);
    }
    //改变分店状态
    public function ChangeNewLand(Request $request){
        $id=$request->get('id');
        $data['is_delete']=1;
        //软删除

            if(MercRegist::where("id",$id)->update($data)){
              return back();
            }else{
                return back();
            }


    }
    //新大陆流水
    public function NewLandBills(Request $request){
        //所有收银员
        $cashier=DB::table("merchants")->select("name","id")->get();
        foreach($cashier as $v){
            $cashier[$v->id]=$v->name;
        }
        $id=$request->get("id");
        $where=[];

        $array=[1001];
        if (Auth::user()->hasRole('admin')) {

            //平安扫码枪
            $data = DB::table("orders")
                ->join("merc_regists", "orders.store_id", "=", "merc_regists.store_id")
                ->whereIn("orders.type",$array)
                ->select("orders.remark",'orders.out_trade_no','orders.trade_no', "orders.store_id", 'merc_regists.store_name', "orders.created_at", "orders.updated_at", "orders.total_amount", "orders.type", "orders.pay_status","orders.merchant_id")
                ->orderBy("orders.created_at", "desc")
                ->get()
                ->toArray();
        } else {
            //平安扫码枪
            $data = DB::table("orders")
                ->join("merc_regists", "orders.store_id", "=", "merc_regists.store_id")
                ->whereIn("orders.type",$array)
                ->where("merc_regists.user_id",auth()->user()->id)
                ->select("orders.remark",'orders.out_trade_no','orders.trade_no', "orders.store_id", 'merc_regists.store_name', "orders.created_at", "orders.updated_at", "orders.total_amount", "orders.type", "orders.pay_status","orders.merchant_id")
                ->orderBy("orders.created_at", "desc")
                ->get()
                ->toArray();

        }
        if($id){
            //获取总店和分店
            $store_id=MercRegist::where("id",$id)->orwhere("pid",$id)->select("store_id")->get()->toArray();
            if($store_id){
                foreach($store_id as $k=>$v){
                    $where[$k]=$v['store_id'];
                }
            }
            //平安扫码枪
            $data = DB::table("orders")
                ->join("merc_regists", "orders.store_id", "=", "merc_regists.store_id")
                ->whereIn("orders.type",$array)
                ->whereIn("orders.store_id",$where)
                ->select("orders.remark",'orders.out_trade_no','orders.trade_no', "orders.store_id", 'merc_regists.store_name', "orders.created_at", "orders.updated_at", "orders.total_amount", "orders.type", "orders.pay_status","orders.merchant_id")
                ->orderBy("orders.created_at", "desc")
                ->get()
                ->toArray();
        }
        //非数据库模型自定义分页
        $perPage = 8;//每页数量
        if ($request->has('page')) {
            $current_page = $request->input('page');
            $current_page = $current_page <= 0 ? 1 : $current_page;
        } else {
            $current_page = 1;
        }
        $item = array_slice($data, ($current_page - 1) * $perPage, $perPage); //注释1
        $total = count($data);
        //dd($total);
        $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
        $datapage = $paginator->toArray()['data'];
        //  dd($paginator);
        return view('NewLand.order', compact('datapage', 'paginator',"cashier"));
    }
}