//校验银行卡
function CheckBankNo(t_bankno) {
    var bankno = $.trim(t_bankno.val());
    if (bankno == "") {
        return false;
    }
    if (bankno.length < 16 || bankno.length > 19) {
        return false;
    }
    var num = /^\d*$/; //全数字
    if (!num.exec(bankno)) {
        return false;
    }
    //开头6位
    var strBin = "10,18,30,35,37,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,58,60,62,65,68,69,84,87,88,94,95,98,99";
    if (strBin.indexOf(bankno.substring(0, 2)) == -1) {
        return false;
    }
    return true;
}

//校验手机号
function IsTel(Tel) {
    var re = new RegExp(/^((\d{11})|^((\d{7,8})|(\d{4}|\d{3})-(\d{7,8})|(\d{4}|\d{3})-(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1})|(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1}))$)$/);
    var retu = Tel.match(re);
    if (retu) {
        return true;
    } else {
        return false;
    }
}
