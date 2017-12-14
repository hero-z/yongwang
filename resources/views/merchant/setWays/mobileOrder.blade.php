@extends('layouts.amaze1')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">

    <script src="{{asset('/amazeui/assets/js/jquery.min.js')}}"></script>
    <link rel="apple-touch-icon-precomposed" href="assets/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="Amaze UI"/>
    <script src="{{asset('/amazeui/assets/js/echarts.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/amazeui.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/amazeui.datatables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/app.css')}}">
    <script src="{{asset('/amazeui/assets/js/jquery.min.js')}}"></script>
    <script src="{{asset('/amazeui/assets/js/theme.js')}}"></script>


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

    {{--<script src="{{asset('/amazeui/assets/js/locales/moment-with-locales.js')}}"></script>--}}
    {{--<script src="{{asset('/amazeui/assets/js/locales/amazeui_002.js')}}"></script>--}}
    <!-- 内容区域 -->

    <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
        <div class="widget am-cf">
            <form method='post' class="am-form tpl-form-line-form" action="{{route('mobileOrderlistssearch')}}">
                <div class="am-input-group am-datepicker-date am-u-sm-6 doc-example">
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
                <div class="am-input-group am-datepicker-date am-u-sm-6 doc-example">
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

                <div class="am-u-sm-6">
                    <select data-am-selected="{btnSize: 'xs',btnWidth: '100%',searchBox: 1,maxHeight: 200}" id="shop_branch" name="shop_branch"  style="display: none;" onchange="change()">
                        <option value="0" >店铺</option>
                        @if($shoplistmain)
                            @foreach($shoplistmain as $v)
                                <option value="{{$v->store_id}}" @if($shop_branch&&$shop_branch==$v->store_id) selected @endif>{{('[总店]').$v->store_name}}</option>
                            @endforeach
                        @endif
                        @if($shoplists)
                            @foreach($shoplists as $v)
                                <option value="{{$v->store_id}}" @if($shop_branch&&$shop_branch==$v->store_id) selected @endif>{{('[分店]').$v->store_name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="am-u-sm-6">
                    <select data-am-selected="{btnSize: 'xs',btnWidth: '100%',searchBox: 1,maxHeight: 200 }"  id="shop_cashier" name="shop_cashier"  style="display: none;" >
                        <option value="0" >收银员</option>
                        @if($userlists)
                            @foreach($userlists as $v)
                                <option value="{{$v->id}}" @if($shop_cashier&&$shop_cashier==$v->id) selected @endif>{{$v->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="am-u-sm-6">
                    <select data-am-selected="{btnSize: 'xs',btnWidth: '100%'}" name="time"  style="display: none;">
                        <option value="0"  >快速选择日期</option>
                        <option value="1" @if($time&&$time=='1') selected @endif >今日(至当前时间)</option>
                        <option value="2" @if($time&&$time=='2') selected @endif >昨日</option>
                        <option value="3" @if($time&&$time=='3') selected @endif >当月(至当前时间)</option>
                        <option value="4" @if($time&&$time=='4') selected @endif >上月</option>
                    </select>
                </div>
                <div class="am-u-sm-6">
                    <select data-am-selected="{btnSize: 'xs',btnWidth: '100%'}" id="pay_source" name="pay_source"  style="display: none;" onchange="changeSelect()" >
                        <option value="0" >订单来源</option>
                        <option value="1" @if($pay_source&&$pay_source=='1') selected @endif  >扫码枪</option>
                        <option value="2" @if($pay_source&&$pay_source=='2') selected @endif>二维码</option>
                    </select>
                 </div>
                <div class="am-u-sm-6">
                    <select data-am-selected="{btnSize: 'xs',btnWidth: '100%'}" id="store_type" name="store_type" style="display: none;">
                        <option value="0">支付方式</option>
                        @if($paylists)
                            @foreach($paylists as $k=>$v)
                                <option value="{{$k}}" @if($store_type&&$store_type==$k) selected @endif>{{$v}}</option>
                            @endforeach
                        @endif
                    </select>
                 </div>
                <div class="am-u-sm-6">
                    <select data-am-selected="{btnSize: 'xs',btnWidth: '100%'}" name="status"  style="display: none;">
                        <option value="0"  >成功订单</option>
                        <option value="1" @if($status&&$status=='1') selected @endif >失败订单</option>
                        <option value="2" @if($status&&$status=='2') selected @endif >全部订单</option>
                    </select>
                </div>
                <div class="am-u-sm-6">
                     <button class="am-btn am-btn-secondary am-btn-sm" type="submit">搜索</button>
                </div>
                <div class="am-u-sm-6">
                    <a href="{{route('newMobileOrderlists')}}">
                    <button class="am-btn am-btn-default am-btn-sm" type="button">新订单</button>
                     </a>
                 </div>
                {{csrf_field()}}
            </form>
        </div>
        </div>
     <div class="am-input-group am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-fl">账单总金额</div>
                    <div class="widget-function am-fr">
                        <a href="javascript:;" class="" style="color:red">￥{{$totalje}}</a>
                    </div>
                </div>
                <div class="widget-body  widget-body-lg am-fr">

                    <table width="100%" class="am-table am-table-compact  tpl-table-black " id="example-r">
                        <tr>
                            <th class="am-u-sm-3">金额</th>
                            <th class="am-u-sm-2">方式</th>
                            <th class="am-u-sm-2">状态</th>
                            <th class="am-u-sm-5">时间</th>

                        </tr>
                        @if($datapage)
                            @foreach($datapage as $v)
                                <tr class="gradeX">
                                    <td class="am-u-sm-3">{{$v->total_fee}}</td>
                                    <td class="am-u-sm-2">
                                        @if($v->type=='oalipay')
                                            当面
                                        @elseif($v->type=='salipay')
                                            付口碑
                                        @elseif($v->type=='weixin')
                                            微信
                                        @elseif($v->type=='alipay')
                                            支付宝
                                        @elseif($v->type=='jd')
                                            京东
                                        @elseif($v->type=='bestpay')
                                            翼支付
                                        @elseif($v->type=='moalipay'||$v->type=='mpalipay')
                                            支付宝机具
                                        @elseif($v->type=='mweixin'||$v->type=='mpweixin')
                                            微信机具
                                        @elseif($v->type=='money')
                                            现金
                                        @endif
                                    </td>
                                    @if($v->status=='SUCCESS'||$v->status=='TRADE_SUCCESS'||$v->status=='JD_SUCCESS')
                                        <td class="am-u-sm-2" style="color:green">成功</td>
                                    @else
                                        <td class="am-u-sm-2" style="color:red">失败</td>
                                    @endif
                                    <td class="am-u-sm-5">{{$v->updated_at}}</td>
                                </tr>
                        @endforeach
                    @endif
                        <!-- more data -->
                    </table>
                    <div class="row">
                            <div class="col-sm-2">共计:{{$counts}}条</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="dataTables_paginate paging_simple_numbers"
                                 id="DataTables_Table_0_paginate">
                                @if($paginator)
                                    {{$paginator->appends(['shop_branch'=>$shop_branch,'shop_cashier'=>$shop_cashier,'status'=>$status,'pay_source'=>$pay_source,'store_type'=>$store_type,'time'=>$time,'time_start'=>$time_start,'time_end'=>$time_end])->render()}}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    <script type="text/javascript">

        function change(){

            $.post("{{route('newgetmerchantCashier')}}", {id:$('#shop_branch').val(),_token: "{{csrf_token()}}"},
                    function (data) {
                        var str="";
                        for(var i=0;i<data.length;i++){
                            str+="<option value='"+data[i].id+"'>"+data[i].name+"</option>"
                        }
                        $('#shop_cashier option').remove();
                        $("#shop_cashier").append('<option value="0">收银员</option>'+str);
                    }, 'json');

        }
        function changeSelect(){

            $.post("{{route('newgetmerchantPaylist')}}", {id:$('#pay_source').val(),_token: "{{csrf_token()}}"},
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
            window.location.href="{{route('newexportdata')}}";
        }
    </script>
    <script>

        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "//hm.baidu.com/hm.js?b424d39312c46404f15e22574a531fbb";
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(hm);
        })();

        (function(w, d, s) {
            function gs(){
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){ (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(w , d, s ,'//www.google-analytics.com/analytics.js','ga');
                ga('create', 'UA-34196034-8', 'amazeui.org');
                ga('send', 'pageview');
            }
            if (w.addEventListener) { w.addEventListener('load', gs, false); }
            else if (w.attachEvent) { w.attachEvent('onload',gs); }
        }(window, document, 'script'));
    </script>
@endsection