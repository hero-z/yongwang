@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>修改口碑店铺名称</h5>
            </div>
            <span style="color:red">{{session('warnning')}}</span>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="{{route("updateshoplists")}}" method="post">
                            <input type="hidden" id="id" value="{{$list->id}}" name="id">
                            <div class="form-group">
                                <label>店铺名称</label>
                                <input value="{{$list->main_shop_name}}" id="auth_shop_name" class="form-control"
                                       type="text" name="name">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div>
                                <button  class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                         type="submit">
                                    <strong>确认修改</strong>
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