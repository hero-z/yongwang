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
        <form method='post' class="am-form tpl-form-line-form" action="{{route('statistics.merchantbillquery')}}">
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
                <select data-am-selected="{btnSize: 'sm',searchBox: 1,maxHeight: 200}" id="merchant_id" name="merchant_id" style="display: none;" onchange="change()">
                    <option value="0" >收银员归属</option>
                    @if($users)
                        @foreach($users as $k=>$v)
                            <option value="{{$k}}" @if($merchant_id&&$merchant_id==$k) selected @endif>{{$v}}</option>
                        @endforeach
                    @endif
                </select>
                <select data-am-selected="{btnSize: 'sm',searchBox: 1,maxHeight: 200}" id="device_no" name="device_no" style="display: none;" onchange="change()">
                    <option value="0" >收银设备</option>
                    @if($devices)
                        @foreach($devices as $k=>$v)
                            <option value="{{$k}}" @if($device_no&&$device_no==$k) selected @endif>{{$v}}</option>
                        @endforeach
                    @endif
                </select>
                <select data-am-selected="{btnSize: 'sm'}" name="time" id="time" style="display: none;">
                    <option value="1"  >最近7天</option>
                    <option value="2" @if($time&&$time=='2') selected @endif >今日(至当前时间)</option>
                    <option value="3" @if($time&&$time=='3') selected @endif >昨日</option>
                    <option value="4" @if($time&&$time=='4') selected @endif >当月(至当前时间)</option>
                    <option value="5" @if($time&&$time=='5') selected @endif >上月</option>
                    <option value="9" @if($time&&$time=='9') selected @endif >自主选择日期</option>
                </select>
            </div>
            <div class="am-input-group  am-datepicker-date am-u-sm-12 am-u-md-12 am-u-lg-12">
                <select data-am-selected="{btnSize: 'sm'}" id="paytype" name="paytype" style="display: none;">
                    <option value="0">支付方式</option>
                    @if($paylist)
                        @foreach($paylist as $k=>$v)
                            <option value="{{$k}}" @if($paytype&&$paytype==$k) selected @endif>{{$v}}</option>
                        @endforeach
                    @endif
                </select>
                <select data-am-selected="{btnSize: 'sm'}" name="paystatus" id="paystatus"  style="display: none;">
                    <option value="0" >成功订单</option>
                    <option value=2 @if($paystatus&&$paystatus=='2') selected @endif >等待支付</option>
                    <option value=3 @if($paystatus&&$paystatus=='3') selected @endif >交易失败</option>
                    <option value=4 @if($paystatus&&$paystatus=='4') selected @endif >订单作废</option>
                    <option value=5 @if($paystatus&&$paystatus=='5') selected @endif >订单关闭</option>
                    <option value=9 @if($paystatus&&$paystatus=='9') selected @endif >全部订单</option>
                </select>
                <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">搜索</button>
                <button type='button' onclick="exportdata()" class="am-btn am-btn-primary tpl-btn-bg-color-success " style="background:gray">导出数据</button>

            </div>



            <button type="button" class="page-header-button">
                <span class="am-icon-paint-brush" style="display: block" id="totalje">总金额：计算中...</span>
                <span class="am-icon-paint-brush" style="display: block" id="receiptje">实收金额：计算中...</span>
                {{--<span class="am-icon-paint-brush"></span> 总金额：￥{{$totalje}}--}}
            </button>
            {{csrf_field()}}
        </form>


    </div>
</div>


