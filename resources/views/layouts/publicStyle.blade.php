<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    @yield('meta')
    <title>@yield('title')</title>
    <meta name="keywords" content="@yield('keywords')">
    <meta name="description" content="@yield('description')">
    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->
    @yield('css')
    {{--<link href="{{asset('/css/bootstrap.min.css?v=3.3.5')}}" rel="stylesheet">--}}
    <link href="{{asset('/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('/css/style.css')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/total.css')}}" rel="stylesheet">
    <link href="{{asset('/adminui/css/btn.css')}}" rel="stylesheet">
    <script src={{asset('/js/jquery.min.js?v=2.1.4')}}></script>
    <script src="{{asset('/js/plugins/layer/layer.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/js/ajaxfileupload.js')}}" type="text/javascript"></script>
</head>
<body class="fixed-sidebar full-height-layout gray-bg  pace-done" style="">
@yield('content')
<!-- 全局js -->
@yield("restore")
@yield('js')
</body>

</html>
