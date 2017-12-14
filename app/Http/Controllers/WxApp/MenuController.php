<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/3/10
 * Time: 18:57
 */

namespace App\Http\Controllers\WxApp;


use App\Models\WechatMenuConfig;
use App\Models\WechatMenuCustom;
use Illuminate\Support\Facades\DB;
use Mockery\CountValidator\Exception;
use Symfony\Component\HttpFoundation\Request;

class MenuController extends BaseController
{

    //菜单

    public function WxAppMenu()
    {

        $WxApp = $this->WxApp();
        $menu = $WxApp->menu;
        //添加菜单
        $result=[];
        $collection=WechatMenuCustom::where('pid',0)->where('status',1);
        if($collection->count()>0){
            $data=$collection->take(3)->get();
            foreach ($data as $v){
                $first=[
                    'type'=>'view',
                    'name'=>$v->name
                ];
                $subcollection=WechatMenuCustom::where('pid',$v->id)->where('status',1);
                if($subcollection->count()>0){
                    $subdata=$subcollection->take(5)->get();
                    $subresult=[];
                    foreach ($subdata as $vv){
                        $subresult[]=[
                            'type'=>'view',
                            'name'=>$vv->name,
                            'url'=>$vv->url,
                        ];
                    }
                    $first['sub_button']=$subresult;
                }else{
                    $first['url']=$v->url;
                }
                $result[]=$first;
            }
            $matchRule = [];
            $menu->add($result, $matchRule);
        }else{
            return json_encode(['success'=>0,'errmsg'=>'请重新检查菜单设置!']);
        }
        return json_encode(['success'=>1]);

        //查询菜单
        // $menus = $menu->all();


    }
    //列表页
    public function menulist(){
        $datapage=WechatMenuCustom::where('pid',0)->paginate(9);
        return view('admin.weixin.menu.list',compact('datapage'));
    }
    //子菜单列表
    public function menusublist(Request $request){
        $id=$request->id;
        $datapage=WechatMenuCustom::where('pid',$id)->paginate(9);
        return view('admin.weixin.menu.sublist',compact('datapage','id'));
    }
    //添加菜单
    public function menuadd(Request $request){

        return view('admin.weixin.menu.addmenu');
    }
    //post添加菜单
    public function menuaddpost(Request $request){

        $data=$request->only('name','url');
        $data['type']='view';
        $pid=$request->get('id',0);
        if($pid)
            $data['pid']=$pid;
        $count=WechatMenuCustom::where('pid',$pid)->where('status',1)->count();
        if(!$pid&&$count>=3){
            $data['status']=2;
        }elseif($pid&&$count>=5){
            $data['status']=2;
        }
        try{
            WechatMenuCustom::create($data);
        }catch(Exception $e){
            return json_encode([
                'errcode'=>1,
                'errmsg'=>$e->getMessage()
            ]);
        }
        return json_encode([
            'errcode'=>0,
            'id'=>$pid
        ]);
    }

    //修改菜单
    public function menuedit(Request $request){
        $id=$request->id;
        $pid=$request->pid or 0;
        $list=WechatMenuCustom::where('id',$id)->first();

        return view('admin.weixin.menu.editmenu',compact('list','pid'));
    }
    //post修改菜单
    public function menueditpost(Request $request){
        $id=$request->id;
        $data=$request->only('name','url','status');
        $pid=$request->get('pid') or 0;
        try{
            $count=WechatMenuCustom::where('pid',$pid)->where('id','<>',$id)->where('status',1)->count();
            if($pid==0&&$count>=3){
                $data['status']=2;
            }elseif($pid!=0&&$count>=5){
                $data['status']=2;
            }
            $res=WechatMenuCustom::where('id',$id)->update($data);
        }catch(Exception $e){
            return json_encode([
                'success'=>0,
                'errmsg'=>$e->getMessage()
            ]);
        }
        if($res)
            return json_encode(['success'=>$res,'pid'=>$pid]);
        else
            return json_encode(['success'=>$res,'errmsg'=>'保存失败']);
    }
    //删除菜单
    public function menudel(Request $request){
        $id=$request->id;
        try{
            $count=WechatMenuCustom::where('pid',$id)->count();
            if($count>0){
                return json_encode([
                    'success'=>0,
                    'errmsg'=>'子菜单列表不为空!请先删除子菜单!'
                ]);
            }
            $res=WechatMenuCustom::where('id',$id)->delete();
        }catch(Exception $e){
            return json_encode([
                'success'=>0,
                'errmsg'=>$e->getMessage()
            ]);
        }
        if($res)
            return json_encode(['success'=>$res]);
        else
            return json_encode(['success'=>$res,'errmsg'=>'删除失败']);
    }
    public function menuset(){
        $config=WechatMenuConfig::where('id',1)->first();
        if(!$config){
            $insert=[
                'id'=>1,
                'app_id'=>'',
                'secret'=>''
            ];
            $ist=DB::table('wechat_menu_configs')->insert($insert);
            if($ist){
                $config=WechatMenuConfig::where('id',1)->first();
            }else
                die('初始化微信菜单配置失败!');
        }
        return view('admin.weixin.menu.setlist',compact('config'));
    }
    public function menudoset(Request $request){
        $data=$request->except('_token');
        $update['app_id']=trim($data['app_id']);
        $update['secret']=trim($data['secret']);
        $update['token']=trim($data['token']);
        $res=WechatMenuConfig::where('id',1)->update($update);
        return json_encode($res);
    }
}