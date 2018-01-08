@extends('layouts.publicStyle')
<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<script type="text/javascript" src="https://webapi.amap.com/demos/js/liteToolbar.js"></script>
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>添加翼支付通道商户</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="{{url('admin/alipayopen/store')}}" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>商户全称</label>
                                <input class="form-control" type="text" placeholder="申请的商户全称" value="" required="required"  name="store_name" id="store_name">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户简称</label>
                                <input class="form-control" type="text" value="" placeholder="申请的商户简称" required="required"  name="alias_name" id="alias_name">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户代码</label>
                                <input placeholder="申请得到的商户代码" class="form-control" name="merchantId" id="merchantId"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系人</label>
                                <input required="required" placeholder="申请时填写的联系人" class="form-control"
                                       name="contact_name" id="contact_name"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系方式</label>
                                <input required="required" placeholder="申请时填写的联系方式" class="form-control"
                                       name="contact_phone" id="contact_phone" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户数据key</label>
                                <input required="required" placeholder="申请通过后,在翼支付管理后台得到的数据key" class="form-control" name="data_key"
                                       id="data_key"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户交易key</label>
                                <input placeholder="申请通过后,在翼支付管理后台得到的交易key" required="required" class="form-control"
                                       name="pay_key" id="pay_key" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>绑定商户merchant_id</label>
                                <input placeholder="在我们系统申请得到商户账号id,可在收银员统一管理里面查看" required="required" class="form-control"
                                       name="merchant_id" id="merchant_id" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
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
            if(!$("#store_name").val()){
                layer.msg('商户全称必填!');
                $("#store_name").focus();
            }else if(!$("#alias_name").val()){
                layer.msg('商户简称必填!');
                $("#alias_name").focus();
            }else if(!$("#merchantId").val()){
                layer.msg('商户代码必填!');
                $("#merchantId").focus();
            }else if(!$("#contact_name").val()){
                layer.msg('联系人必填!');
                $("#contact_name").focus();
            }else if(!$("#contact_phone").val()){
                layer.msg('联系方式必填!');
                $("#contact_phone").focus();
            }else if(!$("#data_key").val()) {
                layer.msg('商户数据key必填!');
                $("#data_key").focus();
            }else if(!$("#pay_key").val()) {
                layer.msg('商户交易key必填!');
                $("#pay_key").focus();
            }else if(!$("#merchant_id").val()) {
                layer.msg('绑定商户merchant_id必填!');
                $("#merchant_id").focus();
            }else{
                $.post("{{route("bestpay.add")}}",
                    {
                        _token: '{{csrf_token()}}',
                        store_name: $("#store_name").val(),
                        alias_name: $("#alias_name").val(),
                        merchantId: $("#merchantId").val(),
                        contact_name: $("#contact_name").val(),
                        contact_phone: $("#contact_phone").val(),
                        data_key: $("#data_key").val(),
                        pay_key: $("#pay_key").val(),
                        merchant_id: $("#merchant_id").val(),
                    },
                    function (result) {
                        if (result.success) {
                            //询问框
                            layer.confirm('提交保存成功！', {
                                btn: ['返回商户列表页','留在本页面'], //按钮
                            }, function () {
                                window.location.href = "{{route('bestpay.index')}}";
                            },function () {
                                
                            });
                        } else {
                            layer.msg(result.msg);
                        }
                    }, "json")
            }
            // 验证身份证
            function isCardNo(card) {
                var pattern = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
                return pattern.test(card);
            }
            // 验证中文名称
            function isChinaName(name) {
                var pattern = /^[\u4E00-\u9FA5]{1,6}$/;
                return pattern.test(name);
            }

        }

    </script>
@endsection
@endsection