<div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
    <div class="widget am-cf">
        <div class="widget-head am-cf">
            <div class="widget-title am-fl">账单信息</div>
            <div class="widget-function am-fr">
                <a href="javascript:;" class="am-icon-cog"></a>
            </div>
        </div>
        <div class="widget-body  widget-body-lg am-fr">

            <table width="100%" class="am-table am-table-compact am-table-striped tpl-table-black " id="example-r">
                <thead>
                <tr>
                    <th >店铺ID</th>
                    <th >商户账号/ID</th>
                    <th >设备号</th>
                    <th >系统订单号</th>
                    <th >订单号</th>
                    <th >订单金额</th>
                    <th >实收金额</th>
                    <th >支付状态</th>
                    <th >退款金额</th>
                    <th >支付类型</th>
                    <th >备注</th>
                    <th >更新时间</th>
                </tr>
                </thead>
                <tbody>
                @if($list)
                    @foreach($list as $v)
                        <tr class="gradeX">
                            <td >{{$v->store_id}}</td>
                            <td>@if(isset($merchants)&&$merchants&&array_key_exists($v->merchant_id,$merchants)){{$merchants[$v->merchant_id]}}@endif{{'/'.$v->merchant_id}}</td>
                            <td >{{$v->device_no}}</td>
                            <td  style="word-wrap:break-word">{{$v->out_trade_no}}</td>
                            <td  style="word-wrap:break-word">{{$v->trade_no}}</td>
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
                            <td >
                                @if($v->type==101)
                                    官方翼支付机具
                                @elseif($v->type==102)
                                    支付宝口碑
                                @elseif($v->type==103)
                                    当面付机具
                                @elseif($v->type==104)
                                    当面付固定码
                                @elseif($v->type==105)
                                    口碑机具
                                @elseif($v->type==106)
                                    口碑固定金额
                                @elseif($v->type==201)
                                    微信
                                @elseif($v->type==202)
                                    微信机具
                                @elseif($v->type==203)
                                    微信固定码
                                @elseif($v->type==301)
                                    平安支付宝
                                @elseif($v->type==302)
                                    平安微信
                                @elseif($v->type==303)
                                    平安京东
                                @elseif($v->type==304)
                                    平安翼支付
                                @elseif($v->type==305)
                                    平安支付宝机具
                                @elseif($v->type==306)
                                    平安微信机具
                                @elseif($v->type==307)
                                    平安京东机具
                                @elseif($v->type==401)
                                    银联扫码固定码
                                @elseif($v->type==402)
                                    银联扫码机具
                                @elseif($v->type==501)
                                    民生支付宝
                                @elseif($v->type==502)
                                    民生微信
                                @elseif($v->type==503)
                                    民生QQ钱包
                                @elseif($v->type==601)
                                    浦发支付宝
                                @elseif($v->type==602)
                                    浦发微信
                                @elseif($v->type==603)
                                    浦发支付宝机具
                                @elseif($v->type==604)
                                    浦发微信机具
                                @elseif($v->type==701)
                                    现金
                                @endif

                            </td>
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
                <div class="col-sm-2" id="counts">共计:{{$count}}条</div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="dataTables_paginate paging_simple_numbers"
                         id="DataTables_Table_0_paginate">
                        @if($list)
                            {{$list->appends(compact('merchant_id','device_no','paytype','paystatus','time','time_start','time_end'))->render()}}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    function exportdata() {
        window.location.href="{{route('statistics.merchantbillquery')}}"+"?export=1&merchant_id="+$('#merchant_id').val()+"&device_no="+$('#device_no').val()+"&paytype="+$('#paytype').val()+"&time="+$('#time').val()+"&paystatus="+$('#paystatus').val()+"&time_end="+$('#time_end').val()+"&time_start="+$('#time_start').val();

    }

</script>

<script>
    $(function(){
        $.post("{{route('statistics.merchantbillquery')}}", {
                _token: "{{csrf_token()}}",

                merchant_id:$('#merchant_id').val(),
                device_no:$('#device_no').val(),
                paytype:$('#paytype').val(),
                time:$('#time').val(),
                paystatus:$('#paystatus').val(),
                time_end:$('#time_end').val(),
                time_start:$('#time_start').val(),
                total_amount:1
            },
            function (data) {
                if(data.success){
                    amount=data.data;
                    $('#totalje').html('总金额：'+amount[0]);
                    $('#receiptje').html('实收金额：'+amount[1]);
                }
            }, 'json');
    })

</script>
@endsection