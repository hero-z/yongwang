<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Merchant;
use App\Models\SmsCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{

    public function setPassword()
    {
        return view('merchant.setPassword.set');

    }

    public function setPasswordPost(Request $request)
    {
        $code = $request->get('code');
        try {
            $Rcode = SmsCode::where('code', $request->get('phone'))->first()->value;
            if ($code != $Rcode) {
            return back()->with('code','验证码不正确');
            }
        } catch (\Exception $exception) {
           
        }
        $data = $request->all();
        $rules = [
            'password' => 'required|min:6|confirmed',
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
        Merchant::where('phone', $data['phone'])->update([
            'password' => bcrypt($data['password']),
        ]);

        return redirect('/merchant/login');
    }
}
