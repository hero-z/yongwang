<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <script src="{{asset('/js/jquery.min.js?v=2.1.4')}}" type="text/javascript"></script>
    <link href="{{asset('/css/weixinpay/wepayui.css')}}" rel="stylesheet">
    <link href="{{asset('/css/weixinpay/wxpay.css')}}" rel="stylesheet">
    <link href="{{asset('/css/weixinpay/index.css')}}" rel="stylesheet">
    <link href="{{asset('/zeroModal/zeroModal.css')}}" rel="stylesheet">
    <title>固定金额付款码</title>
</head>
<style>
    /*!
 * WePayUI v0.1.1 (https://github.com/wepayui/wepayui)
 * Copyright 2017 Tencent, Inc.
 * Licensed under the MIT license
 * Component : [weui-dialog]
 */
    html {
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%
    }

    body {
        line-height: 1.6;
        font-family: -apple-system-font, Helvetica Neue, sans-serif
    }

    * {
        margin: 0;
        padding: 0
    }

    a img {
        border: 0
    }

    a {
        text-decoration: none;
        -webkit-tap-highlight-color: transparent
    }

    .weui-mask {
        background: rgba(0, 0, 0, .6)
    }

    .weui-mask, .weui-mask_transparent {
        position: fixed;
        z-index: 1;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0
    }

    .weui-dialog {
        position: fixed;
        z-index: 2;
        width: 80%;
        max-width: 300px;
        top: 50%;
        left: 50%;
        -webkit-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        background-color: #fff;
        text-align: center;
        border-radius: 3px;
        overflow: hidden
    }

    .weui-dialog__hd {
        padding: 1.3em 1.6em .5em
    }

    .weui-dialog__title {
        font-weight: 400;
        font-size: 18px
    }

    .weui-dialog__bd {
        padding: 0 1.6em .8em;
        min-height: 40px;
        font-size: 15px;
        line-height: 1.3;
        word-wrap: break-word;
        word-break: break-all;
        color: #999
    }

    .weui-dialog__bd:first-child {
        padding: 2.7em 20px 1.7em;
        color: #353535
    }

    .weui-dialog__ft {
        position: relative;
        line-height: 48px;
        font-size: 18px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex
    }

    .weui-dialog__ft:after {
        content: " ";
        position: absolute;
        left: 0;
        top: 0;
        right: 0;
        height: 1px;
        border-top: 1px solid #d5d5d6;
        color: #d5d5d6;
        -webkit-transform-origin: 0 0;
        transform-origin: 0 0;
        -webkit-transform: scaleY(.5);
        transform: scaleY(.5)
    }

    .weui-dialog__btn {
        display: block;
        -webkit-box-flex: 1;
        -ms-flex: 1;
        flex: 1;
        color: #3cc51f;
        text-decoration: none;
        -webkit-tap-highlight-color: transparent;
        position: relative
    }

    .weui-dialog__btn:active {
        background-color: #eee
    }

    .weui-dialog__btn:after {
        content: " ";
        position: absolute;
        left: 0;
        top: 0;
        width: 1px;
        bottom: 0;
        border-left: 1px solid #d5d5d6;
        color: #d5d5d6;
        -webkit-transform-origin: 0 0;
        transform-origin: 0 0;
        -webkit-transform: scaleX(.5);
        transform: scaleX(.5)
    }

    .weui-dialog__btn:first-child:after {
        display: none
    }

    .weui-dialog__btn_default {
        color: #353535
    }

    .weui-dialog__btn_primary {
        color: #0bb20c
    }

    .weui-skin_android .weui-dialog {
        text-align: left;
        box-shadow: 0 6px 30px 0 rgba(0, 0, 0, .1)
    }

    .weui-skin_android .weui-dialog__title {
        font-size: 21px
    }

    .weui-skin_android .weui-dialog__hd {
        text-align: left
    }

    .weui-skin_android .weui-dialog__bd {
        color: #999;
        padding: .25em 1.6em 2em;
        font-size: 17px;
        text-align: left
    }

    .weui-skin_android .weui-dialog__bd:first-child {
        padding: 1.6em 1.6em 2em;
        color: #353535
    }

    .weui-skin_android .weui-dialog__ft {
        display: block;
        text-align: right;
        line-height: 42px;
        font-size: 16px;
        padding: 0 1.6em .7em
    }

    .weui-skin_android .weui-dialog__ft:after {
        display: none
    }

    .weui-skin_android .weui-dialog__btn {
        display: inline-block;
        vertical-align: top;
        padding: 0 .8em
    }

    .weui-skin_android .weui-dialog__btn:after {
        display: none
    }

    .weui-skin_android .weui-dialog__btn:active, .weui-skin_android .weui-dialog__btn:visited {
        background-color: rgba(0, 0, 0, .06)
    }

    .weui-skin_android .weui-dialog__btn:last-child {
        margin-right: -.8em
    }

    .weui-skin_android .weui-dialog__btn_default {
        color: gray
    }

    .weui-dialog__bd img{
        width: 20%;
    }

    @media screen and (min-width: 1024px) {
        .weui-dialog {
            width: 35%
        }
    }
