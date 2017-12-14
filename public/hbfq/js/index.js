$(function () {

    $('.btn').click(function () {
        $('.mark').show();
        $('.er_code').show();
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
