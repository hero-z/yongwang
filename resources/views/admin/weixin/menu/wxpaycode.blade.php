@extends('layouts.publicStyle')
@section('content')

<div style="text-align: center">
    <img style="width: 80%" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(500)->generate($code_url)) !!} ">
    <p>{{$shop_name}}-收款码@if($merchant_name)({{$merchant_name}})@endif</p>
</div>
<div style="text-align: center;margin-top: 12px;">
    <img  src="https://act.weixin.qq.com/static/cdn/img/wepayui/0.1.1/wepay_logo_green.svg" alt="" width="40" height="40">
</div>
@endsection