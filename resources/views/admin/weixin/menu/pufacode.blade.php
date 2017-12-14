@extends('layouts.publicStyle')
@section('content')


<div style="text-align: center">
    <img style="width: 80%"  src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(500)->generate($code_url)) !!} ">
    <p>{{$store_name}}-收款码@if($cashier_name)({{$cashier_name}})@endif</p>
    <!-- <p>{{$code_url}}</p> -->
</div>
<div style="text-align: center;margin-top: 12px;">
    
</div>
@endsection