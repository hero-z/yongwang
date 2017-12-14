<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    <meta name="description" content="@yield('keywords')">
    <meta name="keywords" content="@yield('description')">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="apple-touch-icon-precomposed" href="assets/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="Amaze UI"/>
    <script src="{{asset('/amazeui/assets/js/echarts.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/amazeui.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/amazeui.datatables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/app.css')}}">
    <script src="/jquery/jquery-1.9.1.min.js"></script>
    <script src="/jquery/jquery.form.js"></script>
    <script src="{{asset('/amazeui/assets/js/theme.js')}}"></script>
    <script src="/layer/layer.js"></script>
    @yield('css')
</head>
<body data-type="widgets" class="theme-white">
<div class="am-g tpl-g">
    <!-- 头部 -->
    <!-- 风格切换 -->
    <!-- 侧边导航栏 -->

            @yield('content')
    <!-- 全局js -->
    <script src="{{asset('/amazeui/assets/js/amazeui.min.js')}}"></script>
    <script src="{{asset('/amazeui/assets/js/amazeui.datatables.min.js')}}"></script>
    <script src="{{asset('/amazeui/assets/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('/amazeui/assets/js/app.js')}}"></script>
@yield('js')
</body>
</html>
