@extends('layouts.amaze')
@section('title','扫码枪收款')
@section('content')
    <link rel="stylesheet" href="{{asset('/zeroModal/zeroModal.css')}}">
    <script src="{{asset('/zeroModal/zeroModal.js')}}"></script>
    <div class="row">

        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            @if(!$MerchantPayWay)
                <div class="am-alert am-alert-warning" data-am-alert="">
                    <button type="button" class="am-close">×</button>
                    <p>你还没有配置收银通道请点击这里(<a href="{{route('setWays')}}">点我</a>)设置通道</p></div>
            @endif
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-fl">扫码收款</div>
                    <div class="widget-function am-fr">
                        <a href="javascript:;" class="am-icon-cog"></a>
                    </div>
                </div>
                <div class="widget-body am-fr">

                    <form class="am-form tpl-form-line-form" role="form"
                          action="" method="post">
                        {{csrf_field()}}
                        <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-3 am-form-label">收款信息</label>
                            <div class="am-u-sm-9">
                                <input class="tpl-form-input" value="{{$store_name}}收款" id="desc" name="desc"
                                       placeholder="请输入商品信息,默认可不填写"
                                       type="text">
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label class="am-u-sm-3 am-form-label">收款金额</label>
                            <div class="am-u-sm-9">
                                <input placeholder="收款金额" id="price" name="price" type="text">
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label for="user-weibo" class="am-u-sm-3 am-form-label">收款授权码</label>
                            <div class="am-u-sm-9">
                                <input name="code" id="code" placeholder="收款授权码,请连接扫码枪" type="text">
                                <div>

                                </div>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                                <button type="submit" data-am-modal="{target: '#my-alert'}"
                                        class="am-btn am-btn-primary tpl-btn-bg-color-success ">确认收款
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-fl">最新收款列表</div>
                    <div class="widget-function am-fr">
                        <a href="javascript:;" class="am-icon-cog"></a>
                    </div>
                </div>
                <div class="widget-body  widget-body-lg am-fr">

                    <table class="am-table am-table-compact am-table-striped tpl-table-black " id="example-r"
                           width="100%">
                        <thead>
                        <tr>
                            <th>时间</th>
                            <th>金额</th>
                            <th>方式</th>
                            <th>状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $v)
                            <tr class="gradeX">
                                <td>{{$v->updated_at}}</td>
                                <td>{{$v->total_amount}}</td>
                                @if($v->type=="103")
                                    <td>支付宝当面付(扫码枪)</td>
                                @endif
                                @if($v->type=="105")
                                    <td>支付宝口碑(扫码枪)</td>
                                @endif
                                @if($v->type=="202")
                                    <td>微信(扫码枪)</td>
                                @endif
                                @if($v->type=="305")
                                    <td>平安支付宝(扫码枪)</td>
                                @endif
                                @if($v->type=="306")
                                    <td>平安微信(扫码枪)</td>
                                @endif
                                @if($v->type=="307")
                                    <td>平安京东(扫码枪)</td>
                                @endif
                                @if($v->type=="402")
                                    <td>银联(扫码枪)</td>
                                @endif
                                @if($v->type=="603")
                                    <td>浦发支付宝(扫码枪)</td>
                                @endif
                                @if($v->type=="604")
                                    <td>浦发微信(扫码枪)</td>
                                @endif

                                @if($v->pay_status=="1")
                                    <td><button type="button" class="am-btn-success">支付成功</button></td>
                                @endif
                                @if($v->pay_status=="2")
                                    <td><button type="button" class="am-btn-danger">取消支付</button></td>
                                @endif
                                @if($v->pay_status=="3")
                                    <td><button type="button" class="am-btn-danger">等待支付</button></td>
                                @endif
                                @if($v->pay_status=="4")
                                    <td><button type="button" class="am-btn-danger">订单关闭</button></td>
                                @endif
                                @if($v->pay_status=="5")
                                    <td><button type="button" class="am-btn-danger">已退款</button></td>
                                @endif
                                @if($v->pay_status=="")
                                    <td><button type="button" class="am-btn-danger">支付失败</button></td>
                                @endif
                            </tr>
                        @endforeach
                        <!-- more data -->
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <script>

        $("form").submit(function (e) {
            tmpStr = $.trim($("#price").val());
            code = $.trim($("#code").val());
            tmpStr = parseFloat(tmpStr);
            if (!(tmpStr >= 0.01)) {
                alert("支付金额必须填写一个大于0.01的数");
                $("#price").focus();
                return false;
            }
            if (!code) {
                alert("支付授权码为空,请填写或扫码获取");
                $("#code").focus();//鼠标焦点
                return false;
            }
            $('#code').attr("disabled",true);
            $('#price').attr("disabled",true);
            zeroModal.loading(6);
            $.post("{{route('TradePayCodeType')}}", {
                    _token: "{{csrf_token()}}",
                    desc: $("#desc").val(),
                    code: $("#code").val(),
                    price: $("#price").val(),
                },
                function (data) {
                    if (data.status == 1) {
                        zeroModal.alertSuccess({
                            content: '支付成功',
                            contentDetail: '当前收款金额为'+$("#price").val(),
                            okFn: function() {
                                window.location='/merchant/AlipayTradePayCreate';
                            }
                        });
                        return false;
                    } else {
                        zeroModal.alert({
                            content: data.msg,
                            okFn: function() {
                                window.location='/merchant/AlipayTradePayCreate';
                            }
                        });
                        return false;
                    }
                }, "json");
            return false;
        });
    </script>
@endsection