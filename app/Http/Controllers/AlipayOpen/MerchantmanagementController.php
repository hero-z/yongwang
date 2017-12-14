<?php
namespace App\Http\Controllers\AlipayOpen;
use App\Models\MercRegist;
use App\Models\PufaStores;
use App\Models\UnionPayStore;
use App\Models\WeBankStore;
use Illuminate\Support\Facades\Validator;
use App\Merchant;
use App\Models\MerchantShops;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\CountValidator\Exception;
use App\Models\UnionStore;

class MerchantmanagementController extends AlipayOpenController
{
    public function mmdatalists(Request $request){
        $auth = Auth::user()->can('MerchantManagement ');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }

        $merchant=$request->get("merchant");
        $where=[];
        if($merchant){
            $where[]=['name','like','%'.$merchant."%"];
        }
        if(Auth::user()->hasRole('admin')){
            $list= Merchant::where($where)->orderBy("created_at","desc")->get();
        }else{
            $u_id=Auth::user()->id;
            $wheresql[]=['users.id',$u_id];
            $ids=[];
            $res[]=DB::table('alipay_app_oauth_users')
                ->join('users','alipay_app_oauth_users.promoter_id','users.id')
                ->join('merchant_shops','merchant_shops.store_id','alipay_app_oauth_users.store_id')
                ->where($wheresql)
                ->select('merchant_shops.merchant_id');
            $res[]=DB::table('alipay_shop_lists')
                ->join('users','users.id','alipay_shop_lists.user_id')
                ->join('merchant_shops','merchant_shops.store_id','alipay_shop_lists.store_id')
                ->where($wheresql)
                ->select('merchant_shops.merchant_id');
            $res[]=DB::table('pingan_stores')
                ->join('users','users.id','pingan_stores.user_id')
                ->join('merchant_shops','merchant_shops.store_id','pingan_stores.external_id')
                ->where($wheresql)
                ->select('merchant_shops.merchant_id');
            $res[]=DB::table('pufa_stores')
                ->join('users','users.id','pufa_stores.user_id')
                ->join('merchant_shops','merchant_shops.store_id','pufa_stores.store_id')
                ->where($wheresql)
                ->select('merchant_shops.merchant_id');
            $res[]=DB::table('union_pay_stores')
                ->join('users','users.id','union_pay_stores.user_id')
                ->join('merchant_shops','merchant_shops.store_id','union_pay_stores.store_id')
                ->where($wheresql)
                ->select('merchant_shops.merchant_id');
            $res[]=DB::table('weixin_shop_lists')
                ->join('users','users.id','weixin_shop_lists.user_id')
                ->join('merchant_shops','merchant_shops.store_id','weixin_shop_lists.store_id')
                ->where($wheresql)
                ->select('merchant_shops.merchant_id');

            $result=self::checkEmpty($res);
            $list=empty($result)?'':$result->distinct()->get();
            foreach ($list as $v){
                $ids[]=$v->merchant_id;
            }
            $list= Merchant::where($where)->whereIn('id',$ids)->orderBy("created_at","desc")->get();
        }
        if ($list) {
            $data = $list->toArray();
        }
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
        return view('admin.alipayopen.merchantmgt', ['data'=>$datapage,'paginator'=>$paginator]);
    }

    public function checkEmpty($arr){
        $listarr=[];
        try{
            foreach($arr as $v){
                if($v==''){
                    continue;
                }
                $listarr[]=$v;
            }
            $limit=count($listarr);
            if($limit>0){
                $result=$listarr[0];
                if($limit>1){
                    for($i=1;$i<$limit;$i++){
                        $result=$result->union($listarr[$i]);
                    }
                }
                return $result;
            }else{
                return '';
            }
        }catch(Exception $e){

        }
        return '';
    }
    //加载商户名修改页
    public function editMerchantNames(Request $request){
        $auth = Auth::user()->can('cashier');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $id=$request->get("id");
        $list=DB::table("merchants")->where("id",$id)->first();
        return view("admin.alipayopen.editMerchantNames",compact("list"));

    }
    //修改商户名修改页
    public function updateMerchantNames(Request $request){
        $id=$request->get("id");
        $data['password'] = $request->get("password");
        $data['password_confirmation']=$request->get("password_confirmation");
        if($data['password']||$data['password_confirmation']){
            $dataIn = [
                'password' => bcrypt($data['password']),
            ];


            $rules = [
                'password' => 'required|min:6|confirmed',
            ];
            $messages = [
                'required' => '密码不能为空',
                'between' => '密码必须是6~20位之间',
                'confirmed' => '新密码和确认密码不匹配',
            ];
            $cn = [
                'password' => '密码',
                'password_confirmation' => '确认密码'
            ];
            $validator = Validator::make($data, $rules, $messages, $cn);
            if ($validator->fails()) {
                return back()->withErrors($validator);  //返回一次性错误
            }
        }
        $dataIn['phone']=$request->get("phone");
        $list=DB::table("merchants")->where("phone",$dataIn['phone'])->first();
        if(preg_match("/^(13[0-9]|14[5|7]|15[0-9]|17[0-9]|18[0-9])\\d{8}$/",$dataIn['phone'])){
            if($list){
                if($list->id==$id){
                    if(DB::table("merchants")->where("id",$id)->update($dataIn)){
                        return redirect("/admin/alipayopen/merchantmanagement");
                    }
                }else{
                    return back()->with("warnning","该手机号已被占用");
                }
            }else{
                if(DB::table("merchants")->where("id",$id)->update($dataIn)){
                    return redirect("/admin/alipayopen/merchantmanagement");
                }
            }
        }else{
            return back()->with("warnning","请按格式输入正确的手机号");
        }



    }

    public function mmshoplists(Request $request)
    {
        $auth = Auth::user()->can('bindShops');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
//        dd($request->all());
        $id='';
        if($request->only('id')){
            $id=$request->id;
            $list=MerchantShops::where('merchant_id',$id)->get();
//            dd($list);
        }
        if ($list){
            $data = $list->toArray();
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
        }else{
            $datapage='';
            $paginator='';
        }
        return view('admin.alipayopen.merchantshops', ['data'=>$datapage,'paginator'=>$paginator,'id'=>$id]);
    }
    public function mmshopbind(Request $request)
    {
        $auth = Auth::user()->can('bindShops');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $id=$request->id;
        $list=Merchant::where('id',$id)->first();
        $name=$list->name;
        $where['default']=[];
        $where['oalipay']=[];
        if(!Auth::user()->hasRole('admin')){
            $u_id=Auth::user()->id;
            $where['default'][]=['user_id',$u_id];
            $where['oalipay'][]=['promoter_id',$u_id];
        }
        //匹配店铺
        //口碑
        $first= DB::table('alipay_shop_lists')
            ->where('main_shop_name',$name)
            ->where('audit_status','AUDIT_SUCCESS')
            ->where('shop_id','<>','')
            ->where('is_delete',0)
            ->where($where['default'])
            ->select('store_id','main_shop_name as store_name')
            ->get()->toArray();
        if(!empty($first)){
            foreach($first as $k=>$v){
                $first[$k]->store_name=$v->store_name."(口碑店)";
                $first[$k]->store_type='salipay';
            }
        }
        //当面付
        $second= DB::table('alipay_app_oauth_users')
            ->where('auth_shop_name',$name)
            ->where('is_delete',0)
            ->where($where['oalipay'])
            ->select('store_id','auth_shop_name as store_name')
            ->get()->toArray();
        if(!empty($second)){
            foreach($second as $k=>$v){
                $second[$k]->store_name=$v->store_name."(当面付)";
                $second[$k]->store_type='oalipay';
            }
        }
        //微信
        $third= DB::table('weixin_shop_lists')
            ->where('store_name',$name)
            ->where($where['default'])
            ->select('store_id','store_name')
            ->get()->toArray();
        if(!empty($third)){
            foreach($third as $k=>$v){
                $third[$k]->store_name=$v->store_name."(微信支付)";
                $third[$k]->store_type='weixin';
            }
        }
        //平安
        $four= DB::table('pingan_stores')
            ->where('alias_name',$name)
            ->where('is_delete',0)
            ->where($where['default'])
            ->select('external_id as store_id','alias_name as store_name')
            ->get()->toArray();
        if(!empty($four)){
            foreach($four as $k=>$v){
                $four[$k]->store_name=$v->store_name."(平安通道)";
                $four[$k]->store_type='pingan';
            }
        }
        //银联
        $five=UnionPayStore::where('alias_name',$name)
            ->where('is_delete',0)
            ->where($where['default'])
            ->select('store_id','alias_name as store_name')
            ->get()->toArray();
        if(!empty($five)){
            foreach($five as $k=>$v){
                $five[$k]['store_name']=$v['store_name']."(银联通道)";
                $five[$k]['store_type']='unionpay';
            }
        }
        //浦发
        $six=PufaStores::where('merchant_short_name',$name)
            /*->where('is_delete',0)*/
            ->where($where['default'])
            ->select('store_id','merchant_short_name as store_name')
            ->get()->toArray();
        if(!empty($six)){
            foreach($six as $k=>$v){
                $six[$k]['store_name']=$v['store_name']."(浦发通道)";
                $six[$k]['store_type']='pufa';
            }
        }
        //微众
        $seven=WeBankStore::where('alias_name',$name)
            ->where('is_delete',0)
            ->where($where['default'])
            ->select('store_id','alias_name as store_name')
            ->get()->toArray();
        if(!empty($seven)){
            foreach($seven as $k=>$v){
                $seven[$k]['store_name']=$v['store_name']."(微众通道)";
                $seven[$k]['store_type']='webank';
            }
        }
        //民生
        $eight=DB::table('ms_stores')->where('store_short_name',$name)
            /*->where('is_delete',0)*/
            ->where($where['default'])
            ->select('store_id','store_short_name as store_name')
            ->get()->toArray();
        if(!empty($eight)){
            foreach($eight as $k=>$v){
                $eight[$k]->store_name=$v->store_name."(民生通道)";
                $eight[$k]->store_type='ms';
            }
        }
        //新大陆
        $nine=MercRegist::where("store_name",$name)
            ->where("is_delete",0)
            ->where($where['default'])
            ->select("store_id","store_name")
            ->get()
            ->toArray();
        if(!empty($nine)){
            foreach($nine as $k=>$v){
                $nine[$k]['store_name']=$v['store_name']."(新大陆通道)";
                $nine[$k]['store_type']='newland';
            }
        }


        //银联钱包
        $ten=UnionStore::where('store_name',$name)
            /*->where('is_delete',0)*/
            ->where($where['default'])
            ->select('store_id','store_name')
            ->get()->toArray();
        if(!empty($ten)){
            foreach($ten as $k=>$v){
                $ten[$k]['store_name']=$v['store_name']."(银联钱包通道)";
                $ten[$k]['store_type']='union';
            }
        }

        return view('admin.alipayopen.merchantshopbind', compact('first','second','third','four','five','six','seven','eight',"nine",'ten','name','id'));
    }
    public function mmpostdata(Request $request)
    {
        $auth = Auth::user()->can('MerchantManagement ');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $type=['oalipay'=>'支付宝当面付','salipay'=>'口碑门店收款','pingan'=>'平安通道','weixin'=>'官方微信支付','pufa'=>'浦发通道','unionpay'=>'银联通道','ms'=>'民生通道','webank'=>'微众通道','newland'=>"新大陆通道",'union'=>'银联钱包通道'];
        $insertdata=[];
        $data=$request->only('store_id','merchant_id','store_name');
        if($data){
            $insertdata['merchant_id']=$data['merchant_id'];
            $insertdata['store_name']=$data['store_name'];
            $strIdType=explode('**',$data['store_id'],2);
            $insertdata['store_id']=$strIdType[0];
            if(MerchantShops::where('store_id',$strIdType[0])->first()){
                return back()->with('error','商铺已经被绑定,绑定失败!');
                die();
            }else{
                $insertdata['store_type']=$strIdType[1];
                $insertdata['desc_pay']=$type[strtolower($strIdType[1])];
                $insertdata['status']=1;
                $insertdata['created_at']=date('y-m-d h:i:s',time());
                $insertdata['updated_at']=date('y-m-d h:i:s',time());
                if(MerchantShops::insertGetId($insertdata)){
                    return back()->with('success','绑定成功');
                    die();
                }else{
                    return back()->with('error','绑定失败!请重试');
                    die();
                }
            }

        }
        return back();
    }
    //解除绑定
    public function mmshopdelpost(Request $request){
        $auth = Auth::user()->can('MerchantManagement ');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $id=$request->id;
        $resjs='';
        try{
            $res=MerchantShops::where('store_id',$id)->delete();
            if($res){
                $resjs=1;
            }else{
                $resjs=0;
            }
        }catch(Exception $e){
            $resjs=$e->getMessage();
        }
        return json_encode($resjs);
    }

}
?>