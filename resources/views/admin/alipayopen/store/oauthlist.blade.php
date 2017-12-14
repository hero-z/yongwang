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
                        <h5>商户第三方门店授权</h5>
                    </div>
                    <form action="{{route("oauthlist")}}" method="post">
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input placeholder="请输入店铺名称" class="input-sm form-control" type="text" name="shopname"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                        </div>
                    </div>
                        {{csrf_field()}}
                        </form>
                    <div class="col-sm-3">
                        @permission("oauthlistRestore")
                        <a href="{{route("oauthRestore")}}"  class="btn btn-sm btn-primary" style="color:white">还原</a>
                        @endpermission
                        </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>店铺id</th>
                                    <th>店铺名称</th>
                                    <th>联系电话</th>
                                    <th>授权时间</th>
                                    <th>更新时间</th>
                                    <th>归属员工</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($datapage)
                                    @foreach($datapage as $v)
                                        <tr>
                                            <td>{{$v->store_id}}</td>
                                            <td>{{$v->auth_shop_name}}</td>
                                            <td><span class="pie">{{$v->auth_phone}}</span></td>
                                            <td>{{$v->created_at}}</td>
                                            <td>{{$v->updated_at}}</td>
                                            <td>{{$v->name}}</td>
                                            <td>
                                                @permission("openShop")
                                                <a href="{{url('/admin/alipayopen/store/create?app_auth_token='.$v->app_auth_token.'&promoter_id='.$v->promoter_id)}}">
                                                    <button type="button" class="btn  btn-success">口碑开店</button>
                                                </a>
                                                 @endpermission
                                                <a href="{{url('/admin/alipayopen/onlyskm?store_id='.$v->store_id)}}">
                                                    <button type="button" class="btn  btn-success">当面付</button>
                                                </a>
                                                @if($v->pid==0)
                                                 @permission("oauthlistBranch")
                                                <a href="{{url('/admin/alipayopen/AlipayBranchIndex?pid='.$v->id)}}">
                                                    <button type="button" class="btn  btn-success">分店管理</button>
                                                </a>
                                                 @endpermission
                                                @endif
                                                @permission("oauthlistCashier")
                                                <a href="{{url('admin/alipayopen/CashierIndex?store_id='.$v->store_id.'&store_name='.$v->auth_shop_name)}}">
                                                    <button type="button" class="btn  btn-success">收银员管理</button>
                                                </a>
                                                @endpermission
                                                @permission("oauthlistEdit")
                                                <a href="{{url('/admin/alipayopen/updateOauthUser?id='.$v->id)}}">
                                                <button type="button" class="btn btn-outline btn-info">修改</button>
                                                 </a>
                                                @endpermission
                                                @permission("oauthlistChangeStatus")
                                                <a href="{{url('/admin/alipayopen/changeStatus?id='.$v->id)}}">
                                                    <button type="button" class="btn btn-outline btn-info">删除</button>
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
                                    {{$paginator->appends(['shopname'=>$shopname])->render()}}
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