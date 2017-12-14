@extends('layouts.public')
@section('css')
    <link rel="stylesheet" href="{{asset('css/plugins/jsTree/style.min.css')}}">
@endsection
@section('content')
    <script type="text/javascript" src="{{asset('js/plugins/jsTree/jstree.min.js')}}"></script>
    <div class="col-sm-12">
        @permission("addUser")
        <a  href="{{url('/register')}}">
            <button type="button" class="btn btn-w-m btn-success">添加@if(Auth::user()->level==1)代理商@else业务员@endif</button>
        </a>
        @endpermission
        @permission("changeShopOwner")
        <a href="{{route("changeShopOwner")}}">
            <button type="button" class="btn btn-primary  btn-w-m">员工店铺转移</button>
        </a>
        @endpermission
        @permission('role')
        <a class="J_menuItem" href="{{url('admin/alipayopen/role')}}"><button type="button" class="btn btn-w-m btn-success">角色管理</button></a>
        @endpermission
        @permission('permission')
        {{--<a class="J_menuItem" href="{{url('admin/alipayopen/permission')}}"><button type="button" class="btn btn-primary  btn-w-m">权限管理</button></a>--}}
        @endpermission
        @permission('setrate')
        <a class="J_menuItem" href="{{url('admin/alipayopen/setrate')}}"><button type="button" class="btn btn-primary  btn-w-m">设置费率</button></a>
        @endpermission
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>员工列表</h5>
            </div>
            <div class="ibox-content">

                <table class="table" style="Word-break: break-all;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>用户</th>
                        <th>电话</th>
                        <th>邮箱</th>
                        <th>添加时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($user as $v)
                        <tr>
                            <td>{{$v['id']}}</td>
                            <td>{{$v['name']}}</td>
                            <td>{{$v['phone']}}</td>
                            <td>{{$v['email']}}</td>
                            <td>{{$v['created_at']}}</td>
                            <td>
                                <button type="button" onclick="userback('{{$v['id']}}')"
                                        class="btn btn-primary btn-rounded">恢复
                                </button>
                                @permission('dropUser')
                                    <button type="button" onclick="deleteu('{{$v['id']}}')"
                                            class="btn btn-danger btn-rounded">彻底删除
                                    </button>
                                @endpermission
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="dataTables_paginate paging_simple_numbers"
                     id="DataTables_Table_0_paginate">
                    @if($user)
                    {{$user->links()}}
                    @endif
                </div>
            </div>
        </div>
        @endsection
        @section('js')
            <script>
                function updateu(id) {
                    window.location.href = "/admin/alipayopen/updateu?id=" + id;
                }
                function userback(id) {
                    $.post("{{route('userback')}}", {id: id, _token: "{{csrf_token()}}"}, function (result) {
                        if (result.status==1) {
                            window.location.href = "{{route('deluserlist')}}";
                        }else{
                            layer.msg(result.msg);
                        }
                    },"json");
                }
                function deleteu(id) {
                    layer.confirm('确定彻底删除吗?无法恢复', {
                        btn: ['确定', '取消'] //按钮
                    }, function () {
                        $.post("{{route('dropuser')}}", {id: id, _token: "{{csrf_token()}}"}, function (result) {
                            if (result.status==1) {
                                window.location.href = "{{route('deluserlist')}}";
                            }else{
                                layer.msg(result.msg);
                            }
                        },"json");
                    }, function () {

                    });
                }
            </script>
@endsection