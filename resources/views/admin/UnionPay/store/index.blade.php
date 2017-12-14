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
                        <h5>门店列表</h5>
                    </div>
                    <form action="" method="post">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input placeholder="请输入商户简称" class="input-sm form-control" type="text" name="shopname"> <span
                                        class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                            </div>
                        </div>
                        {{csrf_field()}}
                    </form>
                    @permission("addUnionpayStore")
                    <a href="">
                        <button class="btn btn-success " type="button"><span class="bold">添加商户</span></button>
                    </a>
                    @endpermission
                    @permission("unionpayRestore")
                    <a href="{{route("unionRestoreIndex")}}">
                        <button type="button" class="btn btn-outline btn-default">还原商户</button>
                    </a>
                    @endpermission
                    <a class="J_menuItem" href="{{route('unionPayBill')}}">
                        <button type="button" class="btn btn-outline btn-default">商户流水</button>
                    </a>
                    @permission('unionpaySet')
                    <a class="J_menuItem" href="{{route('UnionPaySet')}}">
                        <button type="button" class="btn btn-outline btn-default">银行通道配置</button>
                    </a>
                    @endpermission

                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>门店id</th>
                                    <th>商户简称</th>
                                    <th>联系人名称</th>
                                    <th>联系人手机号</th>
                                    <th>归属员工</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($store)
                                    @foreach($store as $v)
                                        <tr>
                                            <td>{{$v->store_id}}</td>
                                            <td><span class="pie">{{$v->alias_name}}</span></td>
                                            <td>{{$v->manager}}</td>
                                            <td><span class="pie">{{$v->manager_phone}}</span></td>
                                            <td><span class="pie">{{$v->name}}</span></td>
                                            <td>
                                                @permission("unionpayStoreInfo")
                                                <a href="{{url('/admin/UnionPay/unionpayInfo?id='.$v->id)}}">
                                                    <button type="button" class="btn btn-outline btn-info">商户资料</button>
                                                </a>
                                                @endpermission
                                                @permission("setUnionpayCard")
                                                <a href="{{url('/admin/UnionPay/setUnionPayCard?store_id='.$v->store_id)}}">
                                                    <button type="button" class="btn btn-outline btn-primary">收款设置
                                                    </button>
                                                </a>
                                                @endpermission
                                                @permission("UnionPayBranch")
                                                    <a href="{{url('/admin/UnionPay/BranchIndex?pid='.$v->id)}}">
                                                        <button type="button" class="btn btn-outline btn-primary">分店管理
                                                        </button>
                                                    </a>
                                                @endpermission
                                                <a href="{{url('admin/alipayopen/CashierIndex?store_id='.$v->store_id.'&store_name='.$v->alias_name)}}">
                                                    <button type="button" class="btn btn-outline btn-primary">收银员管理
                                                    </button>
                                                </a>
                                                @permission("unionpayOpen")
                                                @if($v->pay_status==1)
                                                    <button id="cpay" onclick='co("{{$v->id}}",0)' type="button"
                                                            class="btn btn-outline btn-warning">关闭收款
                                                    </button>
                                                @endif

                                                @if($v->pay_status==0)
                                                    <button id="opay" onclick='co("{{$v->id}}",1)' type="button"
                                                            class="btn btn-outline btn-warning">开启收款
                                                    </button>
                                                @endif
                                                @endpermission
                                                @permission("changeUnionpay")
                                                <button onclick='del("{{$v->id}}")' type="button"
                                                        class="btn btn-outline btn-warning">删除
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
                                {{$store->links()}}
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
                $.post("{{route('unionpayChangeStatus')}}", {_token: "{{csrf_token()}}", id: id},
                    function (data) {
                        if (data.success) {
                            window.location.href = "{{route('UnionPayStoreIndex')}}";
                        } else {
                            layer.msg("删除失败")
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
                    $.post("{{route('unionPayStatus')}}", {_token: "{{csrf_token()}}", id: id, type: type},
                        function (data) {
                            window.location.href = "{{route('UnionPayStoreIndex')}}";
                        }, "json");
                }, function () {

                });
            } else {
                //询问框
                layer.confirm('确定要开启此商户的收款功能', {
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    $.post("{{route('unionPayStatus')}}", {_token: "{{csrf_token()}}", id: id, type: type},
                        function (data) {
                            window.location.href = "{{route('UnionPayStoreIndex')}}";
                        }, "json");
                }, function () {

                });
            }

        }
    </script>

@endsection