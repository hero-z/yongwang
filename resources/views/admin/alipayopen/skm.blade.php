@extends('layouts.publicStyle')
@section('content')

<div style="text-align: center">
    <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(500)->generate($code_url)) !!} ">
    <p>{{$store_name}}-门店收款码</p>
</div>
<div class="col-sm-6">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>门店收款码</h5>
        </div>
        <div class="ibox-content">
            <div class="well well-lg">
                如果门店开店成功！请用这个收款码，不要用口碑开店的那个收款码！这个收款码携带商户的shop_id,收款码和店铺有关联关系。
            </div>
        </div>
    </div>
</div>
@endsection