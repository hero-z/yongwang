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
                        <h5>卡券列表</h5>
                    </div>

                    <div class="col-sm-3">
                        <a href="{{route("addAlipass")}}"  class="btn btn-sm btn-success" style="color:white;">添加卡券</a>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>店铺ID</th>
                                    <th>店铺名称</th>
                                    <th>卡券数量</th>
                                    <th>卡券剩余数量</th>
                                    <th>卡券类型</th>
                                    <th>卡券活动</th>
                                    <th>卡券活动开始时间</th>
                                    <th>卡券活动结束时间</th>
                                    <th>模板ID</th>
                                    <th>创建时间</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($list as $v)
                                    <tr>
                                        <td><span class="pie">{{$v->store_id}}</span></td>
                                        <td><span class="pie">{{$v->auth_shop_name}}</span></td>
                                        <td><span class="pie">{{$v->number}}</span></td>
                                        <td><span class="pie">{{$v->stock_number}}</span></td>
                                        @if($v->type=="free")
                                        <td><span class="pie">免费券</span></td>
                                        @endif
                                        @if($v->type=="fto")
                                            <td><span class="pie">满减券</span></td>
                                        @endif
                                        @if($v->type=="discount")
                                            <td><span class="pie">折扣券</span></td>
                                        @endif
                                        <td><span class="pie"></span></td>
                                        <td><span class="pie">{{$v->startDate}}</span></td>
                                        <td><span class="pie">{{$v->endDate}}</span></td>
                                        <td><span class="pie">{{$v->tpl_id}}</span></td>
                                        <td><span class="pie">{{$v->created_at}}</span></td>
                                        <td><span class="pie">{{$v->updated_at}}</span></td>
                                        <td>
                                            <button type="button" class="btn btn-info" type="del" onclick="del()">删除</button>
                                            <a href="{{url('/admin/ticket/editMerchine?id=')}}">
                                                <button type="button" class="btn">修改</button>
                                            </a>
                                            <a href="{{url('/admin/ticket/editMerchine?id=')}}">
                                                <button type="button" class="btn">模板详情</button>
                                            </a>
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
                $.get("{{route('deleteMerchine')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            if(data.success){
                                window.location.href = "{{Route('ticketIndex')}}";
                            }else{
                                layer.msg("删除失败")
                            }

                        }, "json");
            }, function () {

            });
        }
    </script>
@endsection
@section('js')

@endsection