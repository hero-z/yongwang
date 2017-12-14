@extends('layouts.antui')
@section('content')
    <div class="am-message result">
        <i class="am-icon result pay"></i>
        <div class="am-message-main">支付成功</div>
        <div class="am-message-em">{{$price}}元</div>
        <div class="am-message-sub">付款用户账号({{$pay_user}})<span id="jumpTo">5</span>秒后自动跳转到收款界面</div>
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
@endsection