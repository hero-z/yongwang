<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use App\Models\WeixinPayConfig;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    //
    public function spset()
    {
        $c = WeixinPayConfig::find(1);
        return view('admin.weixin.spset', compact('c'));
    }


    public function spsetPost(Request $request)
    {
        $data = $request->except(['_token']);
        $re = WeixinPayConfig::where('id', 1)->update($data);
        if ($re) {
            $json = [
                'status' => 1,
            ];
        } else {
            $json = [
                'status' => 0,
            ];
        }

        return json_encode($json);
    }

}
