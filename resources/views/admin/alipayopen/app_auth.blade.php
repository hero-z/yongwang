@extends('layouts.publicStyle')
@section('content')
    <div style="text-align: center">
        <img id="img"
             src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(500)->generate($code_url)) !!} ">
    </div>
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>第三方应用授权说明</h5>
            </div>
            <div class="ibox-content">
                <div class="well">
                    <h3>
                        说明
                    </h3> 你可以把这个二维码打印出来给你的员工,商户利用支付宝扫描上面这个二维码进行第三方应用授权，授权以后就可以帮助商户开店！生成收款码了。
                </div>
                <div class="well well-lg">
                    <h3>
                        开口碑店流程
                    </h3> 1.商户扫描上面这个二维码授权 2.在口碑开店列表提交资料 3.口碑开店成功自动签约当面付 4.在门店列表生成收款码
                </div>
                <div class="well well-lg">
                    <h3>
                        不开店只签约当面付流程
                    </h3> 1.商户扫描上面这个二维码授权 2.支付宝后台签约当面付产品（<a href="https://openhome.alipay.com/isv/isvMerchantManage.htm"
                                                            target="_blank">https://openhome.alipay.com/isv/isvMerchantManage.htm</a>）
                    3.软件生成收款码
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = IsPCTrue;
        function IsPCTrue() {
            if (!IsPC()) {
                $("#img").width("100%")
            }
        }
        function IsPC() {
            var userAgentInfo = navigator.userAgent;
            var Agents = ["Android", "iPhone",
                "SymbianOS", "Windows Phone",
                "iPad", "iPod"];
            var flag = true;
            for (var v = 0; v < Agents.length; v++) {
                if (userAgentInfo.indexOf(Agents[v]) > 0) {
                    flag = false;
                    break;
                }
            }
            return flag;
        }
    </script>
@endsection