@extends('layouts.publicStyle')
@section('content')

<div style="text-align: center">
    <img style="width: 80%" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(500)->generate($code_url)) !!} ">
    <p>{{$auth_shop_name}}-收款码@if($merchant_name)({{$merchant_name}})@endif</p>
</div>
<div style="text-align: center;margin-top: 12px;">
    <img style="margin-left: 12px" src="{{url('/img/alipay_logo.png')}}" alt="" width="40" height="40">
</div>
@endsection