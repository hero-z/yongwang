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
                        <h5>收银员统一管理</h5>
                    </div>
                    <form action="{{route('mmdatalists')}}" method="get" >
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input placeholder="请输入收银员名称" class="input-sm form-control" name="merchant" type="text"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                        </div>
                    </div>
                    </form>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>收银员ID</th>
                                    <th>姓名</th>
                                    <th>手机号</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($data)
                                    @foreach($data as $v)
                                        <tr>
                                            <td>{{$v['id']}}</td>
                                            <td>{{$v['name']}}</td>
                                            <td>{{$v['phone']}}</td>
                                            <td>
                                                @permission("bindShops")
                                                <a href="{{url('/admin/alipayopen/merchantshoplist?id='.$v['id'])}}">
                                                    <button class="btn btn-info " type="button"><i class="fa fa-paste"></i>店铺绑定</button>
                                                </a>
                                                @endpermission
                                                @permission("cashier")
                                                <a href="{{url('/admin/alipayopen/editMerchantNames?id='.$v['id'])}}">
                                                    <button class="btn btn-info " type="button"><i class="fa  fa-pencil-square"></i>修改</button>
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
@endsection