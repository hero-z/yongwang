@extends('layouts.publicStyle')
@section('content')

<div style="text-align: center">
    <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(500)->generate($code_url)) !!} ">
    <p>{{$auth_shop_name}}-收款码@if($merchant_name)({{$merchant_name}})@endif</p>
</div>
<div class="col-sm-6">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>收款码说明</h5>
        </div>
        <div class="ibox-content">
            <div class="well well-lg">
                如果有开口碑店铺！请在口碑审核成功以后用口碑店铺列表的店铺收款码！
                  必须先在支付宝后台签约当面付产品（<a href="https://openhome.alipay.com/isv/isvMerchantManage.htm" target="_blank">https://openhome.alipay.com/isv/isvMerchantManage.htm</a>
            </div>
        </div>
    </div>
</div>
@endsection