@extends('layouts.koubei')
@section('title')
    {{$shopinfo['store_name']}}
@endsection
@section('content')
    <div class="main">
        <p class="cite">
		<span>
			<img src="{{url('/img/site.jpg')}}">
		</span>{{$shopinfo['store_name']}}
        </p>
        <div class="type">

            <div class="top clear">
                <span>消费总金额（元）</span>
                <input id="total_amount" value="" type="number" placeholder="请询问服务员后输入">
            </div>
            <div class="bot clear">
                <span class="no">不参与优惠金额（元）</span>
                <input type="number" placeholder="请询问服务员后输入">
            </div>
        </div>
        <div class="type type_bot">
            <div class="bot clear">
                <span>选填备注</span>
                <input type="text" placeholder="如包房号、服务员号等" class="notice">
            </div>
        </div>
        {{--   <p class="sale">商家优惠</p>
           <p class="down">8.5折</p>--}}
        <button type="button" id="payLogButton" class="btn db" style="font-size: 18px;">和店员已确认，立即买单</button>
        <input type="hidden" value="{{$shopinfo['store_id']}}" id="store_id">
        <input type="hidden" value="{{$cashier_id}}" id="cashier_id">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        @endsection
        @section('js')
            <script>
                document.addEventListener('AlipayJSBridgeReady', function () {
                    var ck=1;
                    $("#payLogButton").click(function () {
                        if(ck){
                            ck=0;
                            $.post("{{route('ms_handle')}}", {
                                total_amount: $("#total_amount").val(),
                                cashier_id: $("#cashier_id").val(),
                                store_id: $("#store_id").val(),
                                _token: $("#token").val()
                            }, function (data) {
                                $('#payLogButton').removeAttr("disabled");
                                if (data.status == 1) {

                                    AlipayJSBridge.call("tradePay", {
                                        tradeNO: data.trade_no
                                    }, function (result) {
                                        //付款成功
                                        if (result.resultCode == "9000") {
                                            window.location.href = "{{url('api/minsheng/page?flag=s')}}"+'&price=' + $("#total_amount").val();
                                        }
                                        // 用户中途取消
                                        if (result.resultCode == "6001") {
                                            window.location.href = "{{url('api/minsheng/page?flag=m')}}";
                                        }

                                    });
                                } else {
                                    alert(data.message);
                                    window.location.href = "{{url('api/minsheng/page?flag=f')}}";
                                }
                            }, "json");
                        }

                    });
                }, false);
            </script>
@endsection



