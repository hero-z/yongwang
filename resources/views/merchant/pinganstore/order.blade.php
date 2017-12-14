@extends('layouts.public')
@section('content')
    <div class="row">
        <div class="col-sm-3">
            <div class="ibox">
                <div class="ibox-content">
                    <h5>总收入</h5>
                    <h1 class="no-margins">{{$sum}}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>收款详情</h5>
                </div>
                <div class="ibox-content">

                    <table class="table">
                        <thead>
                        <tr>
                            <th>金额</th>
                            <th>方式</th>
                            <th>状态</th>
                            <th>时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($order)
                            @foreach($order as $v)
                                <tr>
                                    <td>{{$v->total_amount}}</td>
                                    @if($v->type==304)
                                        <td class="am-u-sm-2">平安翼支付</td>
                                    @endif
                                    @if($v->type== 303)
                                        <td class="am-u-sm-2">平安京东</td>
                                    @endif
                                    @if($v->type==302)
                                        <td class="am-u-sm-2">平安微信</td>
                                    @endif
                                    @if($v->type==301)
                                        <td class="am-u-sm-2">平安支付宝</td>
                                    @endif
                                    @if($v->type==308)
                                        <td class="am-u-sm-2">平安翼支付(枪)</td>
                                    @endif
                                    @if($v->type== 307)
                                        <td class="am-u-sm-2">平安京东(枪)</td>
                                    @endif
                                    @if($v->type==306)
                                        <td class="am-u-sm-2">平安微信(枪)</td>
                                    @endif
                                    @if($v->type==305)
                                        <td class="am-u-sm-2">平安支付宝(枪)</td>
                                    @endif
                                    @if($v->pay_status==1)
                                        <td><button type="button" class="btn btn-outline btn-success">成功</button></td>
                                    @else
                                        <td><button type="button" class="btn btn-outline btn-danger">失败</button></td>
                                    @endif
                                    <td>{{$v->updated_at}}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="dataTables_paginate paging_simple_numbers"
                 id="DataTables_Table_0_paginate">
                {{ $order->links()}}
            </div>
        </div>
        @endif
    </div>
@endsection

@section('js')
@endsection