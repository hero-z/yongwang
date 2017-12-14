<?php

namespace App\Http\Controllers\ticket;

use App\Models\JpushConfig;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class jpushConfigsController extends Controller
{


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function setJpushConfigs()
    {
        $auth = Auth::user()->can('jpushConfigs');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $list=JpushConfig::first();
       return view("admin.ticket.setjpushConfigs",compact("list"));
    }
    public function updateJpushConfigs(Request $request){
        $id=$request->get("id");
        $data=$request->except("_token");
        try{
            if(JpushConfig::where("id",$id)->update($data) ){
                return json_encode([
                    "status"=>1
                ]);
            }else{
                return json_encode([
                    "status"=>0
                ]);
            }

        }catch(\Exception $e){
            return json_encode([
                "status"=>0
            ]);
        }
    }

}
