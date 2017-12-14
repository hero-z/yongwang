@extends('layouts.antui')
@section('content')
    <div id="nowamagic"></div>
    <div class="demo-content" id="remove">
        <input type="hidden" id="user_id" value="<?php echo $_GET['user_id']?>">
        <input type="hidden" id="u_id" value="<?php echo $_GET['u_id']?>">
        <input type="hidden" id="app_auth_token" value="<?php echo $_GET['app_auth_token']?>">
        <div class="am-list am-list-5lb form">
            <div class="am-list-header">请填写你的联系信息</div>
            <div class="am-list-body">
                <div class="am-list-item am-input-autoclear">
                    <div class="am-list-label">店铺名称</div>
                    <div class="am-list-control">
                        <input name="username" id="auth_shop_name" required="required" placeholder="请输入你的店铺名称"
                               autocomplete="off" type="text">
                    </div>
                    <div class="am-list-clear"><i class="am-icon-clear am-icon" style="visibility: hidden;"></i></div>
                </div>
                <div class="am-list-item am-input-autoclear">
                    <div class="am-list-label">联系方式</div>
                    <div class="am-list-control">
                        <input type="text" id="auth_phone" required="required" placeholder="请输入你的联系方式">
                    </div>
                    <div class="am-list-clear"><i class="am-icon-clear am-icon" style="visibility: hidden;"></i></div>
                </div>
            </div>
        </div>
    </div>
    <button type="button" class="am-button blue" id="remove1" onclick="sub()">确认信息</button>
@endsection
@section('js')
    <script>
        function sub() {
            var auth_shop_name = $("#auth_shop_name").val();
            var auth_phone = $("#auth_phone").val();
            if (auth_shop_name && auth_phone) {
                $.post("{{route('userinfo')}}", {
                    user_id: $("#user_id").val(),
                    u_id: $("#u_id").val(),
                    _token: "{{csrf_token()}}",
                    auth_shop_name: $("#auth_shop_name").val(),
                    app_auth_token: $("#app_auth_token").val(),
                    auth_phone: $("#auth_phone").val()
                }, function (result) {
                    if (result.code == 200) {
                       var url= "{{url('/auto/alipayopen/store/create?app_auth_token=')}}"+result.app_auth_token+'&promoter_id='+result.u_id;
                       $("#remove").remove();
                       $("#remove1").remove();
                       $("#nowamagic").append('<div class="am-message result"> <i class="am-icon result wait"></i> <div class="am-message-main">等待</div> <div class="am-message-sub">已提交成功，等待开通当面付,<a id="url" href="">点击这里开通口碑</a></div></div>');
                        $("#url").attr("href",url);
                    } else {
                    }
                }, "json")
            } else {
                alert('请填写完整');
                return false;
            }
        }
    </script>
@endsection

