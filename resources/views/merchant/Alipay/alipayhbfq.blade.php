<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>花呗分期</title>
    <style>
        html, body {
            width: 100%;
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        * {
            box-sizing: border-box;
        }

        a {
            list-style-type: none;
        }
        ul, li {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .main_box{
            padding:0 0.4rem;
            background-color: #fff;
        }

        .center_style {
            width: 100%;
            height: 1.2rem;
            line-height: 1.2rem;
            color: #333;
            font-size: 0.4rem;
            text-align: left;

        }
        .center_box {
            border-bottom: 1px solid #e7eaec;
        }
        .input_select {
            background: #fff;
            height: 100%;
            float: right;
            text-align: right;
            outline: medium;
            font-size: 0.38rem;
            color: #333333;
            width:60%;
            border:none;
        }
        .input {
            height: 100%;
            float: right;
            border: none;
            width: 70%;
            text-align: right;
            outline: medium;
            font-size: 0.38rem;
            color: #333333;
        }

        ::-webkit-input-placeholder {
            color: #999999;
            font-size: 0.4rem;
        }

        .center_style ul {
            width: 100%;
            background: #fff;
            position: absolute;
            display: none;
            left: 0;
            padding:0 25px;
            border-bottom: 1px solid #ededed;
        }

        .center_style ul li {
            height: 1.2rem;
            line-height: 1.2rem;
            text-align: center;
            border-top: 1px solid #ededed;
        }

        .center_style ul li:first-child {
            border-top: none;
        }

        .center_style ul li a {
            display: block;
            height: 1.2rem;
            color: #807a62;
            text-decoration: none
        }

        .center_style ul li a:hover {
            background: #ededed;
            color: #333;
        }

        .refund {
            width: 100%;
            height: 2.5rem;
            color: #333;
            padding: 0.2rem 0;
            font-size: 0.4rem;
            background-color: #fff;
        }

        .refund_list {
            text-align: center;
            display: flex;
            justify-content: center;
            line-height: 1.5;
            margin-top:0.3rem;
        }

        .refund_list > li {
            margin-right: 0.5rem;
            color: #333;
            font-size: 0.4rem;
        }


        .btn {
            display: inline-block;
            width: 90%;
            height: 1rem;
            line-height: 1rem;
            border-radius: 4px;
            text-align: center;
            font-size: 0.45rem;
            color: #f5f5f5;
            margin:20% 0 0 5%;
            background-color: #00aaef;

        }

        .btn1 {
            display: inline-block;
            width: 90%;
            height: 1rem;
            line-height: 1rem;
            border-radius: 4px;
            text-align: center;
            font-size: 0.45rem;
            color: #f5f5f5;
            margin:20% 0 0 5%;
            background-color: #00aaef;

        }


        /*弹出层*/
        .mark {
            width: 105%;
            height: 105%;
            top: 0;
            background-color: white;
            position: absolute;
            z-index: 99;
            display: none;
            opacity: 1;
        }

        .er_code {
            width: 70%;
            height: 50%;
            background-color: #fff;
            border-radius: 5px;
            position: absolute;
            left: 15%;
            top: 25%;
            z-index: 100;
            display: none;

        }


    </style>

</head>
<body>


<div class="main_box">
    <div class="main_center">
        <div class="center_box" style="text-align: center;height: 4rem;padding-top: 0.5rem">
            <img src="{{url('/hbfq/images/logo.jpg')}}" alt="" style="width: 3rem;height: 3rem;">
        </div>
        <div class="center_box center_style" style="padding-right:25px;">
            选择支付类型：
            <input class="input_select1 input_select" type="text" placeholder="请选择支付类型"/>
            <ul class="ul_list1">
                @if($store_o)
                    <li><a href="#">支付宝当面付</a></li>
                @endif
                @if($store_s)
                    <li><a href="#">支付宝口碑</a></li>
                @endif

            </ul>
        </div>
        <div class="center_box center_style">
            金额：
            <input class="input money" type="number" placeholder="请输入金额">

        </div>
        <div class="center_box center_style" style="padding-right:25px;">
            分期数：
            <input class="input_select input_select2" type="input" placeholder="请选择分期数"/>
            <ul class="ul_list2">
                <li><a href="#">3期</a></li>
                <li><a href="#">6期</a></li>
                <li><a href="#">12期</a></li>
            </ul>
        </div>
        <div class="center_box center_style" style="padding-right:25px;">
            手续费承担方：
            <input class="input_select input_select3" type="text" placeholder="请选择手续费承担方"/>
            <ul class="ul_list3">
                <li><a href="#">商户</a></li>
                <li><a href="#">顾客</a></li>
            </ul>
        </div>
        <div class="refund">
            <div class="refund_title">还款测算</div>
            <ul class="refund_list">
                <li>
                    <div>每期还款(元)</div>
                    <div class="terminally">--</div>
                </li>
                <li>
                    <div>X</div>
                    <div></div>
                </li>
                <li>
                    <div>期数</div>
                    <div class="periods">--</div>
                </li>
                <li>
                    <div>=</div>
                    <div></div>
                </li>
                <li>
                    <div>总价(元)</div>
                    <div id="allMoney">--</div>
                </li>
            </ul>
        </div>
    </div>


    <span class="btn">确定</span>

    <div class="mark"></div>

    <div class="er_code">
        <div class="img_code" style="width: 100%;">
            <div id="divOne"></div>
            <img id='imgOne'  style='border:1px solid #00aaef; width: 100%;height: 100%' />
        </div>
        <span class="btn1" onclick="reset()">从新生成二维码</span>
    </div>
</div>


<script src="https://cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript" src="{{asset('/js/jquery.qrcode.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/hbfq/js/rem.js')}}"></script>
<script>

    $(function () {

        $('.btn').click(function () {
            var type = $(".input_select1").val();//类型
            var total_amount = $(".money").val();//金额
            var hb_fq_num = $(".input_select2").val();//分期数
            var hb_fq_seller_percent = $(".input_select3").val();//谁付手续费
            $.post("{{route('alipayhbfqPost')}}",
                {
                    _token: "{{csrf_token()}}",
                    type: type, total_amount: total_amount,
                    hb_fq_num: hb_fq_num, hb_fq_seller_percent: hb_fq_seller_percent
                },
                function (data) {
                    if (data.status == 0) {
                        aler(data.msg);
                    } else {
                        $('.mark').show();
                        $('.er_code').show();
                        var qrcode= $('#divOne').qrcode(data.data.code_url).hide();
                        var canvas=qrcode.find('canvas').get(0);
                        $('#imgOne').attr('src',canvas.toDataURL('image/jpg'))


                    }
                }, "json");


        });

        $(document).bind("click", function () {
            $('.ul_list1').slideUp();
            $('.ul_list2').slideUp();
            $('.ul_list3').slideUp();
        });

        $('.ul_list1').click(function (e) {
            stopPropagation(e);
        });
        $('.ul_list2').click(function (e) {
            stopPropagation(e);
        });
        $('.ul_list3').click(function (e) {
            stopPropagation(e);
        });
        $('.input_select1').click(function (event) {
            var ul = $(".ul_list1");
            if (ul.css("display") == "none") {
                event.stopImmediatePropagation();
                ul.slideDown("fast");
            } else {
                ul.slideUp("fast");
            }
            $(".ul_list2").hide();
            $(".ul_list3").hide();
            loseFocus();
        });
        $('.input_select2').click(function () {
            var ul = $(".ul_list2");
            if (ul.css("display") == "none") {
                event.stopImmediatePropagation();
                ul.slideDown("fast");
            } else {
                ul.slideUp("fast");
            }
            $(".ul_list1").hide();
            $(".ul_list3").hide();
            loseFocus();
        });
        $('.input_select3').click(function () {
            event.stopImmediatePropagation();
            var ul = $(".ul_list3");
            if (ul.css("display") == "none") {
                ul.slideDown("fast");
            } else {
                ul.slideUp("fast");
            }
            $(".ul_list2").hide();
            $(".ul_list1").hide();
            loseFocus();
        });
        $(".ul_list1 li a").click(function () {
            var txt = $(this).text();
            $(".input_select1").val(txt);
            $(".ul_list1").hide();

        });
        $(".ul_list2 li a").click(function () {
            var txt = $(this).text();
            $(".input_select2").val(txt);
            $(".ul_list2").hide();
            periods();
            terminallyMoney();

        });
        $(".ul_list3 li a").click(function () {
            var txt = $(this).text();
            $(".input_select3").val(txt);
            $(".ul_list3").hide();
            periods();
            terminallyMoney();


        });

        function periods() {
            var period = $(".input_select2").val().split("期")[0];
            $(".periods").text(period);
        }

        $(".money").keyup(function () {
            var val = $('.money').val();
            $('#allMoney').text(val);
            periods();
            terminallyMoney();
        });


        function allMoney() {
            var allMoney = $(".money").val() * Number($(".input_select2").val().split("期")[0]);
            $("#allMoney").text(allMoney);
        }

        function terminallyMoney() {
            var bearer = $(".input_select3").val();
            if (bearer == '商户') {
                if ($(".periods").text() == 3) {
                    $('#allMoney').text($('.money').val());
                    $('.terminally').text(($('.money').val()/ 3).toFixed(2));

                } else if ($(".periods").text() == 6) {
                    $('#allMoney').text($('.money').val());
                    $('.terminally').text(($('.money').val()/ 6).toFixed(2));

                } else if ($(".periods").text() == 12) {
                    $('#allMoney').text($('.money').val());
                    $('.terminally').text(($('.money').val()/ 12).toFixed(2));

                }
            }
            if (bearer == '顾客') {
                if ($(".periods").text() == 3) {
                    $('#allMoney').text(($('.money').val() * 1.023).toFixed(2));
                    $('.terminally').text(($('.money').val() * 1.023 / 3).toFixed(2));

                } else if ($(".periods").text() == 6) {
                    $('#allMoney').text(($('.money').val() * 1.045).toFixed(2));
                    $('.terminally').text(($('.money').val() * 1.045 / 6).toFixed(2));

                } else if ($(".periods").text() == 12) {
                    $('#allMoney').text(($('.money').val() * 1.075).toFixed(2));
                    $('.terminally').text(($('.money').val() * 1.075 / 12).toFixed(2));

                }
            }
        }

        function stopPropagation(e) {
            if (e.stopPropagation)
                e.stopPropagation();
            else
                e.cancelBubble = true;
        }


        //失去焦点
        function loseFocus(){
            document.activeElement.blur();
            $('.input_select').blur();
        }

    });
    function reset() {
        $('.imgOne').html('');
        $(".mark").css('display', 'none');
        $(".er_code").css('display', 'none');

    }
</script>
</body>
</html>






