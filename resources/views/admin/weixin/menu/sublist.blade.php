@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">
        <a href="{{route("WechatMenuAdd",['id'=>$id])}}">
            <button class="btn btn-success " type="button"><span class="bold">添加子菜单</span></button>
        </a>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>二级菜单</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>名称</th>
                                    <th>菜单类型</th>
                                    <th>链接</th>
                                    <th>状态</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($datapage)
                                    @foreach($datapage as $v)
                                        <tr>
                                            <td>{{$v->name}}</td>
                                            <td>{{$v->type}}</td>
                                            <td>{{$v->url}}</td>
                                            @if($v->status==1)
                                                <td>
                                                    <button type="button" class="btn  btn-success">启用中</button>
                                                </td>
                                            @endif
                                            @if($v->status==2)
                                                <td>
                                                    <button type="button" class="btn  btn-danger">已禁用</button>
                                                </td>
                                            @endif
                                            <td>{{$v->updated_at}}</td>
                                            <td>
                                                <button type="button" onclick='del("{{$v->id}}")'
                                                        class="btn btn-outline btn-danger">删除
                                                </button>
                                                <a href="{{route("WechatMenuEdit",['id'=>$v->id,'pid'=>$id])}}">
                                                <button type="button" class="btn btn-primary">修改</button>
                                                 </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers"
                                     id="DataTables_Table_0_paginate">
                                    {{$datapage->appends(['id'=>$id])->links()}}
                                </div>
                            </div>
                        </div>
                        @else
                            没有记录

                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('js')
    <script>
        function del(id) {
            layer.confirm('确定删除', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{route('WechatMenuDel')}}", {_token: "{{csrf_token()}}", id: id, type:2},
                        function (data) {
                            if (data.success==1) {
                                window.location.href = "{{route('WxAppMenuSubList',['id'=>$id])}}";
                            } else {
                                layer.msg(data.errmsg);
                            }
                        }, 'json');
            }, function () {

            });
        }
    </script>
@endsection