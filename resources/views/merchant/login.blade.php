<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>登录</title>
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
<body data-type="login" class="theme-white">
<div class="am-g tpl-g">
    <!-- 风格切换 -->
    <div class="tpl-skiner">
        <div class="tpl-skiner-toggle am-icon-cog">
        </div>
        <div class="tpl-skiner-content">
            <div class="tpl-skiner-content-title">
                选择主题
            </div>
            <div class="tpl-skiner-content-bar">
                <span class="skiner-color skiner-white" data-color="theme-white"></span>
                <span class="skiner-color skiner-black" data-color="theme-black"></span>
            </div>
        </div>
    </div>
    <div class="tpl-login">
        <div class="tpl-login-content">
            <div class="tpl-login-log" align="center">
            <img src="{{url($logo->logo1)}}" style=" max-width: 159px;  height: 205px; margin: 0 auto; margin-bottom: 20px">
            </div>
            <form class="am-form tpl-form-line-form" role="form" method="POST" action="{{ url('merchant/login') }}">
                {{ csrf_field() }}
                <div class="am-form-group">
                    <input type="text" name="phone" value=""  required class="tpl-form-input" id="phone"
                           placeholder="请输入账号">

                </div>
                @if ($errors->has('phone'))
                    @if( $errors->first('phone')=="These credentials do not match our records.")
                        <span class="help-block">
                                        <strong>账号和密码不匹配,请输入正确的账号密码</strong>
                                            </span>
                    @else
                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                        @endif
                @endif
                <div class="am-form-group">
                    <input type="password" name="password" value=""   required class="tpl-form-input" id="password"
                           placeholder="请输入密码">

                </div>
                <div class="am-form-group tpl-login-remember-me">
                    <input id="remember-me" name="remember" checked="checked" type="checkbox">
                    <label for="remember-me">
                        记住密码
                    </label>
                    <a href="{{url('merchant/register')}}">注册账号</a>
                    <a href="{{route('setPassword')}}">忘记密码</a>
                </div>
                <div class="am-form-group">

                    <button type="submit"
                            class="am-btn am-btn-primary  am-btn-block tpl-btn-bg-color-success  tpl-login-btn">登录
                    </button>

                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>