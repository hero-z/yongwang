@extends('layouts.weixinpay')
@section('title','微信支付')
@section('css')
    <link href="{{asset('/css/weixinpay/wxpay.css')}}" rel="stylesheet">
@endsection
@section('content')
    <body ontouchstart>
    <input type="hidden" id="store_id" value="{{$shop->store_id}}">
    <input type="hidden" id="m_id" value="{{$m_id}}">
    <div class="weui-wepay-pay__ft">
        <p class="weui-wepay-pay__info" style="font-size: 18px;">{{$shop->alias_name}}</p>
    </div>

    <script type="text/javascript" src=""></script>

    <div class="weui-wepay-pay">
        <div class="weui-wepay-pay__bd">
            <div class="weui-wepay-pay__inner">
                <h1 class="weui-wepay-pay__title">付款金额(元)</h1>
                <div class="weui-wepay-pay__inputs"><strong class="weui-wepay-pay__strong">￥</strong>
                    <input type="number" class="weui-wepay-pay__input" id="total_fee" placeholder="请输入金额"></div>
                <div class="weui-wepay-pay__intro">可询问服务员消费总额</div>
            </div>
        </div>

        <div class="weui-cells">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" placeholder="选填备注" id="remark" name="remark">
                </div>
            </div>
        </div>
        <div class="weui-wepay-pay__ft">
            <div class="weui-wepay-pay__btn">
                <button onclick="callpay()" class="weui-btn weui-btn_primary">立即支付</button>
            </div>

        </div>
    </div>
    <div class="weui-wepay-logos weui-wepay-logos_ft">
        <img src="https://act.weixin.qq.com/static/cdn/img/wepayui/0.1.1/wepay_logo_default_gray.svg" alt=""
             height="16">
    </div>
    </body>
    <script>
        function callpay() {
            $.post("{{route('webankwxdoPay')}}", {
                        _token: "{{csrf_token()}}",
                        store_id: $("#store_id").val(),
                        total_amount: $("#total_fee").val(),
                        remark:$("#remark").val(),
                        type:1,
                        m_id: $("#m_id").val()
                    },
                    function (data) {
                        if (data.success==1) {
                            WeixinJSBridge.invoke(
                                    'getBrandWCPayRequest', eval('('+data.payInfo+')'),
                                    function (res) {
                                        if (res.err_msg == "get_brand_wcpay_request:ok") {
                                            window.location.href = "{{url('admin/webank/weixin/wxpaysuccess?price=')}}"+$("#total_fee").val();
                                            // 使用以上方式判断前端返回,微信团队郑重提示：
                                            // res.err_msg将在用户支付成功后返回 ok，但并不保证它绝对可靠。
                                        }else{
                                            window.location.href = "{{route('webankwxpayerror',['code'=>'111'])}}";
                                        }
                                    }
                            );
                        } else {
                            window.location.href = "{{route('webankwxpayerror')}}";
                        }
                    }, 'json');
        }
    </script>
@endsection
