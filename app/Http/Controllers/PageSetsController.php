<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/2/14
 * Time: 18:29
 */

namespace App\Http\Controllers;


use App\Models\PageSets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageSetsController extends Controller
{

    public function setPage(Request $request)
    {
        $auth = Auth::user()->can('setRemind');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $type = $request->get('type');
        //WxNotify
        if ($type == 'WxNotify') {
            $WxPayNotify = PageSets::where('id', 1)->first();
            if ($WxPayNotify) {
                $WxPayNotify->toArray();
            }
            return view('admin.set.WxNotify', compact('WxPayNotify'));
        }


    }

    public function setPagePost(Request $request)
    {
        $data = $request->except(['_token', 'type']);
        try {

            if ($request->get('type') == "WxNotify") {
                PageSets::where('id',1)->update($data);
            }
        } catch (\Exception $exception) {
            return json_encode([
                'status' => 0
            ]);
        }

        return json_encode([
            'status' => 1
        ]);
    }

}