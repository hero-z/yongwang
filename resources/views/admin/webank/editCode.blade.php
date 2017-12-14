@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>修改信息</h5>
            </div>
            @if(session('info'))
            <span style="color:red">{{session('info')}}</span>
            @endif
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="{{route("upWebank")}}" method="post">
                            <input type="hidden" id="store_id" value="{{$store_id}}" name="id">
                            <div class="form-group">
                                <label>店铺名称</label>
                                <input value="{{$name}}" disabled id="name" class="form-control"
                                       type="text" name="name">
                            </div>
                            <div class="form-group">
                                <label>收款码编号<span style="color:red">(修改后,原收款码将作废)</span></label>
                                @if($qr)
                                    <input value="{{$qr->code_number}}" id="code_number" class="form-control"
                                           type="text" name="code_number">
                                @else

                                    <input value="" id="code_number" class="form-control"
                                           type="text" name="code_number" placeholder="暂无收款码编号">
                                @endif
                            </div>
                            <div class="form-group">
                                <label>微信支付返回商户号<span style="color:red">(不可修改)</span></label>
                                <input value="{{$wx_merchant_id}}" id="" readonly class="form-control"
                                       type="text" name="">
                            </div>
                            <div class="form-group">
                                <label>支付宝支付返回商户号<span style="color:red">(不可修改)</span></label>
                                <input value="{{$ali_merchant_id}}" id="" readonly class="form-control"
                                       type="text" name="">
                            </div>
                            <div class="form-group">
                                <label>商户公众号appid</label>
                                @if($store)
                                    <input value="{{$store->wx_app_id}}" id="wx_app_id" class="form-control"
                                           type="text" name="wx_app_id">
                                @else

                                    <input value="" id="wx_app_id" class="form-control"
                                           type="text" name="wx_app_id" placeholder="">
                                @endif
                            </div>
                            <div class="form-group">
                                <label>商户公众号secret</label>
                                @if($store)
                                    <input value="{{$store->wx_secret}}" id="wx_secret" class="form-control"
                                           type="text" name="wx_secret">
                                @else

                                    <input value="" id="wx_secret" class="form-control"
                                           type="text" name="wx_secret" placeholder="">
                                @endif
                            </div>
                            <input type="hidden" name="id" value="{{$qr->id}}">
                            <input type="hidden" name="store_id" value="{{$store_id}}">
                            <div class="hr-line-dashed"></div>
                            <div>
                                <button  class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                         type="submit">
                                    <strong>保存</strong>
                                </button>
                            </div>
                            {{csrf_field()}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="con"></div>
    @if($code_number)
        <div>
            <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(300)->generate(url('admin/webank/webankQrCode?code_number='.$code_number))) !!} ">
        </div>
    @endif
@section('js')
    {{--<script>--}}

    {{--function addpost() {--}}
    {{--$.post("{{route('updateOauthUserPost')}}",--}}
    {{--{--}}
    {{--_token: '{{csrf_token()}}',--}}
    {{--auth_phone: $("#auth_phone").val(),--}}
    {{--auth_shop_name: $("#auth_shop_name").val(),--}}
    {{--id: $("#id").val()--}}
    {{--},--}}
    {{--function (result) {--}}
    {{--if (result.status == 1) {--}}
    {{--layer.alert('保存成功', {icon: 6});--}}

    {{--} else {--}}
    {{--layer.alert('保存失败', {icon: 5});--}}

    {{--}--}}
    {{--}, "json")--}}
    {{--}--}}
    {{--</script>--}}
@endsection
@endsection