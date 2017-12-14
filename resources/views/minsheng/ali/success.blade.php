@extends('layouts.antui')

@section('content')
    <div class="am-message result">
        <i class="am-icon result pay"></i>
        <div class="am-message-main">支付成功</div>
        <div class="am-message-em">{{$price}}元</div>
    </div>

@endsection