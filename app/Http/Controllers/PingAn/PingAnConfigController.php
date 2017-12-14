<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2016/12/18
 * Time: 22:48
 */

namespace App\Http\Controllers\PingAn;


use App\Http\Controllers\Controller;
use App\Models\PinganConfig;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PingAnConfigController extends Controller
{


    public function pinganconfig()
    {
        $auth = Auth::user()->can('pinganconfig');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $c = PinganConfig::where('id', 1)->first();
        if ($c) {
            $c = $c->toArray();
        }
        return view('admin.pingan.config', compact('c'));
    }

    public function savepinganconfig(Request $request)
    {
        $auth = Auth::user()->can('pinganconfig');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $c = PinganConfig::where('id', 1)->first();
        $data = [
            'id' => 1,
            'rsaPrivateKey' => $request->get("rsaPrivateKey"),
            'app_id' => $request->get("app_id"),
            'wx_app_id' => $request->get("wx_app_id"),
            'wx_secret' => $request->get("wx_secret"),
            'pinganrsaPublicKey'=>$request->get("pinganrsaPublicKey")
        ];
        if ($c) {
            PinganConfig::where('id', 1)->update($data);
        } else {
            PinganConfig::create($data);
        }
        return json_encode([
            'status' => 1,
        ]);
    }
}