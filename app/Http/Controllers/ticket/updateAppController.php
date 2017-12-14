<?php

namespace App\Http\Controllers\ticket;
use App\Http\Controllers\Controller;
use App\Models\AppUpdate;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class updateAppController extends Controller
{
    public function index(){
        $auth = Auth::user()->can('updateApp');
        if (!$auth) {
            echo '你没有权限操作！';
            die;
        }
        $list=AppUpdate::where('id',1)->first();
        $listpos=AppUpdate::where('id',2)->first();
        if(!$listpos){
            $ist=AppUpdate::create(['id'=>1,'version'=>'1','UpdateUrl'=>'']);
            $listpos=AppUpdate::where('id',2)->first();
        }
        return view("admin.ticket.updateAppIndex",compact("list",'listpos'));
    }
    public function updateApp(Request $request){
        $data=$request->except("_token");
        try{
         if(AppUpdate::where("id",$data['id'])->update($data)){
             return json_encode([
                 'status'=>1
             ]);
         }else{
             return json_encode([
                 'status'=>0
             ]);
         }
        }catch(\Exception $e){
            return json_encode([
                'status'=>0
            ]);
        }
    }
}
