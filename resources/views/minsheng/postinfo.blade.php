@extends('layouts.publicStyle')
@section('title','商户注册')
@section('css')
@endsection
@section('content')
    <script src="{{asset('/jQuery-File-Upload/js/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js//jquery.iframe-transport.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js/jquery.fileupload.js')}}" type="text/javascript"></script>
    <script src="{{asset('uploadify/jquery.uploadify.min.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('uploadify/uploadify.css')}}">
   
    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>商户入驻民生银行注册（支付宝+微信）</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                            <input type="hidden" id="user_id" value="{{$recommender['user_id']}}">
                            <input type="hidden" id="user_name" value="{{$recommender['user_name']}}">
                        <input type="hidden" id="code_number" value="<?php echo $_GET['code_number']?>">
                        <div class="form-group">
                            <label>行业类别</label>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="industrId" id="category_id">
                                    <option value='0'>请选择分类</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label>商铺所在省份</label>
                            <div class="col-sm-10">
                                <select class="form-control m-b province" name="province" id="province">
                                    <option value='0'>选择省份</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label>商铺所在市区</label>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="city" id="city">
                                    <option value='0'>选择市区</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="form-group">
                                <label>商户经营类型</label>

                                <div class='radio-inline'>
                                    <label>
                                        <input type='radio' name='mchDealType' value='1' checked='checked'> 实体
                                    </label>
                                    &nbsp&nbsp&nbsp&nbsp
                                    <label>
                                        <input type='radio' name='mchDealType' value='2'> 虚拟
                                    </label>
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <label>商户名称</label>
                            <input placeholder="格式:地区+名称+行业 如:上海东方大酒店" class="form-control" name="merchantName"
                                   id="merchantName" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>商户简称</label>
                            <input placeholder="" class="form-control" name="merchantShortName" id="merchantShortName"
                                   type="text">
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label>商铺营运人</label>
                            <input placeholder="" class="form-control" name="principal" id="principal" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <label>手机号码：</label>
                            <input placeholder="" class="form-control" name="tel" id="tel" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <label >email邮箱：用于接收密码</label>
                            <input placeholder="" class="form-control" name="email" id="email" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <label>商铺所在地址：</label>
                            <input placeholder="" class="form-control" name="address" id="address" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>身份证号码：</label>
                            <input placeholder="" class="form-control" name="idCode" id="idCode" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>开户银行</label>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="bankId" id="bankId">
                                    <option value='0'>选择银行</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            <label>联行号</label>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="contactLine" id="contactLine">
                                    <option value='0'>请选择</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label>账户类型</label>

                            <div class='radio-inline'>
                                <label>
                                    <input type='radio' name='accountType' value='1' checked='checked'> 企业
                                </label>
                                &nbsp&nbsp&nbsp&nbsp
                                <label>
                                    <input type='radio' name='accountType' value='2'> 个人
                                </label>
                            </div>

                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label>开户支行名称</label>
                            <input placeholder="" class="form-control" name="bankName" id="bankName" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <label>银行卡号</label>
                            <input placeholder="" class="form-control" name="accountCode" id="accountCode" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>银行卡预留手机号码</label>
                            <input placeholder="" class="form-control" name="tel2" id="tel2" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>


