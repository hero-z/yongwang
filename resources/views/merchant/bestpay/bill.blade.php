@extends('layouts.amaze')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">

    <script src="{{asset('/amazeui/assets/js/locales/amazeui.datetimepicker.zh-CN.js')}}"></script>
    <script src="{{asset('/amazeui/assets/js/amazeui.datetimepicker.js')}}"></script>
    <script src="{{asset('/amazeui/assets/js/amazeui.datetimepicker.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/amazeui.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/amazeui.datetimepicker.css')}}"/>

    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/app.css')}}">
    {{--<link rel="stylesheet" href="{{asset('/amazeui/assets/css/amazeui_002.css')}}">--}}

@endsection
@section('title','账单流水信息')

@section('content')



    <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
    <div class="widget am-cf">
        <div class="widget-head am-cf">
            <div class="widget-title am-fl">翼支付流水信息</div>
            <div class="widget-function am-fr">
                <a href="javascript:;" class="am-icon-cog"></a>
            </div>
        </div>
        <div class="widget-head am-cf">
            <div class="widget-title am-fl">
                <form action="{{route('bestpay.merchantquery')}}" method="get">
                    <select data-am-selected name="paystatus">
                        <option value="0" >成功订单</option>
                        <option value=2 @if(isset($paystatus)&&$paystatus=='2') selected @endif >等待支付</option>
                        <option value=3 @if(isset($paystatus)&&$paystatus=='3') selected @endif >交易失败</option>
                        <option value=4 @if(isset($paystatus)&&$paystatus=='4') selected @endif >订单作废</option>
                        <option value=5 @if(isset($paystatus)&&$paystatus=='5') selected @endif >订单关闭</option>
                        <option value=9 @if(isset($paystatus)&&$paystatus=='9') selected @endif >全部订单</option>
                    </select>
                    <button type="submit" class="am-btn am-btn-secondary">筛选</button>
                </form>
            </div>
        </div>
        <div class="widget-body  widget-body-lg am-fr">

            <table width="100%" class="am-table am-table-compact am-table-striped tpl-table-black " id="example-r">
                <thead>
                <tr>
                    <th>店铺id</th>
                    <th>商户账号名称/ID</th>
                    <th>设备号</th>
                    <th>系统订单号</th>
                    <th>订单号</th>
                    <th>订单金额</th>
                    <th>实收金额</th>
                    <th>支付状态</th>
                    <th>退款金额</th>
                    <th>备注</th>
                    <th>更新时间</th>
                </tr>
                </thead>
                <tbody>
                @if($bills)
                    @foreach($bills as $v)
                        <tr class="gradeX">
                            <td >{{$v->store_id}}</td>
                            <td>@if(isset($merchants)&&$merchants&&array_key_exists($v->merchant_id,$merchants)){{$merchants[$v->merchant_id]}}@endif{{'/'.$v->merchant_id}}</td>
                            <td>{{$v->device_no}}</td>
                            <td >{{$v->out_trade_no}}</td>
                            <td >{{$v->trade_no}}</td>
                            <td >{{$v->total_amount}}</td>
                            <td >{{$v->receipt_amount}}</td>
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
                            <td>{{$v->refund_amount}}</td>
                            <td >{{$v->remark}}</td>
                            {{--<td class="am-u-sm-1">{{$v->user_name}}</td>--}}
                            <td >{{$v->updated_at}}</td>
                        </tr>
                    @endforeach
                @endif
                <!-- more data -->
                </tbody>
            </table>
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
        </div>
    </div>
</div>




@endsection