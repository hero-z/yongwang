@extends('union.parent')
@section('css')
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>银联钱包门店列表</h5>
                    </div>
                    <form action="" method="">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input placeholder="商户简称" class="input-sm form-control" type="text" name="store_name"
                                       value="<?php if (isset($condition['store_name'])) echo $condition['store_name']; ?>"> <span
                                        class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                            </div>
                        </div>
                        {{csrf_field()}}
                    </form>

                    <a class="J_menuItem" href="{{route('upstoreedit')}}"><button type="button" class="btn btn-outline btn-default">添加银联钱包商户</button></a>

{{-- 包含页头 
@include('union.daohang')
--}}

                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>店铺名</th>
                                    <th>系统商户号</th>
                                    <th>银联商户号</th>
                                     <th>APPID</th> 
                                     <th>APPKEY</th> 
                                     <th>店铺状态</th> 
                                    <th>联系人名称</th>
                                    <th>联系人手机号</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($data)
                                    @foreach($data as $v)
                                        <tr>
                                            <td>{{$v->id}}</td>
                                            <td>{{$v->store_name}}</td>
                                            <td>{{$v->store_id}}</td>

                                            <td>{{$v->merchant_id}}</td>
                                            <td><span class="pie">{{$v->app_id}}</span></td>
                                            <td><span class="pie">{{$v->app_key}}</span></td>
                                            <td>{{$v->status}}</td>
                                            <td><span class="pie">{{$v->shop_user}}</span></td>
                                            <td><span class="pie">{{$v->mobile}}</span></td>

                                            <td>
                                                <a href="{{url('upstore/edit?id='.$v->id)}}">
                                                    <button type="button" class="btn btn-outline btn-success">店铺信息
                                                    </button>
                                                </a>

                                                <a href="{{url('admin/alipayopen/CashierIndex?store_id='.$v->store_id.'&store_name='.$v->store_name)}}">
                                                    <button type="button" class="btn btn-outline btn-success">收银员管理
                                                    </button>
                                                </a>

                                                
<!--                                                 @permission("pufaStoreInfo")
                                                <a href="{{url('admin/pufa/storeEdit?store_id='.$v->store_id)}}">
                                                    <button type="button" class="btn btn-outline btn-success">店铺信息
                                                    </button>
                                                </a>
                                                @endpermission
                                                @permission("pufaBranch")
                                                <a href="{{url('admin/pufa/branchStore?pid='.$v->id)}}">
                                                    <button type="button" class="btn btn-outline btn-success">分店管理
                                                    </button>
                                                </a>
                                                @endpermission
                                                @permission("pufaCashier")
                                                <a href="{{url('admin/alipayopen/CashierIndex?store_id='.$v->store_id.'&store_name='.$v->store_name)}}">
                                                    <button type="button" class="btn btn-outline btn-success">收银员管理
                                                    </button>
                                                </a>
                                                @endpermission -->

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
                                {{$data->render()}}
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


    /*
        function del(id) {
            //询问框
            layer.confirm('确定要删除', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{route('storeDel')}}", {_token: "{{csrf_token()}}", id: id},
                    function (data) {
                        if (data.success) {
                            window.location.href = "{{route('PingAnStoreIndex')}}";
                        } else {
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

        }*/
    </script>

@endsection