<?php

namespace App\Http\Controllers\AlipayOpen;

use Alipayopen\Sdk\Request\AlipayOpenAuthTokenAppRequest;
use Alipayopen\Sdk\Request\AlipaySystemOauthTokenRequest;
use App\Models\AlipayAppOauthUsers;
use App\Models\AlipayIsvConfig;
use App\Models\AlipayUser;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Alipayopen\Sdk\AopClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OauthController extends AlipayOpenController
{
    /** 商户授权
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    //应用授权URL拼装
    public function oauth()
    {
        $config = AlipayIsvConfig::where('id', 1)->first();
        if ($config) {
            $config = $config->toArray();
        }
        $url = urlencode($config['callback']);
        $appid = $config['app_id'];
        $app_oauth_url = Config::get('alipayopen.app_oauth_url');
        $code_url = $app_oauth_url . '?app_id=' . $appid . '&redirect_uri=' . $url . "&state=App_" . Auth::user()->id;
        return view('admin.alipayopen.app_auth', compact('code_url'));
    }
    /**商户授权返回函数
     * @param Request $request
     */
//授权回调获取商户信息主要是获取token
    public function callback(Request $request)
    {
        $state = $request->get('state', 'App_6');//个人授权有这个参数商户授权没有这个参数
        $arr = explode('_', $state);
        //第三方应用授权
        if ($arr[0] == "App") {
            //1.初始化参数配置
            $c = $this->AopClient();
            //2.执行相应的接口获得相应的业务
            //获取app_auth_code
            $app_auth_code = $request->get('app_auth_code');
            $promoter_id = $arr[1];
            //使用app_auth_code换取app_auth_token
            $obj = new AlipayOpenAuthTokenAppRequest();
            $obj->setApiVersion('2.0');
            $obj->setBizContent("{" .
                "    \"grant_type\":\"authorization_code\"," .
                "    \"code\":\"$app_auth_code\"," .
                "  }");
            try {
                $data = $c->execute($obj);
                $app_response = $data->alipay_open_auth_token_app_response;
            } catch (\Exception $exception) {
                return redirect('/admin/alipayopen/oauth');
            }
            $model = [
                "user_id" => $app_response->user_id,
                "store_id" => 'o' . $app_response->user_id,
                "app_auth_token" => $app_response->app_auth_token,
                "app_refresh_token" => $app_response->app_refresh_token,
                "expires_in" => $app_response->expires_in,
                "re_expires_in" => $app_response->re_expires_in,
                "auth_app_id" => $app_response->auth_app_id,
                "promoter_id" => $promoter_id,
                "auth_shop_name" => "",
                "auth_phone" => "",
            ];
            $alipay_user = AlipayAppOauthUsers::where('user_id', $app_response->user_id)->where('pid', 0)->first();//如果存在修改信息
            if ($alipay_user) {
                //更新总店
                AlipayAppOauthUsers::where('user_id', $app_response->user_id)->where('pid', 0)
                    ->update($model);
                //更新分店的app_auth_token
                AlipayAppOauthUsers::where('user_id', $app_response->user_id)->update(
                    [
                        "app_auth_token" => $app_response->app_auth_token,
                        "app_refresh_token" => $app_response->app_refresh_token,
                        "expires_in" => $app_response->expires_in,
                        "re_expires_in" => $app_response->re_expires_in,
                    ]
                );
            } else {
                $re = AlipayAppOauthUsers::create($model);//新增信息
            }
            //Cache::put('key', 'value', '527040');//一年
            //这里拿到商户信息如下 auth_token 有效期1年
            //  +"code": "10000"
            // +"msg": "Success"
            // +"app_auth_token": "201610BB7bae5f482d3042b58926dcb331b80X20"
            // +"app_refresh_token": "201610BB206dad017d0049218f89418fb048eX20"
            //  +"auth_app_id": "2016072800112318"
            //  +"expires_in": 31536000
            // +"re_expires_in": 32140800
            //  +"user_id": "2088102168897200"
            return redirect("/alipayopen/userinfo?user_id=" . $app_response->user_id.'&app_auth_token='.$app_response->app_auth_token.'&u_id='.$arr[1]);
        } //A用户授权跳转收款
        if ($arr[0] == "OSK" || $arr[0] == "SXD" || $arr[0] == "PA" || $arr[0] == "PF" || $arr[0] == "MS"|| $arr[0] == "WB") {
            //SYD_2088402162863826  扫码下单 生成二维码 用户输入金额 完成付款
            $type = $arr[0];
            $u_id = $arr[1];
            //当面付 平安
            if ($arr[0] == 'OSK' || $arr[0] == "PA" || $arr[0] == "PF"|| $arr[0] == "MS"|| $arr[0] == "WB"|| $arr[0] == "SXD") {
                if (count($arr) == 3) {
                    $m_id = (int)$arr[2];//收银员id
                } else {
                    $m_id = 0;
            }
            }
            //1.初始化参数配置
            $c = $this->AopClient();
            //2.执行相应的接口获得相应的业务
            //获取app_auth_code
            $app_auth_code = $request->get('auth_code');
            //使用app_auth_code换取接口access_token及用户userId
            $obj = new AlipaySystemOauthTokenRequest();
            $obj->setApiVersion('2.0');
            $obj->setCode($app_auth_code);
            $obj->setGrantType("authorization_code");
            try {
                $data = $c->execute($obj);
                $re = $data->alipay_system_oauth_token_response;

            } catch (\Exception $exception) {
                return redirect('/admin/alipayopen/oauth');
            }
            $request->session()->forget('user_data');
            $request->session()->push('user_data', $re);
            //有门店自带收款码
            if ($type == 'SXD') {
                return redirect(url('admin/alipayopen/alipay_trade_create?u_id=' . $u_id. '&m_id=' . $m_id));//跳转到输入金额页面
            }
            //仅生成收款码
            if ($type == 'OSK') {
                //兼容旧版本
                $first = substr($u_id, 0, 1);
                if ($first != "o") {
                    $u_id = 'o' . $u_id;
                }
                return redirect(url('admin/alipayopen/alipay_oqr_create?u_id=' . $u_id . '&m_id=' . $m_id));//跳转到输入金额页面
            }
            //跳到平安界面
            if ($type == 'PA') {
                return redirect(url('admin/pingan/alipay?external_id=' . $u_id . '&m_id=' . $m_id));//跳转到输入金额页面
            }
            /*
             *  dd($re);
            +"access_token": "composeB758d0ffce2eb4f029d7a1b421b2e4X04"
            +"alipay_user_id": "2088102168684040"
            +"expires_in": 500
            +"re_expires_in": 300
            +"refresh_token": "composeB5ae6765a63b648a1b389aaf72cf9dX04"
            +"user_id": "2088102168684040"
            */

            //调到浦发界面
            if ($type == 'PF') {
                return redirect(url("api/pufa/ppdata?store_id={$u_id}&cashier_id={$m_id}"));//服务商处的生成的店铺id和收银员id
            }
            //授权完成后跳转到民生支付页
            if ($type == 'MS') {
                return redirect(url("api/minsheng/aliform?store_id={$u_id}&cashier_id={$m_id}"));//服务商处的生成的店铺id和收银员id
            }
            //授权完成后跳转到微众支付页
            if ($type == 'WB') {
                return redirect(url("admin/webank/weixin/publicPay?store_id={$u_id}&m_id={$m_id}"));//服务商处的生成的店铺id和收银员id
            }

        }

    }

    /*
     * 个人用户授权 跳转支付界面
     */
    public function auth()
    {
        $url = urlencode(Config::get('alipayopen.redirect_uri'));
        $appid = Config::get('alipayopen.app_id');
        $app_auth_url = Config::get('alipayopen.app_auth_url');
        $code_url = $app_auth_url . '?app_id=' . $appid . '&redirect_uri=' . $url . "&scope=auth_base" . '&state=SXD_2088402162863826';
        return view('layouts.qr', compact('code_url'));
    }

    public function userinfo(Request $request)
    {
        return view('admin.alipayopen.store.userinfo');
    }

    public function userinfoinsert(Request $request)
    {
        $user_id = $request->get('user_id', 1);
        $u_id = $request->get('u_id', 1);
        $auth_shop_name = $request->get('auth_shop_name');
        $auth_phone = $request->get('auth_phone');
        $app_auth_token = $request->get('app_auth_token');
        if ($user_id) {
            if ($auth_shop_name && $auth_phone) {
                $update = [
                    'auth_shop_name' => $auth_shop_name,
                    'auth_phone' => $auth_phone,
                ];
                try {
                    $user = AlipayAppOauthUsers::where('user_id', $user_id)->update($update);

                } catch (\Exception $exception) {
                    echo '出错了！请联系客服';
                }
                return json_encode(['code' => 200, 'app_auth_token' => $app_auth_token,'u_id'=>$u_id]);
//                return json_encode(['code' => 200, 'msg' => "添加成功"]);
            } else {
                return json_encode(['code' => 0, 'msg' => "请填写完整"]);
            }
        } else {
            return redirect('/admin/alipayopen/oauth');//重新跳转授权
        }
    }

    //商家第三方应用授权列表
    public function oauthlist(Request $request)
    {
        $shopname = $request->input("shopname");
        $where=[];
        if($shopname){
            $where[]=["alipay_app_oauth_users.auth_shop_name", 'like', '%' . $shopname . '%'];

        }
        $auth = Auth::user()->can('oauthlist');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        //
        $data = DB::table('users')->where('alipay_app_oauth_users.pid', 0)->where($where)->select('users.name', 'alipay_app_oauth_users.*')->orderBy('updated_at', 'desc')->where('promoter_id', Auth::user()->id)->join('alipay_app_oauth_users', 'alipay_app_oauth_users.promoter_id', '=', 'users.id')->where("alipay_app_oauth_users.is_delete", 0)->get();
        if (Auth::user()->hasRole('admin')) {
            $data = DB::table('users')->where('alipay_app_oauth_users.pid', 0)->select('users.name', 'alipay_app_oauth_users.*')->where($where)->orderBy('updated_at', 'desc')->join('alipay_app_oauth_users', 'alipay_app_oauth_users.promoter_id', '=', 'users.id')->where("alipay_app_oauth_users.is_delete", 0)->get();
        }
        if ($data->isEmpty()) {
            $paginator = "";
            $datapage = "";
        } else {
            $data = $data->toArray();

            //下一版本去掉
            foreach ($data as $v) {
                $store_id = AlipayAppOauthUsers::where('user_id', $v->user_id)->first()->store_id;
                if (!$store_id) {
                    AlipayAppOauthUsers::where('user_id', $v->user_id)->update([
                        'store_id' => 'o' . $v->user_id
                    ]);
                }
            }
            //下一版本去掉结束

            //非数据库模型自定义分页
            $perPage = 9;//每页数量
            if ($request->has('page')) {
                $current_page = $request->input('page');
                $current_page = $current_page <= 0 ? 1 : $current_page;
            } else {
                $current_page = 1;
            }
            $item = array_slice($data, ($current_page - 1) * $perPage, $perPage); //注释1
            $total = count($data);
            $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
            $datapage = $paginator->toArray()['data'];
        }
        return view('admin.alipayopen.store.oauthlist', compact('datapage', 'paginator','shopname'));
    }

    //更改状态(软删除)
    public function changeStatus(Request $request)
    {
        $id = $request->get("id");
        $data['is_delete'] = 1;
//        $list=DB::table("alipay_app_oauth_users")->where("id",$id)->first();
//        dd($list);
        if (DB::table("alipay_app_oauth_users")->where("id", $id)->update($data)) {
            return back();
        }
    }

//还原页
    public function oauthRestore()
    {
        $auth = Auth::user()->can('oauthlist');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        } 
        $data = DB::table('users')->where('promoter_id', Auth::user()->id)->select('users.name', 'alipay_app_oauth_users.*')->orderBy('updated_at', 'desc')->join('alipay_app_oauth_users', 'alipay_app_oauth_users.promoter_id', '=', 'users.id')->where("alipay_app_oauth_users.is_delete", 1)->paginate(8);
        if (Auth::user()->hasRole('admin')) {
            $data = DB::table('users')->select('users.name', 'alipay_app_oauth_users.*')->orderBy('updated_at', 'desc')->join('alipay_app_oauth_users', 'alipay_app_oauth_users.promoter_id', '=', 'users.id')->where("alipay_app_oauth_users.is_delete", 1)->paginate(8);
        }
        // dd($data);
        return view("admin.alipayopen.store.restore", ["data" => $data]);
    }

