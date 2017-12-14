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

    <link rel="shortcut icon" href="favicon.ico"> <link href="{{asset('/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('/css/style.css')}}" rel="stylesheet">
    <script src="{{asset('/js/jquery.min.js?v=2.1.4')}}" type="text/javascript"></script>
    @yield('css')
</head>

<body class="fixed-sidebar full-height-layout gray-bg" >
@yield('content')

<!-- 全局js -->
<script src="{{asset('/js/bootstrap.min.js?v=3.3.6')}}" type="text/javascript"></script>
<script src="{{asset('/js/plugins/metisMenu/jquery.metisMenu.js')}}" type="text/javascript"></script>
<script src="{{asset('/js/plugins/slimscroll/jquery.slimscroll.min.js')}}" type="text/javascript"></script>
<script src="{{asset('/js/plugins/layer/layer.min.js')}}" type="text/javascript"></script>
<!-- 自定义js -->
<script src="{{asset('js/hAdmin.js?v=4.1.0')}}" type="text/javascript"></script>
<script type="text/javascript" src="{{asset('js/index.js')}}" type="text/javascript"></script>

@yield('js')
</body>

</html>
