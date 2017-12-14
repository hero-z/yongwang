<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2016/12/18
 * Time: 22:48
 */

namespace App\Http\Controllers\UnionPay;


use App\Http\Controllers\Controller;
use App\Models\PinganConfig;
use App\Models\UnionPayConfig;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{


    public function set()
    {
        $auth = Auth::user()->can('unionpaySet');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $c = UnionPayConfig::where('id', 1)->first();
        if ($c) {
            $c = $c->toArray();
        }
        return view('admin.UnionPay.config', compact('c'));
    }

    public function setPost(Request $request)
    {
        $c = UnionPayConfig::where('id', 1)->first();
        $data = [
            'id' => 1,
            'acquirer_id' => $request->get("acquirer_id"),
            'app_id' => $request->get("app_id"),
            'rsa_private_key' => $request->get("rsa_private_key"),
            'union_public_key' => $request->get("union_public_key"),
        ];
        if ($c) {
            UnionPayConfig::where('id', 1)->update($data);
        } else {
            UnionPayConfig::create($data);
        }
        return json_encode([
            'status' => 1,
        ]);
    }
}