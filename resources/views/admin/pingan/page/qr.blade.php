@extends('layouts.publicStyle')
@section('content')

<div style="text-align: center">
    <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(500)->generate($code_url)) !!} ">
    <p>{{$store_name}}-收款码@if($merchant_name)({{$merchant_name}})@endif</p>
</div>
<div class="col-sm-6">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>收款码说明</h5>
        </div>
        <div class="ibox-content">
            <div class="well well-lg">
               收银员的收款码含收银员的信息！如果要区分收银员收款！用这个二维码即可
            </div>
        </div>
    </div>
</div>
@endsection