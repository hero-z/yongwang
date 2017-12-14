@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        @permission("addAlipayWeixin")
        <a href="{{route('addAliPayWeixinStore')}}">
            <button class="btn btn-success " type="button"><span class="bold">添加合成二维码</span></button>
        </a>
        @endpermission
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>多码合一商户</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>店铺名称</th>
                                    <th>支付宝店铺ID</th>
                                    <th>微信店铺ID</th>
                                    <th>京东店铺ID</th>
                                    <th>翼支付店铺ID</th>
                                    <th>支付宝通道</th>
                                    <th>微信通道</th>
                                    <th>京东通道</th>
                                    <th>翼支付</th>
                                    <th>合成时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($datapage)
                                    @foreach($datapage as $v)
                                        <tr>
                                            <td>{{$v->id}}</td>
                                            <td>{{$v->store_name}}</td>
                                            <td>{{$v->store_id_a}}</td>
                                            <td>{{$v->store_id_w}}</td>
                                            <td>{{$v->store_id_j}}</td>
                                            <td>{{$v->store_id_b}}</td>
                                            <td>
                                            @if($v->alipay_ways=="oalipay")
                                            支付宝当面付
                                            @endif
                                            @if($v->alipay_ways=="salipay")
                                                支付宝口碑
                                            @endif
                                            @if($v->alipay_ways=="palipay")
                                                平安银行支付宝
                                            @endif
                                                @if($v->alipay_ways=="pfalipay")
                                               浦发银行支付宝
                                             @endif
                                            </td>
                                            <td>
                                            @if($v->weixin_ways=="weixin")
                                                微信官方
                                            @endif
                                            @if($v->weixin_ways=="pweixin")
                                                平安银行微信
                                                @endif
                                                @if($v->weixin_ways=="pfweixin")
                                                浦发银行微信
                                                @endif
                                            </td>
                                            <td>
                                                @if($v->jd_ways=="pjd")
                                                    平安京东
                                                @endif
                                            </td>
                                            <td>
                                                @if($v->bestpay_ways=="pbestpay")
                                                    平安翼支付
                                                @endif
                                            </td>
                                            <td>{{$v->created_at}}</td>
                                            <td>
                                                <a href="{{url('/admin/alipayweixin/qrCode?id='.$v->id)}}">
                                                    <button type="button" class="btn  btn-success">收款码</button>
                                                </a>
                                                @permission("alipayWeixinDelete")
                                                <button type="button" onclick='del("{{$v->id}}")'
                                                        class="btn btn-outline btn-danger">删除
                                                </button>
                                                @endpermission
                                                @permission("alipayweixinEdit")
                                                <a href="{{url("/admin/alipayweixin/editAddTwo?id=".$v->id)}}">
                                                <button type="button" class="btn am-btn-secondary">修改</button>
                                                 </a>
                                                @endpermission
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
                                    {{$paginator->render()}}
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
                $.post("{{route('delAlipayWexin')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            window.location.href = "{{route('AlipayWexinLists')}}";
                        }, 'json');
            }, function () {

            });
        }
    </script>
@endsection