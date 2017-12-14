@extends('layouts.antui')

@section('content')
    <div class="am-message result">
        <i class="am-icon result pay"></i>
        <div class="am-message-main">支付成功</div>
        <div class="am-message-em">{{$price}}元</div>
        <a class="am-button blue success" role="alert" aria-live="assertive" onclick="closed()">
            确认
        </a>
    </div>
    <script>
        function closed() {
            AlipayJSBridge.call('closeWebview');
        }

    </script>
    @foreach($ad as $v)
        @if($v->type=="alipay"&&$v->position==1)
            <div class="am-message-main"><a href="{{$v->url}}"><img src="{{$v->pic}}" style="box-sizing: border-box; max-width: 100%; height: auto;  vertical-align: middle;border: 0;"></a></div>
        @endif
    @endforeach
@endsection