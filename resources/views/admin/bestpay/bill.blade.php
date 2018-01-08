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
                        <h5>翼支付交易流水</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>店铺id</th>
                                    <th>商户账号名称/ID</th>
                                    <th>代理商/ID</th>
                                    <th>设备号</th>
                                    <th>系统订单号</th>
                                    <th>订单号</th>
                                    <th>订单金额</th>
                                    <th>实收金额</th>
                                    <th>支付状态</th>
                                    <th>是否退款</th>
                                    <th>退款金额</th>
                                    <th>更新时间</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($bills)
                                    @foreach($bills as $v)
                                        <tr>
                                            <td>{{$v->store_id}}</td>
                                            <td>@if(isset($merchants)&&$merchants&&array_key_exists($v->merchant_id,$merchants)){{$merchants[$v->merchant_id]}}@endif{{'/'.$v->merchant_id}}</td>
                                            <td>@if(isset($admins)&&$admins&&array_key_exists($v->admin_id,$admins)){{$admins[$v->admin_id]}}@endif{{'/'.$v->admin_id}}</td>
                                            <td>{{$v->device_no}}</td>
                                            <td>{{$v->out_trade_no}}</td>
                                            <td>{{$v->trade_no}}</td>
                                            <td>{{$v->total_amount}}</td>
                                            <td>{{$v->receipt_amount}}</td>
                                            <td>
                                                @if($v->pay_status==1)
                                                    <span style="color:green;">
                                                        支付成功
                                                    </span>
                                                @elseif($v->pay_status==2)
                                                    等待支付
                                                @elseif($v->pay_status==3)
                                                    <span style="color:red;">
                                                        交易失败
                                                    </span>
                                                @elseif($v->pay_status==4)
                                                    <span style="color:red;">
                                                        订单作废
                                                    </span>
                                                @elseif($v->pay_status==5)
                                                    <span style="color:red;">
                                                        关闭交易
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($v->refund_flag==0)
                                                    否
                                                @else
                                                    是
                                                @endif
                                            </td>
                                            <td>{{$v->refund_amount}}</td>
                                            <td>{{$v->updated_at}}</td>
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
                                @if($bills)
                                    {{$bills->render()}}
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
@endsection