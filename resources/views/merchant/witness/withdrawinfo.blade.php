@extends('layouts.amaze')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('title','平安见证宝管理')
@section('content')

    <!-- 内容区域 -->

    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-fl">提现记录查询</div>
                    <div class="widget-function am-fr">
                        <a href="javascript:;" class="am-icon-cog"></a>
                    </div>
                </div>
                <div class="widget-body  widget-body-lg am-fr">

                    <table width="100%" class="am-table am-table-compact am-table-striped tpl-table-black " id="example-r">
                        <thead>
                        <tr>
                            <th >平安银行子商户号</th>
                            <th >提现金额</th>
                            <th >流水号</th>
                            <th >提现时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="gradeX">
                            @if($withdrawInfo)
                                @foreach($withdrawInfo as $v)
                                <td >{{$v->sub_merchant_id}}</td>
                                <td >{{$v->tran_amount}}元</td>
                                <td>{{$v->withdraw_no}}</td>
                                <td>{{$v->created_at}}</td>
                                @endforeach
                            @endif
                        </tr>
                        <!-- more data -->
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="dataTables_paginate paging_simple_numbers"
                                 id="DataTables_Table_0_paginate">
                                {{$withdrawInfo->render()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




    </div>
@endsection
