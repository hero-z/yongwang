@extends('layouts.koubei')
@section('title')
    {{$shop->alias_name}}
@endsection
@section('content')
    <div class="main">
        <p class="cite">
		<span>
			<img src="{{url('/img/site.jpg')}}">
		</span>{{$shop->alias_name}}
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
                <input type="text" placeholder="如包房号、服务员号等" class="notice" name="remark" id="remark">
            </div>
        </div>
        {{--   <p class="sale">商家优惠</p>
           <p class="down">8.5折</p>--}}
        <button type="button" id="payLogButton" onclick="pay()" class="btn db" style="font-size: 18px;">和店员已确认，立即买单</button>
        <input type="hidden" value="{{$shop->store_id}}" id="store_id">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <input type="hidden" id="m_id" value="{{$m_id}}">
        </div>
@endsection
@section('js')
            <script>

            </script>
            <script>
                function pay() {
                    $.post("{{route('webankdoPay')}}", {
                        total_amount: $("#total_amount").val(),
                        store_id: $("#store_id").val(),
                        _token: $("#token").val(),
                        remark:$("#remark").val(),
                        type:2,
                        m_id:$("#m_id").val()
                    }, function (data) {
                        if (data.success == 1) {
                            AlipayJSBridge.call("tradePay", {
                                tradeNO: data.channelNo
                            }, function (result) {
                                //付款成功
                                if (result.resultCode=="9000") {
                                    window.location.href="{{url('admin/webank/weixin/alipaysuccess?price=')}}"+$("#total_amount").val();
                                }
                                if(result.resultCode=="6001"){
                                    window.location.href = "{{url('admin/webank/weixin/alipayerror?code=6001')}}";
                                }

                            });
                        } else {
                            window.location.href = "{{url('admin/alipayopen/OrderErrors')}}";
                        }
                    }, "json");
                }
                /*document.addEventListener('AlipayJSBridgeReady', function () {
                    $("#payLogButton").click(function () {
                        $.post("{{route('webankdoPay')}}", {
                            total_amount: $("#total_amount").val(),
                            store_id: $("#store_id").val(),
                            _token: $("#token").val(),
                            remark:$("#remark").val(),
                            type:2,
                            m_id:$("#m_id").val()
                        }, function (data) {
                            layer.info('asdfsa1221');
                            if (data.success == 1) {
                                AlipayJSBridge.call("tradePay", {
                                    tradeNO: data.trade_no
                                }, function (result) {
                                    //付款成功
                                    if (result.resultCode=="9000") {
                                        window.location.href="{{url('admin/alipayopen/PaySuccess?price=')}}"+$("#total_amount").val();
                                    }
                                    if(result.resultCode=="6001"){
                                        window.location.href = "{{url('admin/alipayopen/OrderErrors?code=6001')}}";
                                    }

                                });
                            } else {
                                window.location.href = "{{url('admin/alipayopen/OrderErrors')}}";
                            }
                        }, "json");
                    });
                }, false);*/
            </script>

@endsection



