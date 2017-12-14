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
        <input type="hidden" value="{{$shopinfo['store_id']}}" id="u_id">
        <input type="hidden" value="{{$cashier_id}}" id="cashier_id">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        @endsection
        @section('js')
            <script>
                document.addEventListener('AlipayJSBridgeReady', function () {
                    $("#payLogButton").click(function () {
                        $.post("{{route('MShandle')}}", {
                            total_amount: $("#total_amount").val(),
                            cashier_id: $("#cashier_id").val(),
                            u_id: $("#u_id").val(),
                            _token: $("#token").val()
                        }, function (data) {
                            if (data.status == 1) {
                                AlipayJSBridge.call("tradePay", {
                                    tradeNO: data.trade_no
                                }, function (result) {
                                    //付款成功
                                    if (result.resultCode == "9000") {
                                        window.location.href = "{{url('api/pufa/PaySuccess?price=')}}" + $("#total_amount").val();
                                    }
                                    // 用户中途取消
                                    if (result.resultCode == "6001") {
                                        window.location.href = "{{url('api/pufa/OrderErrors?code=6001')}}";
                                    }

                                    /*  //同步更新状态route('OrderStatus')
                                     $.post("", {
                                     trade_no: data.trade_no,
                                     resultCode: result.resultCode,
                                     _token: $("#token").val()
                                     },
                                     function (dataStatus) {
                                     //付款成功
                                     if (result.resultCode == "9000") {
                                     }
                                     if (result.resultCode == "6001") {
                                     }
                                     }, "json");*/

                                });
                            } else {
                                window.location.href = "{{url('api/pufa/OrderErrors')}}";
                            }
                        }, "json");
                    });
                }, false);
            </script>
@endsection



