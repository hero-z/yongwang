<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>账单管理</title>
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
            <form method='post' class="am-form tpl-form-line-form" action="{{route('neworderlist')}}">
                <div class="am-u-sm-12 am-u-md-12 am-u-lg-2" style="float: right">
                    <a href="{{route('datalist')}}">
                        <button type='button'  class="am-btn am-btn-primary tpl-btn-bg-color-success " style="float: right;background:#0a0">切换至旧订单</button>
                    </a>
                </div>
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
                    <select data-am-selected="{btnSize: 'sm',searchBox: 1,maxHeight: 200}" id="users" name="users" style="display: none;" onchange="change()">
                        <option value="0" >员工归属</option>
                        @if($userlists)
                            @foreach($userlists as $v)
                                <option value="{{$v->id}}" @if($users&&$users==$v->id) selected @endif>{{$v->name}}</option>
                            @endforeach
                        @endif
                    </select>
                    <select data-am-selected="{btnSize: 'sm',searchBox: 1,maxHeight: 200}" id="shop" name="shop" style="display: none;" >
                        <option value="0" >店铺</option>
                        @if($shoplists)
                            @foreach($shoplists as $v)
                                <option value="{{$v->store_id}}" @if($shop&&$shop==$v->store_id) selected @endif>{{$v->store_name}}</option>
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
                    <select data-am-selected="{btnSize: 'sm'}" id="pay_source" name="pay_source"  style="display: none;" onchange="changeSelect()" >
                        <option value="0" >订单来源</option>
                        <option value="1" @if($pay_source&&$pay_source=='1') selected @endif  >扫码枪</option>
                        <option value="2" @if($pay_source&&$pay_source=='2') selected @endif>二维码</option>
                    </select>
                    <select data-am-selected="{btnSize: 'sm'}" id="store_type" name="store_type" style="display: none;">
                        <option value="0">支付方式</option>
                        @if($paylists)
                            @foreach($paylists as $k=>$v)
                                <option value="{{$k}}" @if($store_type&&$store_type==$k) selected @endif>{{$v}}</option>
                            @endforeach
                        @endif
                    </select>
                    <select data-am-selected="{btnSize: 'sm'}" name="status" id="status"  style="display: none;">
                        <option value="0" >成功订单</option>
                        <option value=2 @if($status&&$status=='2') selected @endif >取消支付</option>
                        <option value=3 @if($status&&$status=='3') selected @endif >等待支付</option>
                        <option value=4 @if($status&&$status=='4') selected @endif >订单关闭</option>
                        <option value=5 @if($status&&$status=='5') selected @endif >已退款</option>
                        <option value=9 @if($status&&$status=='9') selected @endif >全部订单</option>
                    </select>
                    <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">搜索</button>
                    <button type='button' onclick="exportdata()" class="am-btn am-btn-primary tpl-btn-bg-color-success " style="background:gray">导出数据</button>

                </div>



                <button type="button" class="page-header-button">
                    <span class="am-icon-paint-brush" id="totalje">总金额：计算中...</span>
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
                            <th class="am-u-sm-2">订单号</th>
                            <th class="am-u-sm-2">店铺ID</th>
                            <th class="am-u-sm-1">店铺名</th>
                            <th class="am-u-sm-1">金额</th>
                            <th class="am-u-sm-1">状态</th>
                            <th class="am-u-sm-1">支付类型</th>
                            <th class="am-u-sm-1">员工</th>
                            <th class="am-u-sm-1">备注</th>
                            {{--<th class="am-u-sm-1">归属员工</th>--}}
                            <th class="am-u-sm-2">更新时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($list)
                            @foreach($list as $v)
                                <tr class="gradeX">
                                    <td class="am-u-sm-2" style="word-wrap:break-word">{{$v->out_trade_no}}</td>
                                    <td class="am-u-sm-2">{{$v->store_id}}</td>
                                    <td class="am-u-sm-1">@if($allstore_names&&isset($allstore_names[$v->store_id])){{$allstore_names[$v->store_id]}}@endif</td>
                                    <td class="am-u-sm-1">{{$v->total_amount}}</td>
                                    <td class="am-u-sm-1">
                                        @if($v->pay_status==1)
                                            <button type="button" class="am-btn-success">支付成功</button>
                                        @elseif($v->pay_status==2)
                                            <button type="button" class="am-btn-danger">取消支付</button>
                                        @elseif($v->pay_status==3)
                                            <button type="button" class="am-btn-danger">等待支付</button>
                                        @elseif($v->pay_status==4)
                                            <button type="button" class="am-btn-danger">订单关闭</button>
                                        @elseif($v->pay_status==5)
                                            <button type="button" class="am-btn-danger">已退款 </button>
                                        @endif
                                    </td>
                                    <td class="am-u-sm-1">
                                        @if($v->type==101)
                                            支付宝当面付
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
                                    <td class="am-u-sm-1">
                                        <?php 

                                            if(isset($store_user[$v->store_id])&&isset($user_id[$store_user[$v->store_id]]))
                                            {
                                                echo ($user_id[$store_user[$v->store_id]]);
                                            }
                                            else
                                            {
                                                echo '店铺收款';
                                            }


                                         ?>

                                    </td>
                                    <td class="am-u-sm-1">{{$v->remark}}</td>
                                    {{--<td class="am-u-sm-1">{{$v->user_name}}</td>--}}
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
                                    {{$list->appends(['users'=>$users,'shop'=>$shop,'status'=>$status,'pay_source'=>$pay_source,'store_type'=>$store_type,'time'=>$time,'time_start'=>$time_start,'time_end'=>$time_end])->render()}}
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
		console.log("{{route('orderexportdata')}}")
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