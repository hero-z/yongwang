@extends('layouts.weixinpay')
@section('title','微信支付')
@section('css')
    <link href="{{asset('/css/weixinpay/wxpay.css')}}" rel="stylesheet">
@endsection

@section('content')



<div class="msg_success">
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">付款成功</h2>
            <p class="weui-msg__desc">{{$price}}元</p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <a  onclick="WeixinJSBridge.invoke('closeWindow',{},function(res){});" class="weui-btn weui-btn_primary">确认</a>
                <!-- <a href="javascript:history.back();" class="weui-btn weui-btn_default">辅助操作</a> -->
            </p>
        </div>
    </div>
    @foreach($ad as $v)
        @if($v->type=="pufa"&&$v->position==1)
            <div class="weui-msg__opr-area"><a href="{{$v->url}}"><img src="{{$v->pic}}" style="box-sizing: border-box; max-width: 100%; height: auto;  vertical-align: middle;border: 0;"></a></div>
        @endif
    @endforeach
    <div class="weui-wepay-logos weui-wepay-logos_ft">
    <img src="https://act.weixin.qq.com/static/cdn/img/wepayui/0.1.1/wepay_logo_default_gray.svg" alt="" height="16">
</div>
</div>

@endsection