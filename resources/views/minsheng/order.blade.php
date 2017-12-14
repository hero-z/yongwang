@extends('layouts.public')
@section('content')
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>民生所有商户交易流水
                <!-- <small>所有微信支付订单的查询，商户可以通过该接口主动查询订单状态</small> -->
            </h5>
@include('minsheng.common.comlabel')
        </div>
        <div class="ibox-content">
            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper form-inline" role="grid">
                <table class="table table-striped table-bordered table-hover dataTables-example dataTable"
                       id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
                    <thead>
                    <tr role="row">
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 189px;" aria-label="渲染引擎：激活排序列升序" aria-sort="ascending">商户订单号
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 308px;" aria-label="平台：激活排序列升序">店铺ID
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 308px;" aria-label="平台：激活排序列升序">店铺名称
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
                            style="width: 142px;" aria-label="引擎版本：激活排序列升序">创建时间
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 144px;" aria-label="CSS等级：激活排序列升序">更新时间
                        </th>
                    </tr>
                    </thead>
                    <tbody id="appends">
                    @foreach($order as $v )
                        <tr class='gradeA odd'>
                            <td class=''>{{$v->out_trade_no}}</td>
                            <td class=''>{{$v->store_id}}</td>
                            <td class=''>{{$v->store_short_name}}</td>
                            <td class=''>{{$v->total_amount}}</td>
                            <td class=''>
                                @if($v->type==501)
                                    支付宝
                                @elseif($v->type=502)
                                    微信
                                @endif
                            </td>
                            <td style="color: green">
                                @if($v->pay_status=="1")
                                <button type="button" class="btn  btn-success">付款成功</button>
                                    @elseif($v->pay_status=="2")
                                    <button type="button" class="btn  btn-danger">付款失败</button>
                                    @elseif($v->pay_status=="3")
                                    <button type="button" class="btn  btn-info">等待付款</button>
                                @endif
                            </td>
                            <td class=''>{{$v->created_at}}</td>
                            <td class=''>{{$v->updated_at}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>


                <div class="row">
                    <div class="col-sm-6">
                        <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
                                {{ $order->links() }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
 