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
                        <h5>微信支付商户列表</h5>
                    </div>
                    <form action="{{route("searchWx")}}" method="post">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input placeholder="请输入门店名称" class="input-sm form-control" type="text" name="shopname"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                            </div>
                        </div>
                        {{csrf_field()}}
                    </form>
                    @permission("wxShopRestore")
                    <a href="{{route("wxRestore")}}"  class="btn btn-sm btn-danger" style="color:white;">还原</a>
                    @endpermission
                    @permission("wxAddShop")
                    <a href="{{route("WxAddShop")}}"  class="btn btn-sm btn-success" style="color:white;">添加商户</a>
                    @endpermission
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
                                            <td>{{$v['mch_id']}}</td>
                                            <td>{{$v['created_at']}}</td>
                                            <td>
                                                @permission("wxEditShop")
                                               <a href="{{url('/admin/weixin/WxEditShop?id='.$v['id'])}}"><button class="btn btn-info " type="button"><i class="fa fa-paste"></i>编辑</button></a>
                                                @endpermission
                                                <a class="btn btn-success" href="{{url('admin/weixin/WxPayQr?store_id='.$v['store_id'])}}">
                                                    <i class="fa fa-weixin"> </i> 收款码
                                                </a>
                                                @if($v['pid']==0)
                                                   @permission("wxBranch")
                                                    <a class="btn btn-success"
                                                       href="{{url('admin/weixin/BranchIndex?pid='.$v['id'])}}">
                                                        <i class="fa"> </i> 分店管理
                                                    </a>
                                                   @endpermission
                                                @endif
                                                <a href="{{url('admin/alipayopen/CashierIndex?store_id='.$v['store_id'].'&store_name='.$v['store_name'])}}">
                                                    <button type="button" class="btn btn-success">收银员管理
                                                    </button>
                                                </a>
                                                @permission("wxChangeStatus")
                                                <a href="{{url("/admin/weixin/wxChangeStatus?id=".$v['id'])}}"><button class="btn btn-success" type="button">删除</button></a>
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
@endsection