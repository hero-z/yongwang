<?php

namespace App\Api\Controllers;

use App\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;


class AuthController extends BaseController
{
    //用户登录返回token接口
    public function authenticate(Request $request)
    {
        // 这里的登陆验证是email和password
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid778_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));


    }

    //注册用户接口 返回token
    public function register(Request $request)
    {
        $newUser = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone'=>$request->get('phone'),
            'password' => bcrypt($request->get('password'))
        ];

        $user = User::create($newUser);
        $token = JWTAuth::fromUser($user);//根据用户得到token
        return response()->json(compact('token'));
        // $token = JWTAuth::fromUser($user);//根据用户得到token


    }

    public function getAuthenticatedUser()
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }
        // the token is valid and we have found the user via the sub claim

        return response()->json(compact('user'));
    }


}