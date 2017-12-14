@extends('layouts.publicStyle')
@section('title','绑定银行账号')
@section('css')
@endsection
@section('content')

    <script src="{{asset('/js/check.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        /*document.body.onpaste=function(){return false}*/
    </script>
    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>绑定结算银行账号</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            {{--<input type="hidden" id="product_type" value="{{$product_type}}">--}}
                            <input type="hidden" id="id_type" value="{{$id_type}}">
                            <input type="hidden" id="id_no" value="{{$id_no}}">
                            <input type="hidden" id="merchant_name" value="{{$merchant_name}}">
                            <input type="hidden" id="merchant_type_code" value="{{$merchant_type_code}}">
                            <input type="hidden" id="licence_no" value="{{$licence_no}}">
                            <input type="hidden" id="category_id" value="{{$category_id}}">
                            <input type="hidden" id="alias_name" value="{{$alias_name}}">
                            <input type="hidden" id="address" value="{{$address}}">
                            <input type="hidden" id="contact_name" value="{{$contact_name}}">
                            <input type="hidden" id="contact_phone" value="{{$contact_phone}}">
                            <input type="hidden" id="service_phone" value="{{$service_phone}}">
                            <input type="hidden" id="user_id" value="{{$user_id}}">
                            <input type="hidden" id="province_code" value="{{$province_code}}">
                            <input type="hidden" id="city_code" value="{{$city_code}}">
                            <input type="hidden" id="district_code" value="{{$district_code}}">
                            <input type="hidden" id="district" value="{{$district}}">
                            <input type="hidden" id="code_number" value="{{$code_number}}">
                            <input type="hidden" id="store_id" value="{{$store_id}}">
                            <input type="hidden" id="code_from" value="{{$code_from}}">
                            <div class="form-group">
                                <label>请输入银行卡卡号</label>
                                <input required placeholder="银行卡卡号" class="form-control" value="" name="account_no"
                                       id="account_no"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>请重新输入银行卡卡号</label>
                                <input required placeholder="重新输入银行卡卡号" onpaste="return false"  class="form-control" value="" id="card_repeat"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>账户开户行:</label>
                                <div class="row">
                                    <select class="form-control m-b" value=""  name="account_info" id="account_info">
                                        @foreach($banks as $v)
                                            <option value="{{$v->bank_no.'**'.$v->bank_name}}">{{$v->bank_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            {{--<div class="form-group">
                                <label>请输入账户开户行号</label>
                                <input required placeholder="账户开户行号" class="form-control" value="" name="account_opbank_no"
                                       id="account_opbank_no"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>--}}
                            <div class="form-group">
                                <label>请输入开户户名</label>
                                <input required="required" placeholder="开户户名" value="" class="form-control"
                                       name="account_name" id="account_name"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            {{--<div class="form-group">
                                <label>请输入开户行名称</label>
                                <input required="required" placeholder="开户行名称" value="" class="form-control"
                                       name="account_opbank" id="account_opbank"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>--}}
                            <div class="form-group">
                                <label>该银行卡是否为对公账户</label>
                                <div class="radio">
                                    <label>
                                        <input onclick="Switch()" checked="checked" value="02" id=""
                                               name="acct_type" type="radio">否</label>
                                    <label>
                                        <input onclick="Switch()" value="01" id=""
                                               name="acct_type" type="radio">是</label>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>支付类型</label>
                                <div class="radio">
                                    <label>
                                        <input onclick="Switch()" checked="checked" value="1" id=""
                                               name="payment_type" type="radio">线上</label>
                                    <label>
                                        <input onclick="Switch()" value="2" id=""
                                               name="payment_type" type="radio">线下</label>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            {{--<div class="form-group">
                                <label>支付类型</label>
                                <div class="radio">
                                    <label>
                                        <input onclick="Switch()" checked="checked" value="@if($product_type=='003') 23 @else 25 @endif" id=""
                                               name="payment_type" type="radio">线上</label>
                                    <label>
                                        <input onclick="Switch()" value="@if($product_type=='003') 24 @else 26 @endif" id=""
                                               name="payment_type" type="radio">线下</label>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>--}}
                        </form>
                    </div>
                </div>
                <a href="javascript:void(0)" onclick="addpost()">
                    <button style="width: 100%;height: 40px;font-size: 18px;" type="button" class="btn btn-primary">
                        下一步上传资料
                    </button>
                </a>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')
    <script>
        function addpost() {
            if (!$("#account_no").val()) {
                layer.msg('请输入银行卡号');
                $("#account_no").focus();
            }else if (!CheckBankNo($("#account_no"))) {
                layer.msg('请检查银行卡号');
                $("#account_no").focus();
                return false;
            }else if ($("#account_no").val() != $("#card_repeat").val()) {
                layer.msg('银行卡号两次输如不一致！');
                $("#card_repeat").focus();
                return false;
            }/*else if (!$("#account_opbank_no").val()) {
                layer.msg('请输入账户开户行号！');
                $("#account_opbank_no").focus();
                return false;
            }*/else if (!$("#account_name").val()) {
                layer.msg('请输入账户开户户名！');
                $("#account_name").focus();
                return false;
            }/*else if (!$("#account_opbank").val()) {
                layer.msg('请输入账户开户行名！');
                $("#account_opbank").focus();
                return false;
            }*/else{
                $.post("{{route("merchantregister")}}",
                        {
                            _token: '{{csrf_token()}}',
//                            product_type: $("#product_type").val(),
                            id_type: $("#id_type").val(),
                            id_no: $("#id_no").val(),
                            merchant_name: $("#merchant_name").val(),
                            merchant_type_code: $("#merchant_type_code").val(),
                            licence_no: $("#licence_no").val(),
                            category_id: $("#category_id").val(),
                            alias_name: $("#alias_name").val(),
                            address: $("#address").val(),
                            contact_name: $("#contact_name").val(),
                            contact_phone: $("#contact_phone").val(),
                            service_phone: $("#service_phone").val(),
                            user_id: $("#user_id").val(),
                            province_code: $("#province_code").val(),
                            city_code: $("#city_code").val(),
                            district_code: $("#district_code").val(),
                            district: $("#district").val(),
                            store_id: $("#store_id").val(),
                            code_number: $("#code_number").val(),
                            account_no: $("#account_no").val(),
//                            account_opbank_no: $("#account_opbank_no").val(),
                            account_info: $("#account_info").val(),
                            account_name: $("#account_name").val(),
//                            account_opbank: $("#account_opbank").val(),
                            acct_type: $("input[name='acct_type']:checked").val(),
                            payment_type: $("input[name='payment_type']:checked").val()
                        },
                        function (result) {
                            if (result.code==1) {
                                {{--window.location.href = "{{route('PingAnSuccess')}}";--}}
                                store_id=result.store_id;
                                code_number=result.code_number;
                                window.location.href = "{{url('admin/webank/uploadfile?store_id=')}}" + store_id+"&code_number="+code_number+"&code_from="+$("#code_from").val() ;
                            } else {
                                layer.msg(result.msg);
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
    </script>

@endsection
@endsection