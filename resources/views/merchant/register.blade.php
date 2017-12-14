<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>注册新用户</title>
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
            <div class="tpl-login-title">注册用户</div>
            <span class="tpl-login-content-info">
                  创建一个新的用户
              </span>
            <form class="am-form tpl-form-line-form" method="post" action="{{url('/merchant/register')}}">
                {{csrf_field()}}
                <div class="am-form-group">
                    <input type="text" class="tpl-form-input" name="phone" required id="phone" placeholder="手机号码">
                </div>
                @if ($errors->has('phone'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                @endif
                <div class="am-form-group">
                    <input type="text" class="tpl-form-input" id="name" required name="name" placeholder="店铺名称">
                </div>
                @if ($errors->has('name'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                @endif
                <div class="am-form-group">
                    <input type="password" class="tpl-form-input" id="password" required name="password"
                           placeholder="请输入密码">
                </div>
                @if ($errors->has('password'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                @endif
                <div class="am-form-group">
                    <input type="password" id="password-confirm" class="tpl-form-input" name="password_confirmation"
                           required placeholder="再次输入密码">
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
                            class="am-btn am-btn-primary  am-btn-block tpl-btn-bg-color-success  tpl-login-btn">提交
                    </button>

                </div>
            </form>
        </div>
    </div>
</div>
<!-- 全局js -->
<script src="{{asset('/amazeui/assets/js/amazeui.min.js')}}"></script>
<script src="{{asset('/amazeui/assets/js/amazeui.datatables.min.js')}}"></script>
<script src="{{asset('/amazeui/assets/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('/amazeui/assets/js/app.js')}}"></script>
</body>
</html>