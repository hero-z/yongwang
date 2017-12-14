<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>重置密码</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="apple-touch-icon-precomposed" href="assets/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="Amaze UI"/>
    <script src="{{asset('/amazeui/assets/js/echarts.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/amazeui.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/amazeui.datatables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/app.css')}}">
    <script src="{{asset('/amazeui/assets/js/jquery.min.js')}}"></script>
    <script src="{{asset('/amazeui/assets/js/theme.js')}}"></script>
</head>
<body class="theme-white">
<div class="am-g tpl-g">
    <!-- 风格切换 -->
    <div class="tpl-login">
        <div class="tpl-login-content">
            <div class="tpl-login-title">重置密码</div>
            <form class="am-form tpl-form-line-form" method="post" action="{{route('setPasswordPost')}}">
                {{csrf_field()}}
                <div class="am-form-group">
                    <input type="text" class="tpl-form-input" name="phone" required id="phone" placeholder="手机号码">
                    <input type="button" class="am-btn am-btn-default am-round" value="点击发送验证码"
                           onclick="sendCode(this)"/>
                </div>
                @if ($errors->has('phone'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                @endif
                <div class="am-form-group">
                    <input type="text" class="tpl-form-input" id="code" required name="code" placeholder="验证码">
                </div>
                @if (session('code'))
                    <span class="help-block">
                                        <strong>{{ session('code') }}</strong>
                                    </span>
                @endif
                <div class="am-form-group">
                    <input type="password" class="tpl-form-input" id="password" required name="password"
                           placeholder="请输入新密码">
                </div>
                @if ($errors->has('password'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                @endif
                <div class="am-form-group">
                    <input type="password" id="password-confirm" class="tpl-form-input" name="password_confirmation"
                           required placeholder="再次输入新密码">
                </div>
                @if ($errors->has('password_confirmation'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                @endif
                <div class="am-form-group tpl-login-remember-me">
                    <label for="remember-me">
                        <a href="{{url('merchant/login')}}">登陆账号</a>
                    </label>

                </div>

                <div class="am-form-group">
                    <button type="submit"
                            class="am-btn am-btn-primary  am-btn-block tpl-btn-bg-color-success  tpl-login-btn">确认重置
                    </button>

                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var clock = '';
    var nums = 10;
    var btn;
    function sendCode(thisBtn) {
        var phone = $('#phone').val();
        var reg = /^0?1[3|4|5|7|8][0-9]\d{8}$/;
        if (reg.test(phone)) {
            $.post("{{url('api/Sms/send')}}", {phone: phone},
                function (data) {
                    if (!data.status_code) {
                        alert(data.message);
                        return false;
                    } else {
                        btn = thisBtn;
                        btn.disabled = true; //将按钮置为不可点击
                        btn.value = nums + '秒后可重新获取';
                        clock = setInterval(doLoop, 1000); //一秒执行一次
                    }
                    alert(data.message);
                }, "json");

        } else {
            alert("号码有误~");
            return false;
        }

    }
    function doLoop() {
        nums--;
        if (nums > 0) {
            btn.value = nums + '秒后可重新获取';
        } else {
            clearInterval(clock); //清除js定时器
            btn.disabled = false;
            btn.value = '点击发送验证码';
            nums = 60; //重置时间
        }
    }
</script>
<!-- 全局js -->
<script src="{{asset('/amazeui/assets/js/amazeui.min.js')}}"></script>
<script src="{{asset('/amazeui/assets/js/amazeui.datatables.min.js')}}"></script>
<script src="{{asset('/amazeui/assets/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('/amazeui/assets/js/app.js')}}"></script>
</body>
</html>