//执行还原1
    public function restore(Request $request)
    {
        $s = $request->get("data");
        //dd($s);
        $data['is_delete'] = 0;
        foreach ($s as $v) {
            DB::table("alipay_app_oauth_users")->where("id", $v)->update($data);
        }
        return redirect("/admin/alipayopen/oauthRestore");
    }

    //执行还原2
    public function restoree(Request $request)
    {
        $id = $request->id;
        $data['is_delete'] = 0;
        if (DB::table("alipay_app_oauth_users")->where("id", $id)->update($data)) {
            return redirect("/admin/alipayopen/oauthRestore");
        }
    }

//执行搜索
    public function oauthSearch(Request $request)
    {
        $shopname = $request->input("shopname");
        $auth = Auth::user()->can('oauthlist');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        //
        $data = DB::table('users')->select('users.name', 'alipay_app_oauth_users.*')->where('pid', 0)->orderBy('updated_at', 'desc')->where('promoter_id', Auth::user()->id)->where("alipay_app_oauth_users.auth_shop_name", 'like', '%' . $shopname . '%')->where("alipay_app_oauth_users.is_delete", 0)->join('alipay_app_oauth_users', 'alipay_app_oauth_users.promoter_id', '=', 'users.id')->get();
        if (Auth::user()->hasRole('admin')) {
            $data = DB::table('users')->select('users.name', 'alipay_app_oauth_users.*')->where('pid', 0)->orderBy('updated_at', 'desc')->join('alipay_app_oauth_users', 'alipay_app_oauth_users.promoter_id', '=', 'users.id')->where("alipay_app_oauth_users.is_delete", 0)->where("alipay_app_oauth_users.auth_shop_name", 'like', '%' . $shopname . '%')->get();
        }
        if ($data->isEmpty()) {
            $paginator = "";
            $datapage = "";
        } else {
            $data = $data->toArray();
            //非数据库模型自定义分页
            $perPage = 9;//每页数量
            if ($request->has('page')) {
                $current_page = $request->input('page');
                $current_page = $current_page <= 0 ? 1 : $current_page;
            } else {
                $current_page = 1;
            }
            $item = array_slice($data, ($current_page - 1) * $perPage, $perPage); //注释1
            $total = count($data);
            $paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);
            $datapage = $paginator->toArray()['data'];
        }
        return view('admin.alipayopen.store.oauthlist', compact('datapage', 'paginator','shopname'));
    }

