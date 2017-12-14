@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>银联通道配置信息</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>APP_ID</label>
                                <input value="{{$c['app_id']}}" id="app_id" placeholder="银联分配给接入平台的服务商唯一的 ID"
                                       class="form-control" name="app_id" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>软件生成的应用私钥</label>
                                <textarea id="rsa_private_key" style="min-height: 300px" placeholder="请填写软件生成的应用私钥"
                                          class="form-control"
                                          name="rsa_private_key">{{$c['rsa_private_key']}}</textarea>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>银联RSA应用公钥</label>
                                <textarea id="union_public_key" style="min-height: 100px" placeholder="银联RSA应用公钥"
                                          class="form-control"
                                          name="union_public_key">{{$c['union_public_key']}}</textarea>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>收单机构号</label>
                                <input value="{{$c['acquirer_id']}}" id="acquirer_id" placeholder="请填写收单机构号"
                                       class="form-control" name="acquirer_id" type="text">
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
    <div id="con"></div>
@section('js')
    <script>
        function addpost() {
            $.post("{{route('UnionPaySetPost')}}",
                {
                    _token: '{{csrf_token()}}', app_id: $("#app_id").val()
                    , rsa_private_key: $("#rsa_private_key").val(), union_public_key: $("#union_public_key").val(),
                    acquirer_id: $("#acquirer_id").val()
                },
                function (result) {
                    if (result.status == 1) {
                        layer.alert('保存成功', {icon: 6});
                    }
                }, "json")
        }
    </script>
@endsection
@endsection