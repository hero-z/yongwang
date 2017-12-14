<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Push\JpushController;
use App\Merchant;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTFactory;

class AuthMerchantController extends Controller
{
    use AuthenticatesUsers;

    protected function guard()
    {

        return auth()->guard('merchantApi');//检查用户是否是登陆
    }

    public function __construct()
    {
        $this->middleware('guest.merchant', ['except' => 'logout']);
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required', 'password' => 'required',
        ], [
            "required" => "msg"
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw new AuthenticationException("msg");
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user());
    }

    public function username()
    {
        return 'phone';
    }

    public function authenticated(Request $request, $user)
    {
        $imei = $request->get('imei');
        $phone=$request->get('phone');
        $token = JWTAuth::fromUser($user);

        //传送安卓识别码
        if ($imei) {
            try {
                //清除旧的手机号登陆状态
                $old= Merchant::where('phone',$phone)->first();
                if ($old&&$old->imei!=$imei){
                    $push=  new JpushController();
                    $push->push_out($old->imei);
                }
                Merchant::where('phone',$phone)->update(['imei' => $imei]);

            } catch (\Exception $exception) {

            }
        }
        return response()->json([
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);
        $customClaims = ['phone' => $request->get('phone')];
        if ($this->guard()->attempt($credentials, $customClaims)) {
            return $this->sendLoginResponse($request);
        }
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    //注册用户接口 返回token
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'phone' => 'required|unique:merchants|min:11|max:11',
            'password' => 'required|min:6|confirmed',
        ], [
            'required' => ':attribute 为必填项',
            'min' => ':attribute 长度不符合要求',
            'max' => ':attribute 长度不符合要求',
            'unique' => '已经被人占用'
        ], [
            'name' => '店铺名称',
            'phone' => '手机',
            'password' => '密码',
        ]);
        $newUser = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'password' => bcrypt($request->get('password'))
        ];
        $user = Merchant::create($newUser);
        if ($user) {
            $token = JWTAuth::fromUser($user);//根据用户得到token
            return response()->json(compact('token'));
            // $token = JWTAuth::fromUser($user);//根据用户得到token
        }
    }

    //
    public function getAuthenticatedUser(Request $request)
    {
        JWTAuth::setToken(JWTAuth::getToken());
        $claim = JWTAuth::getPayload();
        try {
            if (!$claim = JWTAuth::getPayload()) {
                return response()->json(array('message' => 'user_not_found'), 404);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(array('message' => 'token_expired'), $e->getCode());
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(array('message' => 'token_invalid'), $e->getCode());
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(array('message' => 'token_absent'), $e->getCode());
        }
        return response()->json(array('status' => 'success', 'data' => $claim['sub']));
        //  $token= $request->id;
        //  JWTAuth::setToken($token);
        //  $authuser = JWTAuth::toUser(JWTAuth::getToken());
        //   dd($authuser);
        //  $user = JWTAuth::parseToken()->authenticate();
        //  return response()->json(compact('user'));
    }

    public function logout()
    {
        $this->guard()->logout();
    }
}