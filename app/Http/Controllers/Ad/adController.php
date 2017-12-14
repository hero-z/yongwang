<?php

namespace App\Http\Controllers\Ad;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Push\AopClient;
use App\Models\PushConfig;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\CountValidator\Exception;

class adController extends Controller{
    //广告列表页
    public function index(){
        $list=DB::table("ad")->paginate(4);

       return view("admin.ad.adindex",compact("list"));
    }
    //加载广告添加页
    public function addAd(){
        $auth = Auth::user()->can('insertAd');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        return view("admin.ad.addAd");
    }
    //执行广告添加
    public function insertAd(Request $request){
       $data['type']=$request->get("type");
        $data['position']=$request->get("position");
        $data['status']=$request->get("status");
        $data['time_start']=$request->get("time_start");
        $data['time_end']=$request->get("time_end");
        $data['content']=$request->get('content');
        $data['pic']=$request->get("pic");
        $data['user_id']=Auth::user()->id;
        $data['url']=$request->get("url");
        $data['created_at']=date("Y-m-d H:i:s");
        //查询数据库里同一时间是否已经有已上线广告

        if(preg_match("/^(http|ftp|https):/",$data['url'])){

            if($data['pic']==""){
                return json_encode([
                    'success'=>0,
                    'sub_msg'=>"广告图片不能为空,请选择图片"
                ]);
            }elseif($data['status']==1){

                $list=DB::table("ad")
                    ->where("status",1)
                    ->where("position",$data['position'])
                    ->where("type",$data['type'])
                    ->where("time_start","<",$data['time_end'])
                    ->where("time_end",">",$data['time_start'])
                    ->first();
                if($list){
                    return json_encode([
                        'success'=>0,
                        'sub_msg'=>"该时间段已有广告上线,请先下线或者选择其他时间"
                    ]);
                }elseif(DB::table("ad")->insert($data)){
                    return json_encode([
                        'success'=>1
                    ]);
                }


            }else{
                if(DB::table("ad")->insert($data)){
                    return json_encode([
                        'success'=>1
                    ]);
                }
            }
        }else{
            return json_encode([
                'success'=>0,
                'sub_msg'=>"请输入正确的网址格式"
            ]);
        }



    }
    //执行广告删除
    public function deleteAd(Request $request)
    {
        $auth = Auth::user()->can('deleteAd');
        if (!$auth) {
            return json_encode([
                "success" => 0
            ]);
        }else{
            $id = $request->get("id");

            $list=DB::table("ad")->where("id",$id)->first();
            if (DB::table("ad")->where("id", $id)->delete()) {
                try{

                    unlink(public_path().$list->pic);
                }catch(Exception $e){

                }
                return json_encode([
                    "success" => 1
                ]);
            } else {

                return json_encode([
                    "success" => 0
                ]);
            }
        }

    }
    //加载广告修改页
    public function editAd(Request $request){
        $auth = Auth::user()->can('editAd');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $id=$request->get('id');
        $list=DB::table("ad")->where("id",$id)->first();
        return view("admin.ad.editAd",compact("list"));
    }
    //执行广告修改
    public function updateAd(Request $request){
        $data['type']=$request->get("type");
        $data['position']=$request->get("position");
        $data['status']=$request->get("status");
        $data['time_start']=$request->get("time_start");
        $data['time_end']=$request->get("time_end");
        $data['content']=$request->get('content');
        $data['pic']=$request->get("pic");
        $data['url']=$request->get("url");
        $data['updated_at']=date("Y-m-d H:i:s");
        $id=$request->get("id");
        $oldpic=$request->get('oldpic');
        //查询数据库里同一时间是否已经有已上线广告
        if(preg_match("/^(http|ftp|https)/",$data['url'])) {

            if ($data['pic'] == "") {
                $data['pic'] = $oldpic;
                if ($data['status'] == 1) {
                    $list = DB::table("ad")
                        ->where("status", 1)
                        ->where("position", $data['position'])
                        ->where("type", $data['type'])
                        ->where("time_start", "<", $data['time_end'])
                        ->where("time_end", ">", $data['time_start'])
                        ->first();
                    if ($list) {
                        if ($list->id != $id) {
                            return json_encode([
                                'success' => 0,
                                'sub_msg' => "该时间段已有广告上线,请先下线或者选择其他时间"
                            ]);
                        } elseif (DB::table("ad")->where("id", $id)->update($data)) {
                            return json_encode([
                                'success' => 1
                            ]);
                        }

                    } elseif (DB::table("ad")->where("id", $id)->update($data)) {
                        return json_encode([
                            'success' => 1
                        ]);
                    }


                } else {
                    if (DB::table("ad")->where("id", $id)->update($data)) {
                        return json_encode([
                            'success' => 1
                        ]);
                    }
                }
            } else {
                if ($data['status'] == 1) {
                    $list = DB::table("ad")
                        ->where("status", 1)
                        ->where("position", $data['position'])
                        ->where("type", $data['type'])
                        ->where("time_start", "<", $data['time_end'])
                        ->where("time_end", ">", $data['time_start'])
                        ->first();
                    if ($list) {
                        if ($list->id != $id) {
                            return json_encode([
                                'success' => 0,
                                'sub_msg' => "该时间段已有广告上线,请先下线或者选择其他时间"
                            ]);
                        } elseif (DB::table("ad")->where("id", $id)->update($data)) {
                            unlink(public_path() . $oldpic);
                            return json_encode([
                                'success' => 1
                            ]);
                        }

                    } elseif (DB::table("ad")->where("id", $id)->update($data)) {
                        unlink(public_path() . $oldpic);
                        return json_encode([
                            'success' => 1
                        ]);
                    }


                } else {
                    if (DB::table("ad")->where("id", $id)->update($data)) {
                        unlink(public_path() . $oldpic);
                        return json_encode([
                            'success' => 1
                        ]);
                    }
                }
            }


        }else{
            return json_encode([
                'success'=>0,
                'sub_msg'=>"请输入正确的网址格式"
            ]);
        }

    }
}