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
                        <h5>新大陆刷卡通道商户列表</h5>
                    </div>
                    <form action="{{route("searchNewLand")}}" method="post">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input placeholder="请输入门店名称" class="input-sm form-control" type="text" name="shopname"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                            </div>
                        </div>
                        {{csrf_field()}}
                    </form>
                    <a href="{{route("NewLandRestore")}}"  class="btn btn-sm btn-danger" style="color:white;">还原</a>
                    <a href="{{route("addNewLand")}}"  class="btn btn-sm btn-success" style="color:white;">添加商户</a>
                    <a href="{{route('NewLandBills')}}"  class="btn btn-sm btn-default" style="color:white;">流水总表</a>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>店铺ID</th>
                                    <th>店铺名称</th>
                                    <th>商户号</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($datapage)
                                    @foreach($datapage as $v)
                                        <tr>
                                            <td>{{$v['store_id']}}</td>
                                            <td>{{$v['store_name']}}</td>
                                            <td>{{$v['merc_id']}}</td>
                                            <td>{{$v['created_at']}}</td>
                                            <td>
                                                <a href="{{url('/admin/newland/editNewLand?id='.$v['id'])}}"><button class="btn btn-info " type="button"><i class="fa fa-paste"></i>编辑</button></a>
                                                @if($v['pid']==0)
                                                    <a class="btn btn-success"
                                                       href="{{url('admin/newland/NewLandBranchIndex?pid='.$v['id'])}}">
                                                        <i class="fa"> </i> 分店管理
                                                    </a>
                                                @endif
                                                <a href="{{url('admin/alipayopen/CashierIndex?store_id='.$v['store_id'].'&store_name='.$v['store_name'])}}">
                                                    <button type="button" class="btn btn-success">收银员管理
                                                    </button>
                                                </a>
                                                <a href="{{url('admin/newland/NewLandBills?id='.$v['id'])}}"  class="btn btn-sm btn-default" style="color:white;">流水</a>
                                                <button class="btn btn-success" type="button" onclick="del({{$v['id']}})">删除</button>

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
                                    {{$paginator->appends($shopname)->render()}}
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
    <script type="text/javascript">
        function del(id) {
            //询问框
            // alert(id);
            layer.confirm('确定要删除吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.get("{{route('delNewLand')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            if(data.success){
                                window.location.reload();
                            }else{
                                layer.msg(data.message);
                            }

                        }, "json");
            }, function () {

            });
        }
    </script>
@endsection
@section('js')
@endsection