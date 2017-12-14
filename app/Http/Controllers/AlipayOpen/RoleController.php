<?php
/**
 * Created by PhpStorm.
 * User: dmk
 * Date: 2017/1/3
 * Time: 16:33
 */

namespace App\Http\Controllers\AlipayOpen;

use App\Models\Role;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $auth = Auth::user()->can('role');
        if (!$auth) {
            echo '你没有权限操作！';die;
        }
        //
        $data = Role::orderBy('created_at', 'desc')->paginate(9);
        return view('admin.alipayopen.role.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $auth = Auth::user()->can('addRole');
        if (!$auth) {
            echo '你没有权限操作！';die;
        }
        //
        return view('admin.alipayopen.role.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $auth = Auth::user()->can('role');
        if (!$auth) {
            echo '你没有权限操作！';die;
        }
        //验证
        $messages = [
            'required' => '此字段必须填写',
        ];
        $this->validate($request, [
            'name' => 'required|max:255',
        ], $messages);
        //验证通过插入数据库
        $data = [
            'name' => $request->get('name'),
            'display_name' => $request->get('display_name'),
            'description' => $request->get('description'),
            'created_at' => date('Y-m-d H:i:s', time()),
        ];
        Role::create($data);
        return redirect('/admin/alipayopen/role');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }



}