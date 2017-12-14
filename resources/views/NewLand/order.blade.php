@extends('layouts.public')
@section('content')
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>交易流水
            </h5>
        </div>
        <div class="ibox-content">
            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper form-inline" role="grid">
                <table class="table table-striped table-bordered table-hover dataTables-example dataTable"
                       id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
                    <thead>
                    <tr role="row">
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 189px;" aria-label="渲染引擎：激活排序列升序" aria-sort="ascending">订单号
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 308px;" aria-label="平台：激活排序列升序">店铺ID
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 308px;" aria-label="平台：激活排序列升序">店铺名称
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 308px;" aria-label="平台：激活排序列升序">收银员
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 142px;" aria-label="引擎版本：激活排序列升序">创建时间
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 144px;" aria-label="CSS等级：激活排序列升序">更新时间
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 144px;" aria-label="CSS等级：激活排序列升序">总金额
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 144px;" aria-label="CSS等级：激活排序列升序">订单来源
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 144px;" aria-label="CSS等级：激活排序列升序">交易状态
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 144px;" aria-label="CSS等级：激活排序列升序">备注
                        </th>
                    </tr>
                    </thead>
                    <tbody id="appends">
                    @foreach($datapage as $v)
                        <tr class='gradeA odd'>
                            <td class=''>{{$v->trade_no}}</td>
                            <td class=''>{{$v->store_id}}</td>
                            <td class=''>{{$v->store_name}}</td>
                            <td class=''>
                                @if($v->merchant_id&&isset($cashier[$v->merchant_id]))
                                    {{$cashier[$v->merchant_id]}}
                                @endif
                            </td>
                            <td class=''>{{$v->created_at}}</td>
                            <td class=''>{{$v->updated_at}}</td>
                            <td class=''>{{$v->total_amount}}</td>
                            @if($v->type=="1001")
                                <td class=''>新大陆刷卡</td>
                            @endif

                            @if($v->pay_status=="1")
                                <td style="color: green">
                                    <button type="button" class="btn btn-outline btn-success">付款成功</button>
                                </td>
                            @endif
                            @if($v->pay_status=="2")
                                <td class=''>
                                    <button type="button" class="btn btn-outline btn-danger">取消支付</button>
                                </td>
                            @endif
                            @if($v->pay_status=="3")
                                <td class=''>
                                    <button type="button" class="btn btn-outline btn-danger">等待支付</button>
                                </td>
                            @endif
                            @if($v->pay_status=="4")
                                <td class=''>
                                    <button type="button" class="btn btn-outline btn-danger">订单关闭</button>
                                </td>
                            @endif
                            @if($v->pay_status=="5")
                                <td class=''>
                                    <button type="button" class="btn btn-outline btn-danger">已退款</button>
                                </td>
                            @endif
                            @if($v->pay_status=="")
                                <td class=''>
                                    <button type="button" class="btn btn-outline btn-danger">支付失败</button>
                                </td>
                            @endif
                            <td class=''>{{$v->remark}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>


                <div class="row">
                    <div class="col-sm-6">
                        <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
                            {{$paginator->render()}}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('js')
    {{--
        <script>
            window.onload = get;
            function get(){
                getinfo();
            }
            function getinfo() {
                $.post("{{route("ApplyOrderBatchQuery")}}", {_token:"{{csrf_token()}}"}, function (data) {
                    for (var key in data) {
                        var selObj = $("#appends");
                        selObj.append(
                                "<tr class='gradeA odd' >"+
                                "<td class=''>"+data[key].action+"</td>" +
                                "<td class=''>"+data[key].apply_id+"</td>" +
                                "<td class=''>"+data[key].biz_id+"</td>" +
                                "<td class=''>"+data[key].create_time+"</td>" +
                                "<td class=''>"+data[key].update_time+"</td>" +
                                "<td class=''>"+data[key].result_code+"</td>"+
                                "<td class=''>"+data[key].result_code+"</td>"+
                                "</tr>"
                        );
                    }
                }, "json");
            }
        </script>--}}
@endsection