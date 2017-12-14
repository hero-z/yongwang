@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <!--
        <form action="" method="">
            <div class="col-sm-3">
                <div class="input-group">
                    <input placeholder="请输入商户简称" class="input-sm form-control" type="text" name="store_name" value="<?php if(isset($where['store_name'])) echo $where['store_name']; ?>"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                </div>
            </div>
            {{csrf_field()}}
        </form>
        -->

        <a href="{{url('/admin/pufa/BranchAdd?pid='.$_GET['pid'])}}">
            <button class="btn btn-success " type="button"><span class="bold">添加分店</span></button>
        </a>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>门店列表</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>商户id</th>
                                    <th>商户全称</th>
                                    <th>商户简称</th>
                                    <th>联系人名称</th>
                                    <th>联系人手机号</th>
                                    <th>费率</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($datapage)
                                    @foreach($datapage as $v)
                                        <tr>
                                            <td>{{$v->store_id}}</td>
                                            <td><span class="pie">{{$v->store_name}}</span></td>
                                            <td><span class="pie">{{$v->merchant_short_name}}</span></td>
                                            <td>{{$v->shop_user}}</td>
                                            <td><span class="pie">{{$v->tel}}</span></td>
                                            <td><span class="pie">{{$v->rate}}</span></td>
                                            <td>
                                                <a href="{{url('admin/pufa/storeEdit?store_id='.$v->store_id)}}">
                                                    <button  type="button" class="btn btn-outline btn-success">店铺信息</button>
                                                </a>

                                                <a href="{{url('admin/alipayopen/CashierIndex?store_id='.$v->store_id.'&store_name='.$v->store_name)}}">
                                                    <button  type="button" class="btn btn-outline btn-success">收银员管理</button>
                                                </a>
<!--                                                 <button onclick='del("{{$v->id}}")' type="button"
                                                        class="btn btn-outline btn-warning">删除
                                                </button> -->
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
                                {{$paginator->render()}}
                            </div>
                        </div>
                    </div>
                    @else
                        没有任何记录
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
@section('js')
    <script>
        function del(id) {
            //询问框
            layer.confirm('确定要删除', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{route('storeDel')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            if(data.success){
                                window.location.href = "{{route('PingAnStoreIndex')}}";
                            }else{
                                layer.msg("请先解除店铺绑定!")
                            }
                        }, "json");
            }, function () {

            });
        }


        function co(id, type) {
            if (type == 0) {
                //询问框
                layer.confirm('确定要关闭此商户的收款功能', {
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    $.post("{{route('PayStatus')}}", {_token: "{{csrf_token()}}", id: id, type: type},
                            function (data) {
                                window.location.href = "{{route('PingAnStoreIndex')}}";
                            }, "json");
                }, function () {

                });
            } else {
                //询问框
                layer.confirm('确定要开启此商户的收款功能', {
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    $.post("{{route('PayStatus')}}", {_token: "{{csrf_token()}}", id: id, type: type},
                            function (data) {
                                window.location.href = "{{route('PingAnStoreIndex')}}";
                            }, "json");
                }, function () {

                });
            }

        }
    </script>

@endsection