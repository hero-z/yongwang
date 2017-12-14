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
                        <h5>{{$store_name}}收银员列表</h5>
                    </div>
                    <div class="col-sm-3">
                        <a href="{{url('/admin/alipayopen/CashierAdd?store_id='.$_GET['store_id'].'&store_name='.$_GET['store_name'])}}"
                           class="btn btn-sm btn-primary">添加收银员</a>
                        <a href="{{url('/admin/alipayopen/bindCashierIndex?store_id='.$_GET['store_id'].'&store_name='.$_GET['store_name'])}}"
                           class="btn btn-sm btn-primary">绑定收银员</a>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>收银员名称</th>
                                    <th>手机号码</th>
                                    <th>创建时间</th>
                                    <th>类型</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($datapage)
                                    @foreach($datapage as $v)
                                        <tr>
                                            <td>{{$v->id}}</td>
                                            <td>{{$v->name}}</td>
                                            <td>{{$v->phone}}</td>
                                            <td><span class="pie">{{$v->created_at}}</span></td>
                                            @if($v->pid==0&&$v->type==0)
                                                <td>管理员</td>
                                            @endif
                                            @if($v->pid!=0&&$v->type==0)
                                                <td>店长</td>
                                            @endif
                                            @if($v->pid!=0&&$v->type!=0)
                                                <td>收银员</td>
                                            @endif
                                            <td>
                                                @if($v->store_type=="oalipay")
                                                    <a href="{{url('/admin/alipayopen/onlyskm?store_id='.$v->store_id.'&merchant_id='.$v->id)}}">
                                                        <button type="button" class="btn  btn-success">收款码</button>
                                                    </a>
                                                @endif
                                                @if($v->store_type=="salipay")
                                                    <a href="{{url('admin/alipayopen/skm?id='.$id.'&merchant_id='.$v->id)}}">
                                                        <button type="button" class="btn  btn-success">收款码</button>
                                                    </a>
                                                @endif
                                                @if($v->store_type=="pingan")
                                                    <a href="{{url('/admin/alipayopen/pinganCashierQr?store_id='.$v->store_id.'&merchant_id='.$v->id)}}">
                                                        <button type="button" class="btn  btn-success">收款码</button>
                                                    </a>
                                                @endif
                                                @if($v->store_type=="weixin")
                                                    <a href="{{url('/admin/weixin/WxPayQr?store_id='.$v->store_id.'&merchant_id='.$v->id)}}">
                                                        <button type="button" class="btn  btn-success">收款码</button>
                                                    </a>
                                                @endif
                                                @if($v->store_type=="pufa")
                                                    <a href="{{url('/admin/pufa/cashierQr?store_id='.$v->store_id.'&cashier_id='.$v->id)}}">
                                                        <button type="button" class="btn  btn-success">收款码</button>
                                                    </a>
                                                @endif
                                                @if($v->store_type=="webank")
                                                    <a href="{{route('webankcashierqr',['store_id'=>$v->store_id,'cashier_id'=>$v->id])}}">
                                                        <button type="button" class="btn  btn-success">收款码</button>
                                                    </a>
                                                @endif
                                                <a href="">
                                                    <button type="button" class="btn btn-outline btn-info">修改</button>
                                                </a>
                                                <a href="">
                                                    <button type="button" class="btn btn-outline btn-info">删除</button>
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
                                    {{$paginator->appends(['store_id'=>$store_id,'store_name'=>$store_name])->render()}}
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