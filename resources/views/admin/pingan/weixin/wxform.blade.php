@extends('layouts.weixinpay')
@section('title','微信支付')
@section('css')
    <link href="{{asset('/css/weixinpay/wxpay.css')}}" rel="stylesheet">
@endsection
@section('content')
    <body ontouchstart>
    <input type="hidden" id="sub_merchant_id" value="{{$shop->sub_merchant_id}}">
    <div class="weui-wepay-pay__ft">
        <p class="weui-wepay-pay__info" style="font-size: 18px;">{{$shop->alias_name}}</p>
    </div>
    <div class="weui-wepay-pay">
        <div class="weui-wepay-pay__bd">
            <div class="weui-wepay-pay__inner">
                <h1 class="weui-wepay-pay__title">付款金额(元)</h1>
                <div class="weui-wepay-pay__inputs"><strong class="weui-wepay-pay__strong">￥</strong>
                    <input type="number" class="weui-wepay-pay__input" id="total_fee" placeholder="请输入金额"></div>
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
        function callpay() {
            $.post("{{route('PAWxOrder')}}", {
                        sub_merchant_id: $("#sub_merchant_id").val(),
                        _token: "{{csrf_token()}}",
                        total_fee: $("#total_fee").val()
                    },
                    function (data) {
                        if (data.success) {
                            window.location.href = 'https://openapi-liquidation.51fubei.com/payPage/?prepay_id=' + data.return_value.prepay_id + '&callback_url=' + '{{url('/admin/weixin/paySuccess')}}';
                        } else {
                            window.location.href = "{{url('admin/alipayopen/OrderErrors')}}";
                        }
                    }, 'json');
        }
    </script>
@endsection
