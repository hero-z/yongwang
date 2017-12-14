@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>极光配置</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form role="form" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>开发者标识</label>
                                <input placeholder="请联系有梦想科技获得您的开发者标识" value="{{$list->DevKey}}" class="form-control"
                                       id="app_id" name="app_id"
                                       type="text" required>
                            </div>
                            <div class="form-group">
                                <label>密钥</label>
                                <input  placeholder="请输入授权密钥" value="{{$list->API_DevSecre}}"
                                        class="form-control" id="token"
                                        name="token"
                                        type="text" required>
                            </div>
                            <input type="hidden" value="{{$list->id}}" id="id">
                            <div>
                                <button onclick="addpost()" class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                        type="button" >
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
            $.post("{{route("updateJpushConfigs")}}",
                    {
                        _token: '{{csrf_token()}}',
                        id:$("#id").val(),
                        DevKey: $("#app_id").val(),
                        API_DevSecre: $("#token").val(),
                    },
                    function (result) {
                        if (result.status == 1) {
                            layer.alert('保存成功', {icon: 6});
                        }
                    }, "json")
        }
    </script>
@endsection