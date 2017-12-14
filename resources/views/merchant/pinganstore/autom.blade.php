@extends('layouts.publicStyle')
@section('title','绑定银行账号')
@section('css')
@endsection
@section('content')
    <script src="{{asset('/js/check.js')}}" type="text/javascript"></script>
    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>绑定结算银行账号（非法人结算卡的上一步的入账授权函须上传）</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            <input type="hidden" id="external_id" value="<?php echo $_GET['external_id']?>">

                            <div class="form-group">
                                <label>请输入银行卡卡号（请仔细核对，提交错误会影响款项结算）</label>
                                <input required placeholder="绑卡后需进行验证金额鉴权,请准确输入" class="form-control" value="" name="bank_card_no"
                                       id="bank_card_no"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>请再次输入银行卡卡号</label>
                                <input required placeholder="重新输入银行卡卡号" onpaste="return false"  class="form-control" value="" id="card_repeat"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>请输入开户人姓名</label>
                                <input required="required" placeholder="银行卡的开户人姓名" value="" class="form-control"
                                       name="card_holder" id="card_holder"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>请输入银行预留手机号</label>
                                <input required="required" placeholder="绑卡后需进行短信激活,请准确输入" value="" class="form-control"
                                       name="card_phone" id="card_phone"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>银行卡类型</label>
                                <div class="col-sm-4">
                                    <select class="form-control m-b " name="bankType" id="bankType">
                                        @foreach($banks as $v)
                                            <option value='{{$v->bank_code.'**'.$v->bank_no}}'>{{$v->bank_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>请选择开户行地址</label>
                                <div class="col-sm-4">
                                    <select class="form-control m-b " name="province" id="province">
                                        <option value=''>请选择省份</option>
                                        @foreach($province as $k=>$v)
                                            <option value='{{$k}}'>{{$v}}</option>
                                        @endforeach
                                    </select>
                                    <select id="city" class="form-control m-b" name="city" >
                                        <option id='' value=''>请选择城市</option>
                                    </select>
                                    <select id="county" class="form-control m-b" name="county" >
                                        <option id='' value=''>请选择县区(或市)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>选择开户行：</label>
                                <input type="text" id="bankkeyword" value="" style="display: inline-block;width: 130px;" class="form-control" placeholder='开户银行关键字'><button class="btn btn-success" type="button" onclick="getOpenBank()" style="width: 50px;height: 30px;font-size: 15px;">检索</button>
                                <div class="col-sm-10">
                                    <select class="form-control m-b" name="bankName" id="bankName">
                                        <option value=''>请选择开户行</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            {{--<div class="form-group">
                                <label>该银行卡是否为对公账户</label>
                                <div class="radio">
                                    <label>
                                        <input onclick="Switch()" checked="checked" value="0" id="is_public_account"
                                               name="is_public_account" type="radio">否</label>
                                    <label>
                                        <input onclick="Switch()" value="1" id="is_public_account"
                                               name="is_public_account" type="radio">是</label>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>--}}
                        </form>
                    </div>
                </div>
                <a href="javascript:void(0)" onclick="addpost()">
                    <button style="width: 100%;height: 40px;font-size: 18px;" type="button" class="btn btn-primary">
                        确认信息提交资料
                    </button>
                </a>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')
    <script>
        function addpost() {
            if(!CheckBankNo($("#bank_card_no"))){
                layer.msg('请检查银行卡号');
                $("#bank_card_no").focus();
            }else if($("#bank_card_no").val() != $("#card_repeat").val()){
                layer.msg('银行卡号两次输如不一致！');
                $("#card_repeat").focus();
            }else if($("#card_holder").val()==''){
                layer.msg('请输入银行卡的开户人姓名!');
                $("#card_holder").focus();
            }else if($("#card_phone").val()==''){
                layer.msg('请输入银行卡预留手机号!');
                $("#card_phone").focus();
            }else if (!IsTel($("#card_phone").val().trim())) {
                layer.msg('手机号码不正确');
                $("#card_phone").focus();
            }else if($('#bankType').val()==''){
                layer.msg('请先选择银行卡类型!');
                $('#bankType').focus();
            }else if($('#bankName').val()==''){
                layer.msg('请先选择开户行!');
                $('#bankName').focus();
            }else{
                var bankinfo=$('#bankType').val().split("**");
                $.post("{{route("witnessAutomPost")}}",
                    {
                        _token: '{{csrf_token()}}',
                        external_id: $("#external_id").val(),
                        code_number: $("#code_number").val(),
                        bank_card_no: $("#bank_card_no").val(),
                        card_holder: $("#card_holder").val().trim(),
                        card_phone: $("#card_phone").val().trim(),
                        s_bank_code: bankinfo[1],
                        bank_code: $('#bankName').val(),
                        bank_name: $("#bankName").find("option:selected").text()
//                        is_public_account: 0,
//                    is_public_account: $('input:radio:checked').val(),
//                        open_bank: $("#open_bank").val(),
                    },
                    function (result) {
                        if (result.success) {
                            layer.msg(result.error_message);
                            window.location.href = "{{url('/merchant/PingAnQr')}}";
                            {{--window.location.href = "{{url('admin/pingan/autoFile?external_id=')}}" + $("#external_id").val() + '&code_number=' + $("#code_number").val();--}}
                        } else {
                            layer.msg(result.error_message);
                        }
                    }, "json")
            }


        }
        function Switch() {
            if ($('input:radio:checked').val() == 1) {
                $("#ob").css("display", "block");
            } else {
                $("#ob").css("display", "none");
            }
        }
        $('#province').change(function () {
            $('#city').find('option').next().remove();
            $('#county').find('option').next().remove();
            id=$(this).val();
            if(id){
                $.post("{{url('admin/pingan/witness/getcity')}}", {_token: "{{csrf_token()}}",
                        id:id,
                        type:1
                    },
                    function (data) {
                        var citys=[];
                        if (data.success) {
                            citys=data.data;
                            for(city in citys){
                                var option='<option  value='+ city+ '>' +citys[city]+'</option>';
                                $('#city').append(option);
                            }
                        } else {
                            layer.msg(data.msg,{time:2000});
                        }
                    }, 'json');
            }
        });
        $('#city').change(function () {
            $('#county').find('option').next().remove();
            id=$(this).val();
            $.post("{{url('admin/pingan/witness/getcity')}}", {_token: "{{csrf_token()}}",
                    id:id,
                    type:2
                },
                function (data) {
                    var countys=[];
                    if (data.success) {
                        countys=data.data;
                        for(county in countys){
                            var option='<option  value='+ countys[county]+ '>' +county+'</option>';
                            $('#county').append(option);
                        }
                    } else {
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');

        });
        function getOpenBank() {
            if($('#bankType').val()==''){
                layer.msg('请先选择银行卡类型!');
                $('#bankType').focus();
            }else if($('#county').val()==''){
                layer.msg('请先选择银行所在县区或者市区以缩小检索范围!');
                $('#county').focus();
            }else{
                $('#bankName').find('option').next().remove();
                var bankinfo=$('#bankType').val().split("**");
                $.post("{{url('admin/pingan/witness/getopenbank')}}", {_token: "{{csrf_token()}}",
                        banktype:bankinfo[0],
                        county_code:$('#county').val(),
                        keyword:$('#bankkeyword').val()
                    },
                    function (data) {
                        var banks=[];
                        if (data.success) {
                            banks=data.data;
                            for(bank in banks){
                                var option='<option  value='+ bank+ '>' +banks[bank]+'</option>';
                                $('#bankName').append(option);
                            }
                            layer.msg('检索成功!限制50条,如果没有找到您的开户行,请输入准确关键字重新检索.如XXX路');
                            $('#bankName').focus();
                        } else {
                            layer.msg(data.msg,{time:2000});
                        }
                    }, 'json');
            }
        }
    </script>

@endsection
@endsection