@extends('layouts.weixinpay')
@section('title','微信支付')
@section('css')
    <link href="{{asset('/css/weixinpay/wxpay.css')}}" rel="stylesheet">
@endsection
@section('content')
    <body ontouchstart>
    <input type="hidden" id="store_id" value="{{$shopinfo['store_id']}}">
    <input type="hidden" id="cashier_id" value="{{$cashier_id}}">
    <div class="weui-wepay-pay__ft">
        <p class="weui-wepay-pay__info" style="font-size: 18px;">{{$shopinfo['store_name']}}</p>
    </div>
    <div class="weui-wepay-pay">
        <div class="weui-wepay-pay__bd">
            <div class="weui-wepay-pay__inner">
                <h1 class="weui-wepay-pay__title">付款金额(元)</h1>
                <div class="weui-wepay-pay__inputs"><strong class="weui-wepay-pay__strong">￥</strong>
                    <input type="number" class="weui-wepay-pay__input" id="total_fee" placeholder="请输入金额"></div>
                    <!-- <input type="text" class="weui-wepay-pay__input" id="remark" placeholder="备注"></div> -->
                <div class="weui-wepay-pay__intro">可询问服务员消费总额</div>
            </div>
        </div>
        <div class="weui-wepay-pay__ft">
            <div class="weui-wepay-pay__btn">
                <button onclick="callpay()" class="weui-btn weui-btn_primary">立即支付</button>
            </div>

        </div>
    </div>
    <div class="weui-wepay-logos weui-wepay-logos_ft">
        <img src="https://act.weixin.qq.com/static/cdn/img/wepayui/0.1.1/wepay_logo_default_gray.svg" alt="" height="16">
    </div>
    </body>
    <script>
        //调用微信JS api 支付
        var ck=1;
        function onBridgeReady() {
            if(ck){
                ck=0;
                $.post("{{route('ms_wxhandle')}}", {
                        store_id: $("#store_id").val(),
                        // remark: $("#remark").val(),
                        cashier_id: $("#cashier_id").val(),
                        _token: "{{csrf_token()}}",
                        total_fee: $("#total_fee").val()
                    },
                    function (data) {
                        $('#payLogButton').removeAttr("disabled");
                        if(data.status=='1')
                        {

                            // alert(data.data);return;
                            WeixinJSBridge.invoke(
                                'getBrandWCPayRequest',eval('('+data.data+')'),
                                function (res) {
                                    if (res.err_msg == "get_brand_wcpay_request:ok") {
                                        window.location.href = "{{url('api/pufa/resultPage?price=')}}" + $("#total_fee").val();
                                    }
                                    else
                                    {
                                        // alert('订单支付失败')
                                        window.location.href = "{{url('api/pufa/resultPage')}}";

                                    }
                                }
                            );

                            // return;
                        }
                        // alert(data.message);


                    }, 'json');
            }

        }
        function callpay() {
            $('#payLogButton').attr("disabled", "disabled");
            if (typeof WeixinJSBridge == "undefined") {
                if (document.addEventListener) {
                    document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
                } else if (document.attachEvent) {
                    document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                    document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
                }
            } else {
                onBridgeReady();
            }
        }
    </script>
@endsection
