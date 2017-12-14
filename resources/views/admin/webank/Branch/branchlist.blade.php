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
                    <form action="{{route("webankbranchlist",['pid'=>$pid])}}" method="post">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input placeholder="请输入商户简称" @if(isset($alias_name))value="{{$alias_name}}" @endif class="input-sm form-control" type="text" name="alias_name"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                            </div>
                        </div>
                        {{csrf_field()}}
                    </form>
                    <a href="{{route('webankbranchadd',['pid'=>$_GET['pid']])}}">
                        <button class="btn btn-success " type="button"><span class="bold">添加分店</span></button>
                    </a>
                    @permission("addOldBranch")
                    <a href="{{url('admin/alipayopen/addOldBranchIndex?pid='.$_GET['pid'].'&type=webank')}}" class="btn btn-sm btn-primary">绑定分店</a>
                    @endpermission
                    <a href="{{route("webankRestore")}}"> <button type="button" class="btn btn-outline btn-default">还原商户</button></a>
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
                                @if($lists)
                                    @foreach($lists as $v)
                                        <tr>
                                            <td>{{$v->store_id}}</td>
                                            <td><span class="pie">{{$v->alias_name}}</span></td>
                                            <td>{{$v->contact_name}}</td>
                                            <td><span class="pie">{{$v->contact_phone_no}}</span></td>
                                            <td><span class="pie">{{$v->name}}</span></td>
                                            <td>
                                                @permission("webankStoreInfo")
                                                <a href="{{route('webankeditcode',['store_id'=>$v->store_id])}}">
                                                    <button  type="button" class="btn btn-outline btn-success">收款信息</button>
                                                </a>
                                                @endpermission
                                                {{--<a href="{{url('admin/pingan/setMerchantRate?id='.$v->store_id)}}">--}}
                                                {{--<button type="button" class="btn btn-outline btn-info">费率调整</button>--}}
                                                {{--</a>--}}
                                                {{--<a href="{{url('admin/pingan/MerchantFile?id='.$v->store_id)}}">--}}
                                                {{--<button type="button" class="btn btn-outline btn-info">商户资料</button>--}}
                                                {{--</a>--}}
                                                {{--@permission("setStore")--}}
                                                {{--<a href="{{url('admin/pingan/SetStore?id='.$v->store_id)}}">--}}
                                                {{--<button type="button" class="btn btn-outline btn-primary">收款设置--}}
                                                {{--</button>--}}
                                                {{--</a>--}}
                                                {{--@endpermission--}}
                                                @if($v->pid==0)
                                                    @permission("webankBranch")
                                                    <a href="{{route('webankbranchlist',['pid'=>$v->id])}}">
                                                        <button type="button" class="btn btn-outline btn-primary">分店管理
                                                        </button>
                                                    </a>
                                                    @endpermission
                                                @endif
                                                @permission("webankCashier")
                                                <a href="{{route('webankcashierlist',['store_id'=>$v->store_id,'store_name'=>$v->alias_name])}}">
                                                    <button type="button" class="btn btn-outline btn-primary">收银员管理
                                                    </button>
                                                </a>
                                                @endpermission
                                                @permission("webankpayStatus")
                                                @if($v->pay_status==1)
                                                    <button id="cpay" onclick='co("{{$v->store_id}}",0)' type="button"
                                                            class="btn btn-outline btn-warning">关闭收款
                                                    </button>
                                                @endif
                                                @if($v->pay_status==0)
                                                    <button id="opay" onclick='co("{{$v->store_id}}",1)' type="button"
                                                            class="btn btn-outline btn-warning">开启收款
                                                    </button>
                                                @endif
                                                @endpermission
                                                @permission("Delwebank")
                                                <button onclick='del("{{$v->store_id}}")' type="button"
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
                                @if($lists)
                                    {{$lists->appends(['alias_name'=>$alias_name])->links()}}
                                @endif
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
                $.post("{{route('DelWebankstore')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            if(data.success){
                                window.location.href = "{{route('webankbranchlist',['pid'=>$pid])}}";
                            }else{
                                layer.msg(data.erro_message)
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
                    $.post("{{route('Webankpaystatus')}}", {_token: "{{csrf_token()}}", id: id, type: type},
                            function (data) {
                                window.location.href = "{{route('webankbranchlist',['pid'=>$pid])}}";
                            }, "json");
                }, function () {

                });
            } else {
                //询问框
                layer.confirm('确定要开启此商户的收款功能', {
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    $.post("{{route('Webankpaystatus')}}", {_token: "{{csrf_token()}}", id: id, type: type},
                            function (data) {
                                window.location.href = "{{route('webankbranchlist',['pid'=>$pid])}}";
                            }, "json");
                }, function () {

                });
            }

        }
    </script>

@endsection