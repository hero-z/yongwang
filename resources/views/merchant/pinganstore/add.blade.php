@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>添加平安银行通道商户</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="{{url('admin/alipayopen/store')}}" method="post">
                            <input type="hidden" name="external_id" id="external_id"
                                   value="<?php echo 'p' . date('YmdHis', time())?>">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>门店分类</label>
                                <div class="col-sm-10">
                                    <select class="form-control m-b" name="category_id" id="category_id">
                                        <option>请选择分类</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户全称</label>
                                <input placeholder="商户全称,须与商户相关执照一致" class="form-control" name="name" id="name"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户简称</label>
                                <input required="required" placeholder="商户简称,在支付宝、微信支付时展示" class="form-control"
                                       name="alias_name" id="alias_name"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>客服电话</label>
                                <input required="required" placeholder="客服电话" class="form-control"
                                       name="service_phone" id="service_phone" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系人名称</label>
                                <input required="required" placeholder="联系人名称" class="form-control" name="contact_name"
                                       id="contact_name"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系人手机号</label>
                                <input placeholder="联系人手机号" required="required" class="form-control"
                                       name="contact_mobile" id="contact_mobile" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系人电话</label>
                                <input msg="门店电话号码"  placeholder="联系人电话,可以不填"
                                       class="form-control" name="contact_phone" id="contact_phone" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系人邮箱</label>
                                <input type="text" placeholder="联系人邮箱，可以不填" class="form-control" required="required"
                                       size="50" name="contact_email" id="contact_email">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户备注</label>
                                <input type="text" placeholder="商户备注，可以不填" class="form-control" size="50" name="memo"
                                       id="memo">
                            </div>
                            <div>
                                <button class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                        type="button" onclick="addpost()">
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
            $.post("{{route("PingAnStoreAddPost")}}",
                    {
                        _token: '{{csrf_token()}}',
                        category_id: $("#category_id").val(),
                        name: $("#name").val()
                        ,
                        alias_name: $("#alias_name").val(),
                        service_phone: $("#service_phone").val(),
                        contact_name: $("#contact_name").val()
                        ,
                        contact_phone: $("#contact_phone").val(),
                        contact_mobile: $("#contact_mobile").val(),
                        contact_email: $("#contact_email").val()
                        ,
                        memo: $("#memo").val(),
                        external_id: $("#external_id").val()
                    },
                    function (result) {
                        if (result.success) {
                            //询问框
                            layer.confirm('提交保存成功！等待审核！', {
                                btn: ['确定'] //按钮
                            }, function () {
                                window.location.href = "{{route('PingAnStoreIndex')}}";
                            });
                        } else {
                            layer.msg(result.error_message);
                        }
                    }, "json")

        }

    </script>
    <script>
        window.onload = get;
        function get() {
            getCategory();
        }
        //获得分类
        function getCategory() {
            $.post("{{route("getCategory")}}", {_token: $("#token").val()}, function (data) {
                for (var key in data) {
                    var selObj = $("#category_id");
                    var value = data[key].category_id;
                    var text = data[key].link;
                    selObj.append("<option value='" + value + "'>" + text + "</option>");
                }
            }, "json");
        }
    </script>

@endsection
@endsection