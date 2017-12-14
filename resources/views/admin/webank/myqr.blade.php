@extends('layouts.publicStyle')
@section('content')
    <div style="text-align: center">
        <h2>{{$store_name}}@if(isset($merchant_name))({{$merchant_name}}) @endif</h2>
        <h2>{{$store_id}}</h2>
        <img style="width: 80%" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(300)->generate($code_url)) !!} ">
    </div>
    <div style="text-align: center;margin-top: 12px;">
        <img  src="https://act.weixin.qq.com/static/cdn/img/wepayui/0.1.1/wepay_logo_green.svg" alt="" width="40" height="40">
        <img style="margin-left: 12px" src="{{url('/img/alipay_logo.png')}}" alt="" width="40" height="40">
    </div>
@endsection