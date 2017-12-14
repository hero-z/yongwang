@extends('layouts.public')
@section('css')
    <style>
        .clear{
            background-color: #ffffff;
            border-top: 1px dashed #e7eaec;
            color: #ffffff;
            height: 1px;
            margin: 20px 0;
            clear: both;
        }
        .title {
            background-color: #e6e8e8;
            border-bottom: 1px solid transparent;
            border-color: #edf1f2 #edf1f2 transparent;
            border-radius: 2px 2px 0 0;
            clear: both;
            color: #333;
            display: block;
            font-weight: 700;
            height: 41px;
            padding: 15px 15px 3px;
        }
    </style>
@endsection
@section('content')
    <script src="{{asset('/js/plugins/layer/laydate/laydate.js')}}" type="text/javascript"></script>
    <div class="title">
        <h5 style="font-size: 14px;">查询账单</h5>
    </div>
    <div class=" clear"></div>
    <div class="form-group">
        <label class="col-sm-3 control-label" style="text-align: right">订单号:</label>
        <div class="col-sm-3">
            <input id="order" type="text" name="order" class="form-control">
        </div>
    </div>
    <div class=" clear"></div>
    <div class="form-group" style="margin-top: 20px">
        <label class="col-sm-3 control-label" style="text-align: right">选择订单号类型:</label>
        <div class="col-sm-3">
            <select class="form-control m-b" id="billtype" name="billtype">
                <option value="1">内部订单号</option>
                <option value="2">银行订单号</option>
            </select>
        </div>
    </div>
    <div class=" clear"></div>
    <input type="hidden" id="file_type" value="1">
    <div class="form-group" style="margin-top: 20px">
        <label class="col-sm-3 control-label" style="text-align: right"></label>
        <div class="col-sm-3">
            <button class="btn btn-sm btn-primary pull-right m-t-n-xs" type="button" onclick="querybill()">
                <strong>查询</strong>
            </button>
        </div>
    </div>

@endsection
@section('js')
    <script>
        function querybill() {
            $.post("{{route("pinganquerybillpost")}}",
                    {
                        _token: '{{csrf_token()}}',
                        billtype: $("#billtype").val(),
                        order: $("#order").val()
                    },
                    function (result) {
                        if (result.success) {
                            var status='未知';
                            if(result.return_value.hasOwnProperty("trade_status")){
                                status=result.return_value.trade_status
                            }else if(result.return_value.hasOwnProperty("trade_state")){
                                status=result.return_value.trade_state
                            }else if(result.return_value.hasOwnProperty("status")){
                                status=result.return_value.status
                            }else if(result.return_value.hasOwnProperty("trans_status")){
                                status=result.return_value.trans_status
                            }
                            switch (status){
                                case 'USERPAYING':
                                case '0':
                                case '1':
                                case 'A':
                                case 'WAIT_BUYER_PAY':
                                    status='交易创建，等待买家付款';
                                    break;
                                case 'TRADE_CLOSED':
                                case 'CLOSED':
                                    status='未付款交易超时关闭，或支付完成后全额退款';
                                    break;
                                case 'TRADE_SUCCESS':
                                case 'SUCCESS':
                                case '2':
                                case 'B':
                                    status='交易支付成功';
                                    break;
                                case 'TRADE_FINISHED':
                                    status='交易结束，不可退款';
                                    break;
                                case 'REFUND'||'4'||'5':
                                case '4':
                                case '5':
                                    status='转入退款';
                                    break;
                                case 'REVOKED':
                                    status='已撤销(刷卡支付)';
                                    break;
                                case 'PAYERROR':
                                case '3':
                                case 'C':
                                    status='支付失败(其他原因，如银行返回失败)';
                                    break;
                                case '6':
                                    status='退款失败';
                                    break;
                                case '7':
                                    status='部分退款';
                                    break;
                                case '9':
                                    status='撤单成功';
                                    break;
                                case 'G':
                                    status='订单作废（订单状态结果）';
                                    break;
                                default:
                                    status='未知';
                                    break;
                            }
                            layer.msg('订单状态:'+status+'订单净收金额:'+result.return_value.net_receipt_amount);
                        } else {
                            layer.msg(result.error_message);
                        }
                    }, "json")
        }
    </script>
    <script>
        //外部js调用
        laydate({
            elem: '#bill_date', //目标元素。由于laydate.js封装了一个轻量级的选择器引擎，因此elem还允许你传入class、tag但必须按照这种方式 '#id .class'
            event: 'focus' //响应事件。如果没有传入event，则按照默认的click
        });

        //日期范围限制
        var start = {
            elem: '#start',
            format: 'YYYY/MM/DD',
            min: laydate.now(), //设定最小日期为当前日期
            max: '2099-06-16 23:59:59', //最大日期
            istime: true,
            istoday: false,
            choose: function (datas) {
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        };
        var end = {
            elem: '#end',
            format: 'YYYY/MM/DD',
            min: laydate.now(),
            max: '2099-06-16 23:59:59',
            istime: true,
            istoday: false,
            choose: function (datas) {
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };
        laydate(start);
        laydate(end);
    </script>
@endsection