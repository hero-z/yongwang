@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                @permission("addRole")
                <a href="{{url('/admin/alipayopen/role/create')}}">
                    <button type="button" class="btn btn-primary" id="showtoast">添加角色</button>
                </a>
                @endpermission
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>角色列表</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped" style="Word-break: break-all;">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>角色名称</th>
                                    <th>角色说明</th>
                                    <th>角色描述</th>
                                    <th>创建时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $v)
                                    <tr>
                                        <td>{{$v->id}}</td>
                                        <td><span class="pie">{{$v->name}}</span></td>
                                        <td><span class="pie">{{$v->display_name}}</span></td>
                                        <td>{{$v->description}}</td>
                                        <td><span class="pie">{{$v->created_at}}</span></td>
                                        <td>
                                            <a href="{{url('admin/alipayopen/assignment?role_id='.$v->id)}}">
                                                <button type="button" class="btn btn-outline btn-info">权限分配</button>
                                            </a>
                                            @if($v->name!="admin")
                                                @permission("deleteRole")
                                                <button type="button" onclick='del("{{$v->id}}")'
                                                        class="btn btn-outline btn-danger">删除
                                                </button>
                                                @endpermission
                                            @endif
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
                                {{ $data->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('js')
    <script>
        function del(id) {
            //询问框
            layer.confirm('你真的要删除这个重要的数据？', {
                btn: ['删除', '不删除'] //按钮
            }, function () {
                $.post("{{route('delRole')}}", {role_id: id, _token: "{{csrf_token()}}"}, function (result) {
                    if (result.status == 1) {
                        layer.msg('删除成功', {icon: 6});
                    } else {
                        layer.msg('删除失败,请检查权限', {icon: 5});
                    }
                    window.location.href = "{{url('/admin/alipayopen/role')}}";

                }, 'json');
            }, function () {

            });
        }
    </script>
@endsection