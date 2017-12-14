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
        <h5 style="font-size: 14px;">下载账单</h5>
    </div>
    <div class=" clear"></div>
        <div class="form-group">
            <label class="col-sm-3 control-label" style="text-align: right">选择日期:</label>
            <div class="col-sm-6">
                <input id="bill_date" class="laydate-icon form-control layer-date">
            </div>
        </div>
        <div class=" clear"></div>
        <div class="form-group" style="margin-top: 20px">
            <label class="col-sm-3 control-label" style="text-align: right">选择支付平台:</label>
            <div class="col-sm-3">
                <select class="form-control m-b" id="pay_platform" name="pay_platform">
                    <option value="1">支付宝(对账单)</option>
                    <option value="2">微信(对账单)</option>
                    <option value="3">京东(对账单)</option>
                    <option value="4">翼支付(对账单)</option>
                    <option value="5">银行(打款明细)</option>
                </select>
            </div>
        </div>
        <div class=" clear"></div>
        <input type="hidden" id="file_type" value="1">
        <div class="form-group" style="margin-top: 20px">
            <label class="col-sm-3 control-label" style="text-align: right"></label>
            <div class="col-sm-3">
                <button class="btn btn-sm btn-primary pull-right m-t-n-xs" type="button" onclick="querybill()">
                    <strong>下载</strong>
                </button>
            </div>
        </div>

@endsection
@section('js')
            <script>
                function querybill() {
                    $.post("{{route("pingandownloadbillpost")}}",
                            {
                                _token: '{{csrf_token()}}',
                                bill_date: $("#bill_date").val(),
                                pay_platform: $("#pay_platform").val(),
                                file_type: $("#file_type").val()
                            },
                            function (result) {
                                if (result.success==1) {
                                    window.open(''+result.download_url);
//                                    document.location.href = ''+result.download_url;
                                } else {
                                    layer.msg(result.msg);
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