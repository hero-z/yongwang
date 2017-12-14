<?php

namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\Controller;
use App\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Extensions\AuthenticatesLogout;

class RegisterController extends Controller
{
    //use AuthenticatesUsers;
    use AuthenticatesUsers, AuthenticatesLogout {
        AuthenticatesLogout::logout insteadof AuthenticatesUsers;
    }

    //显示页面
    public function showRegister()
    {
        return view('merchant.register');
    }

    //用户注册
    public function register(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validateRegister($request->input());
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $user = new Merchant();
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->password = bcrypt($request->password);
            $user->created_at = time();
            $user->updated_at = time();
            if ($user->save()) {
                return redirect(url('merchant/login'));
            } else {
                return back()->with('error', '注册失败！')->withInput();
            }
        }
        return view('merchant.register');
    }

    protected function validateRegister(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|alpha_num|max:255',
            'phone' => 'required|unique:merchants|min:11|max:11',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6|'
        ], [
            'required' => ':attribute 为必填项',
            'min' => ':attribute 长度不符合要求',
            'max' => ':attribute 长度不符合要求',
            'confirmed' => '两次输入的密码不一致',
            'unique' => '该手机已经被人占用',
            'alpha_num' => ':attribute 必须为字母或数字'
        ], [
            'name' => '昵称',
            'phone' => '手机',
            'password' => '密码',
            'password_confirmation' => '确认密码'
        ]);
    }

    /**
     * 使用 merchant guard
     */
    protected function guard()
    {
        return auth()->guard('merchant');
    }

}
