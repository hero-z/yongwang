@extends('layouts.publicStyle')
@section('content')

    <div style="text-align: center">
        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(500)->generate($code_url)) !!} ">
        <p>{{$store_name}}-银联收款码</p>
    </div>
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>收款码说明</h5>
            </div>
            <div class="ibox-content">
                <div class="well well-lg">

                </div>
            </div>
        </div>
    </div>
@endsection