</style>
<!--
	通用说明：
	1.模块的隐藏添加class:hide;
	2.body标签默认绑定ontouchstart事件，激活所有按钮的:active效果
-->
<body ontouchstart>
<div class="weui-wepay-pay-select">
    <div class="weui-wepay-pay">
        <div class="weui-wepay-pay__bd">
            <div class="weui-wepay-pay__inner">
                <div class="weui-wepay-pay__inputs"><strong class="weui-wepay-pay__strong">￥</strong>
                    <input type="text" id="total_amount" class="weui-wepay-pay__input" value="" placeholder="请输入金额">
                </div>
            </div>

        </div>
    </div>
    <div class="weui-wepay-pay-select__bd">
        <ul class="weui-wepay-pay-select__element">
            <a href="javascript:;" class="weui-btn weui-btn_mini weui-btn_primary">按钮</a>
            <a href="javascript:;" class="weui-btn weui-btn_primary">页面主操作 Normal</a>
            <li class="weui-wepay-pay-select__li">
                <a href="javascript:;" class="weui-wepay-pay-select__item">1元</a>
            </li>
            <li class="weui-wepay-pay-select__li">
                <a href="javascript:;" class="weui-wepay-pay-select__item">10元</a>
            </li>
            <li class="weui-wepay-pay-select__li">
                <a href="javascript:;" class="weui-wepay-pay-select__item">100元</a>
            </li>
            <li class="weui-wepay-pay-select__li">
                <a href="javascript:;" class="weui-wepay-pay-select__item">200元</a>
            </li>
            <li class="weui-wepay-pay-select__li">
                <a href="javascript:;" class="weui-wepay-pay-select__item">300元</a>
            </li>
            <li class="weui-wepay-pay-select__li">
                <a href="javascript:;" class="weui-wepay-pay-select__item">500元</a>
            </li>
        </ul>
    </div>
    <div class="js_dialog" id="iosDialog2" style="opacity: 1;display:none">
        <div class="weui-mask"></div>
        <div class="weui-dialog">
            <div class="weui-dialog__bd" id="weui-dialog__bd">

            </div>
            <div class="weui-dialog__ft">
                <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary" onclick="reset()">重新生成</a>
            </div>
        </div>
    </div>

    <div class="weui-wepay-pay-select__ft">
        <div class="weui-wepay-pay__btn">
            <a href="javascript:;" class="weui-btn weui-btn_primary" onclick="fun('{{$_GET['type']}}')">立即生成付款码支付</a>
        </div>
    </div>
</div>
<div class="weui-wepay-logos weui-wepay-logos_ft">
    @if($_GET['type']=="u")
        <img src="{{url('/img/upay_logo.png')}}" alt="" height="32">
    @endif
</div>
<script type="text/javascript" src="{{asset('/css/weixinpay/zepto.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/css/weixinpay/index.js')}}"></script>
<script type="text/javascript" src="{{asset('/js/jquery.qrcode.min.js')}}"></script>

<script>
    function fun(type) {
        if (type == "u") {
            $url = "{{route('UnionPayOrder')}}"
        }

        $.post($url, {
                _token: "{{csrf_token()}}",
                store_id: "{{$_GET['store_id']}}",
                total_amount: $("#total_amount").val()
            },
            function (data) {
                if (data.status == 1) {
                    $("#iosDialog2").css('display', 'block');
                    $('#weui-dialog__bd').qrcode(data.code_url);
                } else {
                    alert(data.msg);
                }

            }, "json");
    }

    function reset() {
        $('#weui-dialog__bd').html('');
        $("#iosDialog2").css('display', 'none');
    }
</script>
</body>
</html>
