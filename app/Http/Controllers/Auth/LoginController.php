<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Extensions\AuthenticatesLogout;

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

   // use AuthenticatesUsers;
    use AuthenticatesUsers, AuthenticatesLogout {
        AuthenticatesLogout::logout insteadof AuthenticatesUsers;
    }
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    public function login(Request $request)
    {
        try{
            $email=$request->get("email");
            $this->validateLogin($request);
            $is_delete=DB::table('users')->where("email",$email)->where("is_delete",0)->first();
            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the username and
            // the IP address of the client making these requests into this application.
            if($is_delete){
                if ($this->hasTooManyLoginAttempts($request)) {
                    $this->fireLockoutEvent($request);

                    return $this->sendLockoutResponse($request);
                }

                if ($this->attemptLogin($request)) {
                    return $this->sendLoginResponse($request);
                }

//         If the login attempt was unsuccessful we will increment the number of attempts
//         to login and redirect the user back to the login form. Of course, when this
//         user surpasses their maximum number of attempts they will get locked out.
//        $this->incrementLoginAttempts($request);

                return $this->sendFailedLoginResponse($request);
            }else{
                return redirect()->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors([
                        $this->username() =>"用户不存在",
                    ]);
            }
        }catch(\Exception $e){

        }


    }
    protected $redirectTo = '/admin/alipayopen';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /*
     *  退出登录
     */
    public function logout()
    {
        if (Auth::check()) {
            Auth::logout();
        }
        return Redirect::to('login');
    }
}
