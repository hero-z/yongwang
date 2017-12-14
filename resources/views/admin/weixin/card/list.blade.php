@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">
        <a href="{{route('WxCardsubMerchantAdd')}}">
            <button class="btn btn-success " type="button"><span class="bold">添加子商户</span></button>
        </a>
        <a href="{{route('WxCardOperate')}}">
            <button class="btn btn-warning " type="button"><span class="bold">操作</span></button>
        </a>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>支付宝微信二维码合一商户</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Appid</th>
                                    <th>名称</th>
                                    <th>授权函</th>
                                    <th>营业执照</th>
                                    <th>身份证</th>
                                    <th>状态</th>
                                    <th>一级类目</th>
                                    <th>二级类目</th>
                                    <th>授权截止日期</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($datapage)
                                    @foreach($datapage as $v)
                                        <tr>
                                            <td>{{$v->merchant_id}}</td>
                                            <td>{{$v->app_id}}</td>
                                            <td>{{$v->brand_name}}</td>
                                            <td>{{$v->protocol}}</td>
                                            <td>{{$v->license}}</td>
                                            <td>{{$v->idcard}}</td>
                                            @if($v->status=="CHECKING")
                                            <td>审核中</td>
                                            @endif
                                            @if($v->status=="APPROVED")
                                                <td>已通过</td>
                                            @endif
                                            @if($v->status=="REJECTED")
                                                <td>被驳回</td>
                                            @endif
                                            @if($v->status=="EXPIRED")
                                                <td>协议已过期</td>
                                            @endif
                                            <td>{{$v->primary_category_id}}</td>
                                            <td>{{$v->secondary_category_id}}</td>
                                            <td>{{$v->end_time}}</td>
                                            <td>{{$v->updated_at}}</td>
                                            <td>
                                                <button type="button" onclick='del("{{$v->id}}")'
                                                        class="btn btn-outline btn-danger">删除
                                                </button>
                                                <a href="{{url("/admin/alipayweixin/editAddTwo?id=".$v->id)}}">
                                                <button type="button" class="btn am-btn-secondary">修改</button>
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
                                    {{$datapage->links()}}
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
                $.post("{{route('WxCardsubMerchantDel')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            window.location.href = "{{route('WxCardManage')}}";
                        }, 'json');
            }, function () {

            });
        }
    </script>
@endsection