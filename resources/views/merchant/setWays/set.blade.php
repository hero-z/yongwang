@extends('layouts.amaze')
@section('title','设置通道')
@section('content')
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-fl">扫码枪通道配置</div>
                </div>
                <div class="widget-body am-fr">
                    <form class="am-form tpl-form-line-form">
                        <div class="am-form-group">
                            <label for="user-phone" class="am-u-sm-3 am-form-label">支付宝通道</label>
                            <div class="am-u-sm-9">
                                <select data-am-selected="{searchBox: 1}" id="alipay" style="display: none;">
                                    <option value="">请选择收款通道</option>
                                    @if($merchant)
                                        @foreach($merchant as $v)
                                            @if($v['store_type']!='weixin')
                                                <option value="{{$v['store_type']}}" @if($m&&$m[0]['alipay']==$v['store_type']) selected @endif >{{$v['desc_pay']}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label for="user-phone" class="am-u-sm-3 am-form-label">微信支付通道</label>
                            <div class="am-u-sm-9">
                                <select data-am-selected="{searchBox: 2}" id="weixin" style="display: none;">
                                    <option value="">请选择收款通道</option>
                                    @if($merchant)
                                        @foreach($merchant as $v)
                                            @if($v['store_type']!=='oalipay'&&$v['store_type']!=='salipay')
                                                <option value="{{$v['store_type']}}" @if($m&&$m[0]['weixin']==$v['store_type']) selected @endif >{{$v['desc_pay']}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label for="user-phone" class="am-u-sm-3 am-form-label">京东支付通道</label>
                            <div class="am-u-sm-9">
                                <select data-am-selected="{searchBox: 3}" id="jd" style="display: none;">
                                    <option value="">请选择收款通道</option>
                                    @if($merchant)
                                        @foreach($merchant as $v)
                                            @if($v['store_type']=='pingan')
                                                <option value="{{$v['store_type']}}" @if($m&&$m[0]['jd']==$v['store_type']) selected @endif >{{$v['desc_pay']}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                                <button type="button" onclick="save()"
                                        class="am-btn am-btn-primary tpl-btn-bg-color-success ">确认保存
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function save() {
            $.post("{{route('setWaysPost')}}", {
                        weixin: $("#weixin").val(),
                        alipay: $("#alipay").val(),
                        jd: $("#jd").val(),
                        _token: "{{csrf_token()}}"
                    },
                    function (data) {
                        alert(data.msg);
                    }, "json");
        }
    </script>
@endsection