@extends('layouts.weixinpay')
@section('title','微信支付')
@section('css')
    <link href="{{asset('/css/weixinpay/wxpay.css')}}" rel="stylesheet">
@endsection
@section('content')
    <body ontouchstart class="weui-wepay-pay-wrap">
    <div class="msg_success">
        <div class="weui-msg">
            <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
            <div class="weui-msg__text-area">
                <h2 class="weui-msg__title">支付成功</h2>
                <h1 class="weui-msg__title">{{$price}}</h1>
                <p class="weui-msg__desc"><span id="jumpTo">5</span>秒后自动跳转到收款界面</div></p>
            </div>
        </div>
        <div class="weui-wepay-logos weui-wepay-logos_ft">
            <i class="weui-wepay-logo-default weui-wepay-logo_gray"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    <script type="text/javascript">
        window.onload = get;
        function get() {
            countDown(5, '{{route('AlipayTradePayCreate')}}');
        }
    </script>
    <script>
        function countDown(secs, surl) {
            var jumpTo = document.getElementById('jumpTo');
            jumpTo.innerHTML = secs;
            if (--secs > 0) {
                setTimeout("countDown(" + secs + ",'" + surl + "')", 1000);
            }
            else {
                location.href = surl;
            }
        }
    </script>
    </body>


@endsection
