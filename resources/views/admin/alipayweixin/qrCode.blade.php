@extends('layouts.publicStyle')
@section('content')

    <div style="text-align: center">
        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(500)->generate($code_url)) !!} ">
        <p>{{$store_name}}-收款</p>
    </div>
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>多码合一</h5>
            </div>
            <div class="ibox-content">
                <div class="well well-lg">
                    请服务商先测试是否可用！
                </div>
            </div>
        </div>
    </div>
@endsection