//zhi执行还原搜索
    public function restoreSearch(Request $request)
    {
        $shopname = $request->input("shopname");
        $data = DB::table('users')->select('users.name', 'alipay_app_oauth_users.*')->orderBy('updated_at', 'desc')->join('alipay_app_oauth_users', 'alipay_app_oauth_users.promoter_id', '=', 'users.id')->where("alipay_app_oauth_users.is_delete", 1)->where("alipay_app_oauth_users.auth_shop_name", 'like', '%' . $shopname . '%')->paginate(8);
        // dd($data);
        return view("admin.alipayopen.store.restore", ["data" => $data]);
    }

    //修改信息
    public function updateOauthUser(Request $request)
    {
        $id = $request->get('id');
        $store = AlipayAppOauthUsers::where('id', $id)->first()->toArray();
        return view('admin.alipayopen.config.updateOauthUser', compact('store'));

    }

    public function updateOauthUserPost(Request $request)
    {
        $data = $request->except(['_token', 'id']);
        try {
            AlipayAppOauthUsers::where('id', $request->get('id'))->update($data);
        } catch (\Exception $exception) {
            return json_encode([
                'status' => 0,
            ]);
        }
        return json_encode([
            'status' => 1,
        ]);
    }

    //执行口碑开店彻底删除
    public function deleteOauth(Request $request)
    {
        if (Auth::user()->hasRole('admin')) {
            $id = $request->get("id");
            try {
                if (DB::table("alipay_app_oauth_users")->where("id", $id)->delete()) {
                    return json_encode(['success' => 1]);
                }
            } catch (\Exception $exception) {
                return json_encode(['success' => 0]);
            }
        } else {
            return json_encode(['success' => 0]);
        }
    }
}
