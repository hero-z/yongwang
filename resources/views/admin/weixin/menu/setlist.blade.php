@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">
        <a href="{{route('WechatMenuAdd')}}">
            <button class="btn btn-success " type="button"><span class="bold">添加菜单</span></button>
        </a>
        <a href="javascript:" onclick="postcreate()">
            <button class="btn btn-warning " type="button"><span class="bold">生成菜单</span></button>
        </a>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>微信菜单配置</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-12">
                                <form role="form" method="post">
                                    {{csrf_field()}}
                                    <div class="form-group">
                                        <label>app_id</label>
                                        <input placeholder="请输入您app_id" value="{{$config->app_id}}" class="form-control" id="app_id" name="app_id"
                                               type="text">
                                    </div>
                                    <div class="form-group">
                                        <label>secret</label>
                                        <input placeholder="请输入secret" value="{{$config->secret}}" class="form-control" id="secret"
                                               name="secret"
                                               type="text">
                                    </div>
                                    <div class="form-group">
                                        <label>token</label>
                                        <input placeholder="请输入您token" value="{{$config->token}}" class="form-control" id="token" name="token"
                                               type="text">
                                    </div>
                                    <div>
                                        <button onclick="updatepost()" class="btn btn-sm btn-primary pull-right m-t-n-xs"
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

        </div>
    </div>
@endsection
@section('js')
    <script>
        function updatepost(){
            $.post("{{route('WechatMenuDoSet')}}",
                    {
                        _token: '{{csrf_token()}}',
                        app_id: $("#app_id").val(),
                        secret:$("#secret").val(),
                        token: $("#token").val()
                    },
                    function (result) {
                        if (result== 1) {
                            layer.alert('保存成功', {icon: 6});
                        }
                    }, "json")
        }
    </script>
@endsection