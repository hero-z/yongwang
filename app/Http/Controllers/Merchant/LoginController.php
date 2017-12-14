<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/2/28
 * Time: 16:41
 */

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Extensions\AuthenticatesLogout;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    //  use AuthenticatesUsers;
    use AuthenticatesUsers, AuthenticatesLogout {
        AuthenticatesLogout::logout insteadof AuthenticatesUsers;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/merchant/index';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest.merchant', ['except' => 'logout']);
    }

         /*
         *  退出登录 和服务商分开 单独登录
         */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->forget($this->guard()->getName());

        $request->session()->regenerate();

        return redirect('/merchant/login');
    }

    /**
     * 显示后台登录模板
     */
    public function showLoginForm()
    {
        return view('merchant.login');
    }

    /**
     * 使用 merchant guard
     */
    protected function guard()
    {
        return auth()->guard('merchant');
    }

    /**
     * 重写验证时使用的用户名字段 验证手机号
     */
    public function username()
    {
        return 'phone';
    }
}