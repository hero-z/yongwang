<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>返佣管理</title>
    <meta name="description" content="@yield('keywords')">
    <meta name="keywords" content="@yield('description')">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="apple-touch-icon-precomposed" href="assets/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="Amaze UI"/>
    <script src="{{asset('/amazeui/assets/js/echarts.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/amazeui.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/amazeui.datatables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/app.css')}}">
    <script src="{{asset('/amazeui/assets/js/jquery.min.js')}}"></script>
    <script src="{{asset('/amazeui/assets/js/theme.js')}}"></script>
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">


    <script src="{{asset('/amazeui/assets/js/locales/amazeui.datetimepicker.zh-CN.js')}}"></script>
    <script src="{{asset('/amazeui/assets/js/amazeui.datetimepicker.js')}}"></script>
    <script src="{{asset('/amazeui/assets/js/amazeui.datetimepicker.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/amazeui.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/amazeui.datetimepicker.css')}}"/>

    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/app.css')}}">
    @yield('css')
</head>
<body data-type="widgets" class="theme-white">


<div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
    <div class="widget am-cf">
        <form method='post' class="am-form tpl-form-line-form" action="{{route('userprofit')}}">
            {{--<div class="am-u-sm-12 am-u-md-12 am-u-lg-2" style="float: right">--}}
                {{--<a href="{{route('datalist')}}">--}}
                    {{--<button type='button'  class="am-btn am-btn-primary tpl-btn-bg-color-success " style="float: right;background:#0a0">切换至旧订单</button>--}}
                {{--</a>--}}
            {{--</div>--}}
            <div class="am-input-group am-datepicker-date am-u-sm-12 am-u-md-12 am-u-lg-2 doc-example">
                <input size="16" type="text" id="time_start" name="time_start" placeholder="开始日期" value="{{$time_start or ''}}"  class="form-datetime-lang am-form-field">
                {{--<input size="16" type="text" id="time_start" name="time_start" value="@if(!empty($time_start)){{$time_start}}@else{{date('Y-m-d').' 00:00'}}@endif"  class="form-datetime-lang am-form-field">--}}

                <script>
                    (function($){
                        // 也可以在页面中引入 amazeui.datetimepicker.zh-CN.js
                        $.fn.datetimepicker.dates['zh-CN'] = {
                            days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],
                            daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],
                            daysMin:  ["日", "一", "二", "三", "四", "五", "六", "日"],
                            months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                            monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                            today: "今日",
                            suffix: [],
                            meridiem: ["上午", "下午"]
                        };

                        $('.form-datetime-lang').datetimepicker({
                            language:  'zh-CN',
                            format: 'yyyy-mm-dd hh:ii'
                        });
                    }(jQuery));
                </script>



            </div>
            <div class="am-input-group am-datepicker-date am-u-sm-12 am-u-md-12 am-u-lg-2 doc-example">
                <input size="16" type="text" id="time_end" name="time_end" placeholder="结束日期" value="{{$time_end or ''}}"  class="form-datetime-lang am-form-field">
                {{--                        <input size="16" type="text" id="time_end" name="time_end" value="@if(!empty($time_end)){{$time_end}}@else{{date('Y-m-d').' 23:59'}}@endif"  class="form-datetime-lang am-form-field">--}}

                <script>
                    (function($){
                        // 也可以在页面中引入 amazeui.datetimepicker.zh-CN.js
                        $.fn.datetimepicker.dates['zh-CN'] = {
                            days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],
                            daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],
                            daysMin:  ["日", "一", "二", "三", "四", "五", "六", "日"],
                            months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                            monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                            today: "今日",
                            suffix: [],
                            meridiem: ["上午", "下午"]
                        };

                        $('.form-datetime-lang').datetimepicker({
                            language:  'zh-CN',
                            format: 'yyyy-mm-dd hh:ii'
                        });
                    }(jQuery));
                </script>


            </div>

            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <select data-am-selected="{btnSize: 'sm'}" name="time" id="time" style="display: none;">
                    <option value="1"  >最近7天</option>
                    <option value="2" @if($time&&$time=='2') selected @endif >今日(至当前时间)</option>
                    <option value="3" @if($time&&$time=='3') selected @endif >昨日</option>
                    <option value="4" @if($time&&$time=='4') selected @endif >当月(至当前时间)</option>
                    <option value="5" @if($time&&$time=='5') selected @endif >上月</option>
                    <option value="9" @if($time&&$time=='9') selected @endif >自主选择日期</option>
                </select>
                <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">筛选</button>
                <button type='button' onclick="exportdata()" class="am-btn am-btn-primary tpl-btn-bg-color-success " style="background:gray">导出数据</button>
            </div>
            {{csrf_field()}}
        </form>


    </div>
</div>


