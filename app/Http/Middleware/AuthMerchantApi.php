<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/2/26
 * Time: 18:33
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthMerchantApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null )
    {
        try {
           $token= JWTAuth::setToken(JWTAuth::getToken());
        } catch (JWTException $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['massage'=>'Token 参数无效','status_code'=>401]);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['massage'=>'Token 过期','status_code'=>402]);
        }else{
                return response()->json(['massage'=>'系统出错了-1','status_code'=>500]);
            }
        }

        return $next($request);

    }

}