<!--                         <div class="form-group">
                            <label>备注：</label>
                            <input placeholder="" class="form-control" name="remark" id="remark" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

 -->
                    </div>
                </div>
                <button style="width: 100%;height: 40px;font-size: 18px;" type="button" id='tijiao'
                        class="btn btn-primary">
                    确认信息提交资料
                </button>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')






    <script type="text/javascript">

        //获取分类======start==============
        function getCategory() {
            $.post(
                "{{route('PFCate')}}",
                {_token: $("#token").val()},
                function (data) {
                    if (!data) {
                        return;
                    }

                    var str = '';
                    for (var key in data) {
                        str += "<option value='" + data[key].industry + "'>" + data[key].rawstr + "</option>";
                    }
                    $("#category_id").append(str);

                }, "json");
        }

        $(function () {
            getCategory();
        });
        //获取分类======end==============


        //获取银行====start===
        function getBank() {
            $.post(
                "{{route('pufabank')}}",
                {_token: $("#token").val()},
                function (data) {
                    if (!data) {
                        return;
                    }

                    var str = '';
                    for (var key in data) {
                        str += "<option value='" + data[key].id + "'>" + data[key].bankname + "</option>";
                    }
                    $("#bankId").append(str);

                }, "json");
        }

        $(function () {
            getBank();
        });
        //获取银行====end===

        //获取省份====start===
        function getProvince() {
            $.post(
                "{{route('province')}}",
                {_token: $("#token").val()},
                function (data) {
                    if (!data) {
                        return;
                    }

                    var str = '';
                    for (var key in data) {
                        str += "<option value='" + data[key].id + "'>" + data[key].name + "</option>";
                    }
                    $(".province").append(str);

                }, "json");
        }

        $(function () {
            getProvince();
        });
        //获取省份====end===

        //获取市区====start===
        $("#province").change(function () {
                var pid = $(this).val();

                if (!pid) {
                    return;
                }
                $.post(
                    "{{route('city')}}",
                    {_token: $("#token").val(), pid: pid},
                    function (data) {
                        $("#city").children().remove();
                        $("#city").append("<option value='0'>选择市区</option>");

                        if (!data) {
                            return;
                        }

                        var str = '';
                        for (var key in data) {
                            str += "<option value='" + data[key].id + "'>" + data[key].name + "</option>";
                        }
                        $("#city").append(str);

                    }, "json");


            }
        );
        //获取市区====end===

// 获取联行号
    $('#bankId').change(function(){
                var keyname = $(this).find("option:selected").text();
                if (!keyname) {
                    return;
                }
                console.log(keyname);
                // 获取联行号
                $.post(
                    "{{route('pufabankrelation')}}",
                    {_token: $("#token").val(), keyname: keyname},
                    function (data) {
                        $("#contactLine").children().remove();
                        $("#contactLine").append("<option value='0'>请选择</option>");

                        if (!data) {
                            return;
                        }

                        var str = '';
                        for (var key in data) {
                            str += "<option value='" + data[key].bankid + "'>" + data[key].bankname + "</option>";
                        }
                        $("#contactLine").append(str);

                    }, "json");



    });





        var tijiaotimes = 1;
        //表单提交=========start======
        function addpost() {
            if (tijiaotimes != 1) {
                alert('请不要重复提交！');
                return;
            }
            tijiaotimes = 1;
            $.post(
                "{{route('PFautoStorePost')}}",
                {
                    // 推荐人
                    user_id: $("#user_id").val(),
                    user_name: $("#user_name").val(),

                    _token: '{{csrf_token()}}',
                    code_number: $("#code_number").val(),

                    merchantName: $("#merchantName").val(),
                    mchDealType: $("input[name='mchDealType']:checked").val(),
                    license: $("#license").val(),
                    license_pf: $("#license_pf").val(),
                    merchantShortName: $("#merchantShortName").val(),
                    industrId: $("#category_id").val(),
                    province: $("#province").val(),
                    city: $("#city").val(),
                    address: $("#address").val(),
                    tel: $("#tel").val(),
                    email: $("#email").val(),
                    idCode: $("#idCode").val(),
                    indentityPhoto_a: $("#indentityPhoto_a").val(),
                    indentityPhoto_b: $("#indentityPhoto_b").val(),
                    indentityPhoto_c: $("#indentityPhoto_c").val(),
                    indentityPhoto_a_pf: $("#indentityPhoto_a_pf").val(),
                    indentityPhoto_b_pf: $("#indentityPhoto_b_pf").val(),
                    indentityPhoto_c_pf: $("#indentityPhoto_c_pf").val(),
                    principal: $("#principal").val(),


                    // bankld:$("input[name='bankld']:checked").val(),
                    bankId: $("#bankId").val(),
                    accountCode: $("#accountCode").val(),
                    accountType: $("input[name='accountType']:checked").val(),
                    bankName: $("#bankName").val(),
                    tel2: $("#tel2").val(),
                    // remark: $("#remark").val(),
                    contactLine: $("#contactLine").val()

                },
                function (result) {

                    if (result.status == 2) {
                        window.location.href = "{{route('storeSuccess')}}";
                    }

                    tijiaotimes = 1;
                    layer.msg(result.message);
                    return;

                }, "json");

        }
        //表单提交=========end======


        $(function () {
            $('#tijiao').on('click', function () {
                addpost();
            });
        })


    </script>

@endsection
@endsection