<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/20
 * Time: 9:18
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Models\Role;
use App\Models\RoleUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mockery\CountValidator\Exception;

class UsersController extends AlipayOpenController
{

    public function users(Request $request)
    {
        $auth = Auth::user()->can('users');
        $u_id = Auth::user()->id;
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $qq=$request->isMethod('ajax');
//        dd($qq);
        $user = User::where("is_delete",0)->where('level',2)->get();
//        if(Auth::user()->level==1){
//        }elseif(Auth::user()->level==2){
//            $user = User::where("is_delete",0)->where('level',3)->where('pid',$u_id)->get();
//        }else{
//            die('非法权限!');
//        }
        return view('admin.alipayopen.users.users', compact('u_id'));
    }
    public function ajaxusers(Request $request){
        try {
            //检查管理员
            $admin = Auth::user()->hasRole('admin');
            $auth = Auth::user()->can('users');
            $u_id = Auth::user()->id;
            if (!$auth) {
                echo '你没有权限操作！';
                die;
            }
            if ($admin) {
                $userInfo = User::where("is_delete",0)->get();
            } else {
                $s=User::where("is_delete",0)->where('pid',$u_id)->get();
                $b=[];
                if($s){
                    foreach($s as $v){
                        $b[]=$v->id;
                    }
                }
                $userInfo=User::whereIn("pid",$b)
                    ->where("is_delete",0)
                    ->orwhere('pid',$u_id)
                    ->orwhere('id',$u_id)
                    ->orderBy('level','ASC')
                    ->get();
            }
            return json_encode($userInfo);

        } catch (\Exception $e) {
            return json_encode([
                "status_code" => $e->getCode(),
                "message" => $e->getMessage()
            ]);
        }
    }
    public function edituser(Request $request){
        $uid=$request->id;
        $userinfo=User::where('id',$uid)->first();
        $piduser=User::where('id',$userinfo->pid)->first();
        return json_encode([$userinfo,$piduser]);
    }
    public function doedituser(Request $request){
        $uid=$request->id;
        $uname=$request->name;
        $rate=$request->rate;
        $phone=$request->phone;
        $user=User::where('id',$uid)->first();
        if(($user->level-Auth::user()->level)>1){
            return json_encode([
                "code" => 0,
                "msg" => '非直接下级,不能修改'
            ]);
        }
        $rules = [
            'name' => 'required|max:255',
            'phone' => 'required|min:11|max:11',
        ];
        $message = [
            'required' => ':attribute 不能为空',
            'between' => '密码必须是6~20位之间',
            'phone.max' => '手机号长度不正确',
            'phone.min' => '手机号长度不正确',
            'confirmed' => '新密码和确认密码不匹配'
        ];
        $validator = Validator::make($request->except('_token','id'), $rules,$message);
        $msg='';
        $ck=true;
        if($validator->errors()->get("name")){
            $msg.=$validator->errors()->get("name")[0]."\n";
            $ck=false;
        }
        if($validator->errors()->get("phone")) {
            $msg.=$validator->errors()->get("phone")[0]."\n";
            $ck=false;
        }
        $checkphone=User::where("phone",$request->get("phone"))->where("id","!=",$uid)->first();
        if($checkphone){
            $msg.="手机号已被占用！\n";
            $ck=false;
        }
        $checkname=User::where("name",$request->get("name"))->where("id","!=",$uid)->first();
        if($checkname){
            $msg.="名称已被占用！\n";
            $ck=false;
        }
        $update['name']=$uname;
        $update['phone']=$phone;
        //设置费率逻辑
        if(Auth::user()->hasRole('admin')||Auth::user()->id!=$uid){
            $ratelimit=DB::table('rate_limit')->where('id',1)->first();
            if($ratelimit){
                $minrate=$ratelimit->minrate;
                $maxrate=$ratelimit->maxrate;
                if(empty($minrate)||empty($maxrate)){
                    $msg.='请先设置费率区间'."\n";
                    $ck=false;
                }else{
                    if(Auth::user()->level==1){
                        if($rate<$minrate){
                            $msg.='费率必须不小于最低费率:'.$minrate."\n";
                            $ck=false;
                        }
                        if($rate>$maxrate){
                            $msg.='费率必须不大于最高费率:'.$maxrate."\n";
                            $ck=false;
                        }
                    }else{
                        if($rate<$minrate||$rate<Auth::user()->rate){
                            $msg.='费率必须不小于最低费率:'.$minrate.'且不小于上级费率:'.Auth::user()->rate."\n";
                            $ck=false;
                        }
                        if($rate>$maxrate){
                            $msg.='费率必须不大于最高费率:'.$maxrate."\n";
                            $ck=false;
                        }
                    }
                }
            }else{
                $msg.='请先设置费率区间'."\n";
                $ck=false;
            }
            $update['rate']=$rate;
        }
        if(!$ck){
            return json_encode([
                "code" => 0,
                "msg" => $msg
            ]);
        }
        if(User::where('id',$uid)->update($update)){
            return json_encode([
                "code" => 1,
                "msg" => '修改成功'
            ]);
        }else{
            return json_encode([
                "code" => 0,
                "msg" => '修改失败'
            ]);
        }
    }
    //添加用户
    public function useradd(Request $request)
    {
        $auth = Auth::user()->can('addUser');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $data = $request->all();
        $data['rate'] = (float)$request->rate;
        $minrate=Auth::user()->rate;
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|min:11|max:11',
            'rate' => 'min:0.5',
//            'rate' => 'min:'.$minrate.'|max:'.$maxrate,
        ];
        $messages = [
            'required' => ':attribute 不能为空',
            'between' => '密码必须是6~20位之间',
            'phone.max' => '手机号长度不正确',
            'phone.min' => '手机号长度不正确',
            'email' => '邮箱格式不正确',
            'email.unique' => '邮箱已经被占用',
//            'rate.max' => '费率不能大于'.$maxrate,
//            'rate.min' => '费率不能小于'.$minrate,
            'password.min' => '密码必须大于6位',
            'confirmed' => '新密码和确认密码不匹配'
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator);  //返回一次性错误
        }
        $rolewhere=[];
        $msg=[];
        $ck=true;
        $ratelimit=DB::table('rate_limit')->where('id',1)->first();
        if($ratelimit){
            $minrate=$ratelimit->minrate;
            $maxrate=$ratelimit->maxrate;
            if(empty($minrate)||empty($maxrate)){
                $msg['rate']='请先设置费率区间';
                $ck=false;
            }
            if($data['rate']<$minrate||$data['rate']<Auth::user()->rate){
                $msg['rate']='费率必须不小于最低费率:'.$minrate.'且不小于上级费率:'.Auth::user()->rate;
                $ck=false;
            }
            if($data['rate']>$maxrate){
                $msg['rate']='费率必须不大于最高费率:'.$maxrate;
                $ck=false;
            }
        }else{
            $msg['rate']='请先设置费率区间';
            $ck=false;
        }
        if(Auth::user()->level==1){
            $rolewhere[]=['name','代理商'];
            $mk='请先添加名称为\'代理商\'的角色';
        }elseif(Auth::user()->level==2){
            $rolewhere[]=['name','员工'];
            $mk='请先添加名称为\'员工\'的角色';
        }else{
            $mk='第三级别员工无权限添加!';
            $ck=false;
        }
        $role=Role::where($rolewhere)->first();
        if(!$role){
            $msg['name']=$mk;
            $ck=false;
        }
        if(!$ck){
            return back()->withErrors($msg);
        }
        $istres = User::insertGetId([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'rate' => trim($data['rate']),
            'level' => trim($data['level']),
            'pid' => Auth::user()->id,
            'password' => bcrypt($data['password']),
            'created_at' => date("Y-m-d H:i:s"),
        ]);
        if ($istres) {
            $user_id = $istres;//用户id
            $user = User::where('id', $user_id)->first();
            RoleUser::where('user_id', $user_id)->delete();
            $user->attachRole($role->id);
            return redirect('/admin/alipayopen/users');

        } else {
            return back()->withErrors(['name'=>'添加用户失败']);
        }
    }
    public function ajaxpasswd(Request $request){
        $id=$request->get("id");
        $confirm_password = $request->get('confirm_password');
        $password = $request->get('password');
        //有密码的话修改密码
        if ($confirm_password|| $password) {
            $data = $request->all();
            $rules = [
                'password' => 'required|between:6,20',
            ];
            $messages = [
                'required' => '密码不能为空',
                'between' => '密码必须是6~20位之间',
            ];
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->fails()) {
                return json_encode([
                    "code" => 0,
                    "msg" => $validator
                ]);
            }
            $datas['password'] = bcrypt($password);
            try{
                $user=User::where("id",$id)->update($datas);
                if($user){
                    return json_encode([
                        "code" => 1,
                        "msg" => "密码修改成功"
                    ]);
                }else{
                    return json_encode([
                        "code" => 0,
                        "msg" => "密码修改失败"
                    ]);
                }
            }catch(\Exception $e){
                return json_encode([
                    "code" => $e->getCode(),
                    "msg" => $e->getMessage()
                ]);
            }
        }
    }
    public function setrate(){
        if(Auth::user()->can('setrate')){
            $min='';
            $max='';
            $rate=DB::table('rate_limit')->where('id',1)->first();
            if($rate){
                $min=$rate->minrate;
                $max=$rate->maxrate;
            }
            return view('admin.alipayopen.users.setrate',compact('min','max'));
        }
        return back();
    }
    public function dosetrate(Request $request){
        if(!Auth::user()->can('setrate')){
            return back();
        }
        $minrate=$request->minrate;
        $maxrate=$request->maxrate;
        $msg=[];
        $ck=true;
        if($minrate<=0||$minrate>100){
            $msg['minrate']='最低费率必须大于0且小于100%';
            $ck=false;
        }
        if($maxrate<$minrate||$maxrate>100){
            $msg['maxrate']='最低费率必须大于0且小于100%';
            $ck=false;
        }
        if(!$ck){
            return back()->withErrors($msg);
        }
        try{
            $rate=DB::table('rate_limit')->where('id',1)->first();
            if($rate){
                DB::table('rate_limit')->where('id',1)->update([
                    'minrate'=>$minrate,
                    'maxrate'=>$maxrate
                ]);
            }else{
                DB::table('rate_limit')->insert([
                    'id'=>1,
                    'minrate'=>$minrate,
                    'maxrate'=>$maxrate
                ]);
            }
            return redirect(route('users'));
        }catch (Exception $e){
            Log::info($e);
        }
        return back();
    }
    public function updateu(Request $request)
    {
        $auth = Auth::user()->can('editUser');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $user = User::where('id', $request->get('id'))->first();
        if ($user) {
            $user = $user->toArray();
        }
        return view('admin.alipayopen.users.updateu', compact('user'));
    }

    //admin  删除账号
    public function deleteu(Request $request)
    {
        $auth = Auth::user()->can('deleteUser');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $id=$request->get('id');
        $list=DB::table('role_user')
            ->join("roles","role_user.role_id","=","roles.id")
            ->where("role_user.user_id",$id)
            ->where("roles.name","admin")
            ->first();
        //管理员ID
        $datass['is_delete']=1;
        $admin=Auth::user()->id;
        $data['promoter_id']=$admin;
        $datas['user_id']=$admin;
        //该员工名下所有店铺转移至admin
        if(Auth::user()->hasRole('admin')&&!$list){
            try{
                $a=DB::table('alipay_app_oauth_users')->where("promoter_id",$id)->update($data);
                $b=DB::table("alipay_shop_lists")->where('user_id',$id)->update($datas);
                $c=DB::table('weixin_shop_lists')->where("user_id",$id)->update($datas);
                $d=DB::table("pingan_stores")->where("user_id",$id)->update($datas);
                $e=DB::table("pufa_stores")->where("user_id",$id)->update($datas);
                $e=DB::table("ms_stores")->where("user_id",$id)->update($datas);
                $e=DB::table("we_bank_stores")->where("user_id",$id)->update($datas);
                $e=DB::table("union_pay_stores")->where("user_id",$id)->update($datas);
                $user = User::where('id', $id)->update($datass);

                return json_encode(['success' => 1,'msg'=>'SUCCESS']);
            }catch(\Exception $e){
                return json_encode(['success' => 0,'msg'=>$e->getMessage().$e->getLine()]);
            }
        }else{
            return json_encode(['success' => 0,'msg'=>'没有权限操作']);
        }


    }
    //admin彻底删除
    public function deluserlist(){
        $auth = Auth::user()->can('dropUser');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $user=User::where('is_delete',1)->paginate();
        return view('admin.alipayopen.users.dellist',compact('user'));
    }
    //执行删除
    public function dropuser(Request $request){
        $id=$request->id;
        $auth = Auth::user()->can('dropUser');
        if (!$auth) {
            return json_encode(['status'=>0,'msg'=>'没有权限操作']);
        }
        $del=User::where('is_delete',1)->where('id',$id)->delete();
        if($del){
            return json_encode(['status'=>1,'msg'=>'SUCCESS']);
        }else{
            return json_encode(['status'=>0,'msg'=>'数据库删除失败!']);
        }
    }
    //恢复在职
    public function userback(Request $request){
        $id=$request->id;
        $del=User::where('is_delete',1)->where('id',$id)->update(['is_delete'=>0]);
        if($del){
            return json_encode(['status'=>1,'msg'=>'SUCCESS']);
        }else{
            return json_encode(['status'=>0,'msg'=>'数据库操作失败!']);
        }
    }
    //admin修改账号信息
    public function updateuSave(Request $request)
    {
        $auth = Auth::user()->can('editUser');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $email = $request->get('email');
        $user = User::where('email', $email)->first();
        $password_confirm = $request->input('password_confirm');
        $password = $request->input('password');
        //有密码的话修改密码
        if ($password_confirm || $password) {
            $data = $request->all();
            $rules = [
                'password' => 'required|between:6,20|confirmed',
            ];
            $messages = [
                'required' => '密码不能为空',
                'between' => '密码必须是6~20位之间',
                'confirmed' => '新密码和确认密码不匹配'
            ];
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->fails()) {
                return back()->withErrors($validator);  //返回一次性错误
            }
            $user->password = bcrypt($password);
            $user->name = $request->get('name');
            $user->phone = $request->get('phone');
            $user->email = $request->get('email');
            $user->id = $request->get('id');
            $user->save();
            return redirect(route('users'));
        } //没有密码 跳过验证修改其他信息
        else {
            $user->name = $request->get('name');
            $user->phone = $request->get('phone');
            $user->email = $request->get('email');
            $user->id = $request->get('id');
            $user->save();
            return redirect(route('users'));
        }
    }

}