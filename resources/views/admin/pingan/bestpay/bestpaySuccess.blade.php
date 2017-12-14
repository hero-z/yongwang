@extends('layouts.antui')
@section('content')
    <div class="am-message result">
        <div class="am-message-main">支付成功</div>
        <img src="{{url('img/pingan/bestpay.png')}}">
        @foreach($ad as $v)
            @if($v->type=="bestpay"&&$v->position==1)
                <a href="{{$v->url}}"><img src="{{$v->pic}}" style="box-sizing: border-box; max-width: 100%; height: auto;  vertical-align: middle;border: 0;"></a>
            @endif
        @endforeach
    </div>

@endsection