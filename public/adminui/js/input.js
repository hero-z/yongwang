
$("#addUser").click(function() {
    $(".input").each(function () {
        var val = $(this).val();
        if (val == "") {
            $(this).focus().css({
                "border": "1px solid red"
            });
            $(this).next().show()

        }

    });
    $(".select option:selected").each(function () {
        var select = $(this).text();
        if ( select == "请选择省份" || select == "请选择城市" || select == "请选择区" || select == "请选择代理级别" || select == "请选择系统角色") {
            $(this).parent().css({
                "border": "1px solid red"
            });
            $(this).parent().next().show()


        }

    });
});

$('#addrole_sure').click(function () {
    $(".input").each(function () {
        var val = $(this).val();
        if (val == "") {
            $(this).focus().css({
                "border": "1px solid red"
            });
            $(this).next().show()

        }

    });
});



$(".input").each(function() {
    $(this).click(function () {
        $(this).css({"border": "1px solid #d9d9d9"});
        $(this).next().hide()
    });
});
$(".select ").each(function () {
    $(this).click(function () {
        $(this).css({'border':'1px solid #d9d9d9'});
        $(this).next().hide()

    })
});