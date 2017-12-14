@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>网站配置</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form role="form" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>软件授权app_id</label>
                                <input placeholder="请联系有梦想科技获得您的app_id" value="{{$app->app_id}}" class="form-control"
                                       id="app_id" name="app_id"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>软件授权邮箱</label>
                                <input  placeholder="请输入授权邮箱" value="{{$app->token}}"
                                       class="form-control" id="token"
                                       name="token"
                                       type="email">
                            </div>
                            <div>
                                <button onclick="addpost()" class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                        type="button">
                                    <strong>保存</strong>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>

        function addpost() {
            $.post("{{route('setAppPost')}}",
                    {
                        _token: '{{csrf_token()}}',
                        app_id: $("#app_id").val(),
                        token: $("#token").val(),
                        app_version: $("#app_version").val(),
                        msg: $("#msg").val(),
                    },
                    function (result) {
                        if (result.status == 1) {
                            layer.alert('保存成功', {icon: 6});
                        }
                    }, "json")
        }
    </script>
@endsection