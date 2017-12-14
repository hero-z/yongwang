@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <script src="{{asset('/jQuery-File-Upload/js/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js//jquery.iframe-transport.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js/jquery.fileupload.js')}}" type="text/javascript"></script>
    <style type="text/css">
        /* 图片展示样式 */
        .images_zone {
            position: relative;
            width: 120px;
            height: 120px;
            overflow: hidden;
            float: left;
            margin: 3px 5px 3px 0;
            background: #f0f0f0;
            border: 5px solid #f0f0f0;
        }

        .images_zone span {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
            width: 120px;
            height: 120px;
        }

        .images_zone span img {
            width: 120px;
            vertical-align: middle;
        }

        .images_zone a {
            text-align: center;
            position: absolute;
            bottom: 0px;
            left: 0px;
            background: rgba(255, 255, 255, 0.5);
            display: block;
            width: 100%;
            height: 20px;
            line-height: 20px;
            display: none;
            font-size: 12px;
        }

        /* 进度条样式 */
        .up_progress, .up_progress1, .up_progress2, .up_progress3, .up_progress4, .up_progress5, .up_progress6, .up_progress7, .up_progress8 {
            width: 300px;
            height: 13px;
            font-size: 10px;
            line-height: 14px;
            overflow: hidden;
            background: #e6e6e6;
            margin: 5px 0;
            display: none;
        }

        .up_progress .progress-bar, .up_progress1 .progress-bar1, .up_progress2 .progress-bar2, .up_progress3 .progress-bar3, .up_progress4 .progress-bar4, .up_progress5 .progress-bar5, .up_progress6 .progress-bar6, .up_progress7 .progress-bar7, .up_progress8 .progress-bar8 {
            height: 13px;
            background: #11ae6f;
            float: left;
            color: #fff;
            text-align: center;
            width: 0%;
        }
    </style>
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>修改微众店铺信息</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form role="form" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>微众银行微信商户号@if($wxstatus==0)<span style="color:grey">(未审核)</span>@elseif($wxstatus==1)<span style="color:green">(已通过)</span>@elseif($wxstatus==2)<span style="color:red">(审核不通过)</span>@else<span >(未成功查询到状态)</span>@endif</label>
                                <input required readonly  value="{{$wxstore_union->wb_merchant_id}}" class="form-control" id="wx_wb_merchant_id" name="wx_wb_merchant_id"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>微众银行支付宝商户号@if($alistatus==0)<span style="color:grey">(未审核)</span>@elseif($alistatus==1)<span style="color:green">(已通过)</span>@elseif($alistatus==2)<span style="color:red">(审核不通过)</span>@else<span >(未成功查询到状态)</span>@endif</label>
                                <input required readonly  value="{{$alistore_union->wb_merchant_id}}" class="form-control" id="ali_wb_merchant_id" name="ali_wb_merchant_id"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>商户名称</label>
                                <input required placeholder="请输入您的商户名称" value="{{$store->store_name}}" class="form-control" id="store_name" name="store_name"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>商户简称</label>
                                <input required placeholder="请输入您的商户简称" value="{{$store->alias_name}}" class="form-control" id="alias_name" name="alias_name"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>证件类型</label>
                                <input required placeholder="请输入您的证件类型（如：身份证，军人军官证)" value="{{$store->id_type}}" class="form-control" id="id_type" name="id_type"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>证件号码</label>
                                <input required placeholder="请输入您的证件号码" value="{{$store->id_no}}" class="form-control" id="id_no" name="id_no"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>营业执照编号</label>
                                <input required placeholder="请输入您的营业执照编号" value="{{$store->licence_no}}" class="form-control" id="licence_no" name="licence_no"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>联系人姓名</label>
                                <input required placeholder="请输入联系人姓名" value="{{$store->contact_name}}" class="form-control" id="contact_name" name="contact_name"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>联系人电话</label>
                                <input required placeholder="请输入联系人电话" value="{{$store->contact_phone_no}}" class="form-control" id="contact_phone_no" name="contact_phone_no"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>商户类别码(MCC）</label>
                                <input required  value="{{$store->merchant_type_code}}" class="form-control" id="merchant_type_code" name="merchant_type_code"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>微信经营类目</label>
                                <input required  value="{{$wxstore_union->category_id}}" class="form-control" id="wx_category_id" name="wx_category_id"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>支付宝经营类目</label>
                                <input required  value="{{$alistore_union->category_id}}" class="form-control" id="ali_category_id" name="ali_category_id"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>商户账号</label>
                                <input required  value="{{$store->account_no}}" class="form-control" id="account_no" name="account_no"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>账户开户行号</label>
                                <input required  value="{{$store->account_opbank_no}}" class="form-control" id="account_opbank_no" name="account_opbank_no"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>账户名称</label>
                                <input required  value="{{$store->account_name}}" class="form-control" id="account_name" name="account_name"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>账户开户行名称</label>
                                <input required  value="{{$store->account_opbank}}" class="form-control" id="account_opbank" name="account_opbank"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>回拥费率(必须大于签约费率且小于100%)</label>
                                <input required  value="{{$store->commission_rate}}" class="form-control" id="commission_rate" name="commission_rate"
                                       type="text">
                            </div>
                            <input type="hidden" id="store_id" name="store_id" value="{{$store->store_id}}">
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
            if(!$("#store_name").val()){
                layer.msg('请输入商户名称!');
                $("#store_name").focus();
            }else if(!$("#alias_name").val()){
                layer.msg('请输入商户简称!');
                $("#alias_name").focus();
            }else if(!$("#id_type").val()){
                layer.msg('请输入证件类型!');
                $("#id_type").focus();
            }else if(!$("#id_no").val()){
                layer.msg('请输入证件号码!');
                $("#id_no").focus();
            }else if(!$("#licence_no").val()){
                layer.msg('请输入营业执照编号!');
                $("#licence_no").focus();
            }else if(!$("#contact_name").val()){
                layer.msg('请输入联系人名称!');
                $("#contact_name").focus();
            }else if(!$("#contact_phone_no").val()){
                layer.msg('请输入联系人手机号!');
                $("#contact_phone_no").focus();
            }else if(!$("#merchant_type_code").val()){
                layer.msg('请输入商户类别码(mcc)!');
                $("#merchant_type_code").focus();
            }else if(!$("#wx_category_id").val()){
                layer.msg('请输入微信经营类目!');
                $("#wx_category_id").focus();
            }else if(!$("#ali_category_id").val()){
                layer.msg('请输入支付宝经营类目!');
                $("#ali_category_id").focus();
            }else if(!$("#account_no").val()){
                layer.msg('请输入商户账号!');
                $("#account_no").focus();
            }else if(!$("#account_opbank_no").val()){
                layer.msg('请输入开户行号!');
                $("#account_opbank_no").focus();
            }else if(!$("#account_name").val()){
                layer.msg('请输入账户名称!');
                $("#account_name").focus();
            }else if(!$("#account_opbank").val()){
                layer.msg('请输入开户行名称!');
                $("#account_opbank").focus();
            }else if(!$("#commission_rate").val()){
                layer.msg('请输入回佣费率!');
                $("#commission_rate").focus();
            }else{
                $.post("{{route('webankeditmerchantfilepost')}}",
                        {
                            _token: '{{csrf_token()}}',
                            store_id:$("#store_id").val(),
                            wx_wb_merchant_id:$("#wx_wb_merchant_id").val(),
                            ali_wb_merchant_id:$("#ali_wb_merchant_id").val(),
                            store_name:$("#store_name").val(),
                            alias_name:$("#alias_name").val(),
                            id_type:$("#id_type").val(),
                            id_no:$("#id_no").val(),
                            licence_no:$("#licence_no").val(),
                            contact_name:$("#contact_name").val(),
                            contact_phone_no:$("#contact_phone_no").val(),
                            merchant_type_code:$("#merchant_type_code").val(),
                            wx_category_id:$("#wx_category_id").val(),
                            ali_category_id:$("#ali_category_id").val(),
                            account_no:$("#account_no").val(),
                            account_opbank_no:$("#account_opbank_no").val(),
                            account_name:$("#account_name").val(),
                            account_opbank:$("#account_opbank").val(),
                            commission_rate:$("#commission_rate").val(),
                        },
                        function (result) {
                            if (result.code == 1) {
                                layer.confirm('修改成功', {
                                    btn: ['确定'] //按钮
                                }, function () {
                                    window.location.href = "{{route('webankindex')}}";
                                });
//                                layer.alert('修改成功', {icon: 6});
                            }else{
                                layer.msg(result.msg);
                            }
                        }, "json")
            }


        }
    </script>
    <script type="text/javascript">
        publicfileupload("#fileupload", ".files", "#client_cert1", ".up_progress .progress-bar", ".up_progress");
        publicfileupload("#fileupload1", ".files1", "#client_key1", '.up_progress1 .progress-bar1', ".up_progress1");
        publicfileupload("#fileupload2", ".files2", "#client_cert2", ".up_progress2 .progress-bar2", ".up_progress2");
        publicfileupload("#fileupload3", ".files3", "#client_key2", '.up_progress3 .progress-bar3', ".up_progress3");
        function publicfileupload(fileid, imgid, postimgid, class1, class2) {
            //图片上传
            $(fileid).fileupload({
                dataType: 'json',
                add: function (e, data) {
                    var numItems = $('.files .images_zone').length;
                    if (numItems >= 2) {
                        alert('提交文件过多');
                        return false;
                    }
                    $(class1).css('width', '0px');
                    $(class2).show();
                    $(class1).html('上传中...');
                    data.submit();
                },
                done: function (e, data) {
                    $(class2).hide();
                    $('.upl').remove();
                    var d = data.result;
                    if (d.status == 0) {
                        alert(d.error);
                    } else {
                        jQuery(postimgid).val(d.path);
                    }
                },
                progressall: function (e, data) {
                    console.log(data);
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $(class1).css('width', progress + '%');
                }
            });
        }
    </script>
@endsection