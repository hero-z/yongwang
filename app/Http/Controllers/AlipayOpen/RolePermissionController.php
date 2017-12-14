<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/1/4
 * Time: 15:30
 */

namespace App\Http\Controllers\AlipayOpen;


use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\RoleUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolePermissionController extends AlipayOpenController
{
    //权限分配
    public function assignment(Request $request)
    {
        if (Auth::user()->hasRole('admin')) {
            $role_id = $request->get('role_id');//角色id
            $role = Role::where('id', $role_id)->first();
            $permission = Permission::all();//所有权限
            $PermissionRole = PermissionRole::where('role_id', $role_id)->get();
            $p_id = [];
            foreach ($PermissionRole as $v) {
                $p_id[] = $v->permission_id;
            }
            return view('admin.alipayopen.role.assignment', compact('role', 'permission', 'p_id'));
        }else{
            $auth = Auth::user()->can('rolePermission');
            if (!$auth) {
                echo '你没有权限操作！';die;
            }
            $role_id = $request->get('role_id');//角色id
            $role = Role::where('id', $role_id)->first();
            $permission = Permission::all();//所有权限
            $PermissionRole = PermissionRole::where('role_id', $role_id)->get();
            $p_id = [];
            foreach ($PermissionRole as $v) {
                $p_id[] = $v->permission_id;
            }
            return view('admin.alipayopen.role.assignment', compact('role', 'permission', 'p_id'));
        }




    }

    //权限分配提交
    public function assignmentpost(Request $request)
    {
        $auth = Auth::user()->can('role');
        if (!$auth) {
            echo '你没有权限操作！';die;
        }
        $role_id = $request->get('role_id');
        if ($role_id==1){
            $data=Permission::all()->toArray();
            $role = Role::where('id', $role_id)->first();
            PermissionRole::where('role_id', 1)->delete();
            foreach ($data as $v) {
                // $permission = Permission::where('id', $v)->first();
                $role->attachPermission($v['id']);//追加权限到这个角色里面
            }
        }else{
            $data = $request->except(['_token', 'role_id']);
            $role = Role::where('id', $role_id)->first();
            PermissionRole::where('role_id', $role_id)->delete();
            foreach ($data as $v) {
                // $permission = Permission::where('id', $v)->first();
                $role->attachPermission($v);//追加权限到这个角色里面
            }
        }
        return redirect('/admin/alipayopen/role');
    }

    //删除角色
    public function delRole(Request $request)
    {
        $auth = Auth::user()->can('deleteRole');
        if (!$auth) {
            $data = [
                'status' => 0,
            ];
        }else{
            $role_id = $request->get('role_id');
            if ($role_id != 1) {
                $re = Role::where('id', $role_id)->delete();
            } else {
                $re = 0;
            }
            if ($re) {
                $data = [
                    'status' => 1,
                ];
            } else {
                $data = [
                    'status' => 0,
                ];
            }
        }

        return json_encode($data);
    }

    //设置角色
    public function setRole(Request $request)
    {
        $auth = Auth::user()->can('manageUser');
        if (!$auth) {
            echo '你没有权限操作！';die;
        }
        $user_id = $request->get('user_id');
        $user = User::where('id', $user_id)->first();
        $role = Role::all();
        $RoleUser = RoleUser::where('user_id', $user_id)->get();
        $r_u = [];
        foreach ($RoleUser as $v) {
            $r_u[] = $v->role_id;
        }
        return view('admin.alipayopen.users.setrole', compact('user', 'r_u', 'role'));
    }

    //角色提交
    public function setRolePost(Request $request)
    {
        $auth = Auth::user()->can('role');
        if (!$auth) {
            echo '你没有权限操作！';die;
        }
        $data = $request->except(['_token', 'user_id']);//角色列表
        $user_id = $request->get('user_id');//用户id
        $user = User::where('id', $user_id)->first();
        RoleUser::where('user_id', $user_id)->delete();
        foreach ($data as $v) {
            $user->attachRole($v);
        }
        return redirect('/admin/alipayopen/users');
    }
}