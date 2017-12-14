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
                        <h5>{{$store_name}}分店列表<small>添加新分店是在该店铺下新添加分店;绑定分店是将原有无分店的总店添加为该店铺的分店,单独收款</small></h5>
                    </div>
                    <a href="{{url('/admin/pingan/BranchAdd?pid='.$_GET['pid'])}}">
                        <button class="btn btn-success " type="button"><span class="bold">添加分店</span></button>
                    </a>
                    @permission("addOldBranch")
                    <a href="{{url('admin/alipayopen/addOldBranchIndex?pid='.$_GET['pid'].'&type=pingan')}}" class="btn btn-sm btn-primary">绑定分店</a>
                    @endpermission
                    <a href="{{route("pinganRestore")}}"> <button type="button" class="btn btn-outline btn-default">还原商户</button></a>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>商户id</th>
                                    <th>商户简称</th>
                                    <th>联系人名称</th>
                                    <th>联系人手机号</th>
                                    <th>归属员工</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($datapage)
                                    @foreach($datapage as $v)
                                        <tr>


                                            <td>{{$v->external_id}}</td>
                                            <td><span class="pie">{{$v->alias_name}}</span></td>
                                            <td>{{$v->contact_name}}</td>
                                            <td><span class="pie">{{$v->contact_mobile}}</span></td>
                                            <td><span class="pie">{{$v->user_name}}</span></td>
                                            <td>
                                                <a href="{{url('admin/pingan/editPingan?id='.$v->id)}}">
                                                    <button  type="button" class="btn btn-outline btn-success">店铺信息</button>
                                                </a>
                                                <a href="{{url('admin/pingan/MerchantFile?id='.$v->external_id)}}">
                                                    <button type="button" class="btn btn-outline btn-info">商户资料</button>
                                                </a>
                                                @permission("PinganRate")
                                                <a href="{{url('admin/pingan/setMerchantRate?id='.$v->id)}}">
                                                    <button type="button" class="btn btn-outline btn-info">费率调整</button>
                                                </a>
                                                @endpermission
                                                <a href="{{url('admin/pingan/SetStore?id='.$v->id)}}">
                                                    <button type="button" class="btn btn-outline btn-primary">收款设置
                                                    </button>
                                                </a>
                                                <a href="{{url('admin/alipayopen/CashierIndex?store_id='.$v->external_id.'&store_name='.$v->alias_name)}}">
                                                    <button type="button" class="btn btn-outline btn-primary">收银员管理
                                                    </button>
                                                </a>
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
                                                <button onclick='del("{{$v->id}}")' type="button"
                                                        class="btn btn-outline btn-warning">删除
                                                </button>
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
                                {{$datapage->appends(['pid'=>$pid])->links()}}
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
                $.post("{{route('DelPinanStore')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            if(data.success){
                                window.location.reload();
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
                                window.location.reload();
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
                                window.location.reload();
                            }, "json");
                }, function () {

                });
            }

        }
    </script>

@endsection