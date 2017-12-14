@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>修改信息</h5>
            </div>
            <span style="color:red">{{session('warnning')}}</span>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="{{route("upPingan")}}" method="post">
                           <input type="hidden" id="id" value="{{$info->id}}" name="id">
                            <div class="form-group">
                                <label>店铺名称</label>
                                <input value="{{$list->alias_name}}" id="auth_shop_name" class="form-control"
                                       type="text" name="name">
                            </div>
                            <div class="form-group">
                                <label>收款码编号<span style="color:red">(修改后,原收款码将作废)</span></label>
                                @if($info)
                                <input value="{{$info->code_number}}" id="auth_shop_name" class="form-control"
                                       type="text" name="codes">
                                    @else

                                    <input value="" id="auth_shop_name" class="form-control"
                                           type="text" name="codes" placeholder="暂无收款码编号">
                              @endif
                            </div>
                            <div class="form-group">
                                <label>子商户号<span style="color:red">(不可修改)</span></label>
                                <input value="{{$list->sub_merchant_id}}" readonly class="form-control"
                                       type="text" >
                            </div>
                            <div class="form-group">
                                <label>商户公众号appid</label>
                                <input value="{{$list->wx_app_id}}" id="wx_app_id" name="wx_app_id" class="form-control"
                                       type="text" >
                            </div>
                            <div class="form-group">
                                <label>商户公众号secret</label>
                                <input value="{{$list->wx_secret}}" id="wx_secret" name="wx_secret" class="form-control"
                                      type="text" >
                          </div>
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
    @if($info)
    <div>
        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(300)->generate(url('Qrcode?code_number='.$info->code_number))) !!} ">
    </div>
    <div>
        <span style="color:green">该收款码已聚合多种支付通道,支持支付宝,微信,翼支付,京东等多种支付方式</span>
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