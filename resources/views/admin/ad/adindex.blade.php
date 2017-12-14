@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>广告列表</h5>
                    </div>
                    @permission("insertAd")
                    <div class="col-sm-3">
                        <a href="{{route('addAd')}}"  class="btn btn-sm btn-success" style="color:white;">添加广告</a>
                    </div>
                    @endpermission
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>图片</th>
                                    <th>类型</th>
                                    <th>描述</th>
                                    <th>位置</th>
                                    <th>广告链接</th>
                                    <th>状态</th>
                                    <th>开始时间</th>
                                    <th>结束时间</th>
                                    <th>创建时间</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($list as $v)
                                    <tr>
                                        <td><span class="pie">{{$v->id}}</span></td>
                                        <td><span class="pie"><img src="{{url($v->pic)}}" style="width:100px; height:100px"></span></td>
                                        <td><span class="pie">{{$v->type}}</span></td>
                                        <td><span class="pie">{{$v->content}}</span></td>
                                        @if($v->position==1)
                                        <td><span class="pie">支付成功页</span></td>
                                        @endif
                                        @if($v->position==0)
                                            <td><span class="pie">支付失败页</span></td>
                                        @endif
                                        <td><span class="pie">{{$v->url}}</span></td>
                                        @if($v->status==1)
                                        <td><span class="pie">启用中</span></td>
                                        @endif
                                        @if($v->status==0)
                                            <td><span class="pie">下线中</span></td>
                                        @endif
                                        <td><span class="pie">{{$v->time_start}}</span></td>
                                        <td><span class="pie">{{$v->time_end}}</span></td>
                                        <td><span class="pie">{{$v->created_at}}</span></td>
                                        <td><span class="pie">{{$v->updated_at}}</span></td>
                                        <th>
                                            @permission("deleteAd")
                                            <button type="button" class="btn btn-info" type="del" onclick="del({{$v->id}})">删除</button>
                                            @endpermission
                                            @permission("editAd")
                                            <a href="{{url('/admin/ad/editAd?id='.$v->id)}}">
                                                <button type="button" class="btn">修改</button>
                                            </a>
                                            @endpermission
                                        </th>
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
                             {{$list->links()}}

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script type="text/javascript">
        function del(id) {
            //询问框
            // alert(id);
            layer.confirm('确定要删除吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.get("{{route('deleteAd')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            if(data.success){
                                window.location.href = "{{Route('adIndex')}}";
                            }else{
                                layer.msg("删除失败,请检查是否有权限")
                            }

                        }, "json");
            }, function () {

            });
        }
    </script>
@endsection
@section('js')

@endsection