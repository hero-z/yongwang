@extends('layouts.publicStyle')
<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<script type="text/javascript" src="https://webapi.amap.com/demos/js/liteToolbar.js"></script>
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>添加派派小盒</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="{{url('admin/alipayopen/store')}}" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>绑定商户账号id</label>
                                <input placeholder="在我们系统申请得到商户账号id,可在收银员统一管理里面查看" required="required" class="form-control"
                                       name="merchant_id" id="merchant_id" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>设备号</label>
                                <input class="form-control" type="text" value="" placeholder="派派小盒设备编号" required="required"  name="device_no" id="device_no">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>设备名称</label>
                                <input class="form-control" type="text" value="" placeholder="派派小盒设备名称" required="required"  name="name" id="name">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>设备秘钥</label>
                                <input placeholder="派派小盒通信秘钥" value="88888" class="form-control" name="device_pwd" id="device_pwd"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div>
                                <button class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                        type="button" onclick="addpost()">
                                    <strong>添加</strong>
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
            if(!$("#merchant_id").val()){
                layer.msg('绑定商户账号id必填!!');
                $("#merchant_id").focus();
            }else if(!$("#device_no").val()){
                layer.msg('派派小盒设备号必填!');
                $("#device_no").focus();
            }else if(!$("#name").val()){
                layer.msg('派派小盒设备名称必填!');
                $("#name").focus();
            }else if(!$("#device_pwd").val()){
                layer.msg('派派小盒设备秘钥必填!');
                $("#device_pwd").focus();
            }else{
                $.post("{{route("paipaiadd")}}",
                    {
                        _token: '{{csrf_token()}}',
                        m_id: $("#merchant_id").val(),
                        device_no: $("#device_no").val(),
                        name: $("#name").val(),
                        device_pwd: $("#device_pwd").val(),
                    },
                    function (result) {
                        if (result.success) {
                            //询问框
                            layer.confirm('提交添加成功！', {
                                btn: ['返回设备列表页','继续添加'], //按钮
                            }, function () {
                                window.location.href = "{{route('paipaiindex')}}";
                            },function () {

                            });
                        } else {
                            layer.msg(result.msg);
                        }
                    }, "json")
            }

        }

    </script>
@endsection
@endsection