<div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
    <div class="widget am-cf">
        <div class="widget-head am-cf">
            <div class="widget-title am-fl">返佣信息</div>
            <div class="widget-function am-fr">
                <a href="javascript:;" class="am-icon-cog"></a>
            </div>
        </div>
        <div class="widget-body  widget-body-lg am-fr">

            <table width="100%" class="am-table am-table-compact am-table-striped tpl-table-black " id="example-r">
                <thead>
                <tr>
                    <th class="am-u-sm-2">订单号</th>
                    <th class="am-u-sm-1">金额</th>
                    <th class="am-u-sm-1">总佣金</th>
                    <th class="am-u-sm-1">业务商/费率/佣金</th>
                    <th class="am-u-sm-1">代理商/费率/佣金</th>
                    <th class="am-u-sm-1">员工/费率/佣金</th>
                    <th class="am-u-sm-1">商户费率</th>
                    <th class="am-u-sm-1">状态</th>
                    <th class="am-u-sm-1">订单来源</th>
                    <th class="am-u-sm-2">更新时间</th>
                </tr>
                </thead>
                <tbody>
                @if($list)
                    @foreach($list as $v)
                        <tr class="gradeX">
                            <td class="am-u-sm-2" style="word-wrap:break-word">{{$v->out_trade_no}}</td>
                            <td class="am-u-sm-1">{{$v->total_amount}}</td>
                            <td class="am-u-sm-1">{{$v->total_profit}}</td>
                            <td class="am-u-sm-1">@if($users&&isset($users[$v->service_id])){{$users[$v->service_id]}}@else无@endif/{{$v->service_rate}}/{{$v->service_profit}}</td>
                            <td class="am-u-sm-1">@if($users&&isset($users[$v->agent_id])){{$users[$v->agent_id]}}@else无@endif/{{$v->agent_rate}}/{{$v->agent_profit}}</td>
                            <td class="am-u-sm-1">@if($users&&isset($users[$v->employee_id])){{$users[$v->employee_id]}}@else无@endif/{{$v->employee_rate}}/{{$v->employee_profit}}</td>
                            <td class="am-u-sm-1">{{$v->merchant_rate}}</td>
                            <td class="am-u-sm-1">
                                @if($v->pay_status==$v->status)
                                    <button type="button" class="am-btn-success">正常</button>
                                @else
                                    <button type="button" class="am-btn-danger">异常 </button>
                                @endif
                            </td>
                            <td class="am-u-sm-1">
                                @if($v->order_from=='pingan')
                                    平安
                                @elseif($v->type==102)
                                    支付宝口碑
                                @endif

                            </td>
                            <td class="am-u-sm-2">{{$v->updated_at}}</td>
                        </tr>
                    @endforeach
                @endif
                <!-- more data -->
                </tbody>
            </table>
            <div class="row">
                <div class="col-sm-2" id="counts">共计:{{$counts}}条</div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="dataTables_paginate paging_simple_numbers"
                         id="DataTables_Table_0_paginate">
                        @if($list)
                            {{$list->links()}}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>







<!-- 全局js -->
<script src="{{asset('/amazeui/assets/js/amazeui.min.js')}}"></script>
<script src="{{asset('/amazeui/assets/js/amazeui.datatables.min.js')}}"></script>
<script src="{{asset('/amazeui/assets/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('/amazeui/assets/js/app.js')}}"></script>
<script type="text/javascript">
    function change(){

        $('#shop').removeAttr('disabled');
        $.post("{{route('getdplist')}}", {id:$('#users').val(),_token: "{{csrf_token()}}"},
                function (data) {
//                    alert(data.list);
                    var str="";
                    for(var i=0;i<data.length;i++){
                        str+="<option value='"+data[i].store_id+"'>"+data[i].store_name+"</option>"
                    }
                    $('#shop option').remove();
                    $("#shop").append('<option value="0">店铺</option>'+str);
//                $("#twoId").html(str);
                }, 'json');

    }
    function changeSelect(){

        $.post("{{route('getadminPaylist')}}", {id:$('#pay_source').val(),_token: "{{csrf_token()}}"},
                function (data) {
                    var str="";
                    for(var i=0;i<data.length;i++){
                        str+="<option value='"+data[i].id+"'>"+data[i].value+"</option>"
                    }
                    $('#store_type option').remove();
                    $("#store_type").append('<option value="0">支付方式</option>'+str);
                }, 'json');

    }
    function exportdata() {
        window.location.href="{{route('orderexportdata')}}"+"?users="+$('#users').val()+"&shop="+$('#shop').val()+"&time="+$('#time').val()+"&pay_source="+$('#pay_source').val()+"&status="+$('#status').val()+"&store_type="+$('#store_type').val()+"&time_end="+$('#time_end').val()+"&time_start="+$('#time_start').val();
        {{--$.post("{{route('orderexportdata')}}", {--}}
        {{--_token: "{{csrf_token()}}",--}}

        {{--users:$('#users').val(),--}}
        {{--shop:$('#shop').val(),--}}
        {{--time:$('#time').val(),--}}
        {{--pay_source:$('#pay_source').val(),--}}
        {{--store_type:$('#store_type').val(),--}}
        {{--status:$('#status').val(),--}}
        {{--time_end:$('#time_end').val(),--}}
        {{--time_start:$('#time_start').val()--}}
        {{--},--}}
        {{--function (data) {--}}

        {{--}, 'json');--}}

    }

</script>

<script>
    $(function(){
        $.post("{{route('gettotalamount')}}", {
                    _token: "{{csrf_token()}}",

                    users:$('#users').val(),
                    shop:$('#shop').val(),
                    time:$('#time').val(),
                    pay_source:$('#pay_source').val(),
                    store_type:$('#store_type').val(),
                    status:$('#status').val(),
                    time_end:$('#time_end').val(),
                    time_start:$('#time_start').val()
                },
                function (data) {
                    $('#totalje').html('总金额：'+data.totalje);
                }, 'json');
    })

</script>
</body>
</html>