@extends('layouts.amaze1')
@section('title','设置通道')
@section('content')
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-fl">支付宝微信二码合一</div>
                </div>
                <div class="widget-body am-fr">
                    <form class="am-form tpl-form-line-form" action="{{route('updateAddTwo')}}" method="post">
                        <input type="hidden" name="id" value="{{$id}}">
                        <div class="am-form-group ">
                            <span style="color:red">{{session("warnning")}}</span>
                            <label for="user-phone" class="am-u-sm-5 am-form-label">支付宝通道</label>
                            <div class="am-u-sm-7">
                                <select data-am-selected="{searchBox: 1,maxHeight: 200,maxWidth:100,btnWidth: '200', btnSize: 'sm', btnStyle: 'secondary'}" id="ali" style="display: none;" name="ali" onchange="change()">
                                    @if($oali)
                                        @foreach($oali as $v)
                                            @if($v->auth_shop_name)
                                                <option class="am-u-sm-3" value="store_id_a*{{$v->store_id}}*alipay_ways*oalipay*" @if($list->store_id_a==$v->store_id)selected="selected"@endif>{{$v->auth_shop_name}}(当面付)</option>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if($sali)
                                        @foreach($sali as $v)
                                            @if($v->main_shop_name)
                                                <option class="am-u-sm-3" value="store_id_a*{{$v->store_id}}*alipay_ways*salipay" @if($list->store_id_a==$v->store_id)selected="selected"@endif>{{$v->main_shop_name}}(口碑)</option>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if($pingan)
                                        @foreach($pingan as $v)
                                            @if($v->alias_name)
                                                <option class="am-u-sm-3" value="store_id_a*{{$v->external_id}}*alipay_ways*palipay" @if($list->store_id_a==$v->external_id)selected="selected"@endif>{{$v->alias_name}}(平安)</option>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if($pufa)
                                            @foreach($pufa as $v)
                                                <option class="am-u-sm-3" value="store_id_a*{{$v->store_id}}*alipay_ways*pfalipay" @if($list->store_id_a==$v->store_id)selected="selected"@endif>{{$v->merchant_short_name}}(浦发)</option>
                                            @endforeach
                                        @endif
                                </select>
                            </div>
                        </div>

                        <div class="am-form-group">
                            <label for="user-phone" class="am-u-sm-5 am-form-label">微信支付通道</label>
                            <div class="am-u-sm-7">
                                <select data-am-selected="{searchBox: 2,maxHeight: 200,maxWidth:100,btnWidth: '200', btnSize: 'sm', btnStyle: 'secondary'}" id="weixin" style="display: none;" name="weixin">
                                    @if($weixin)
                                        @foreach($weixin as $v)
                                            @if($v->store_name)
                                                <option value="store_id_w*{{$v->store_id}}*weixin_ways*weixin" @if($list->store_id_w==$v->store_id)selected="selected"@endif>{{$v->store_name}}(微信官方)</option>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if($pingan)
                                        @foreach($pingan as $v)
                                            @if($v->alias_name)
                                                <option value="store_id_w*{{$v->external_id}}*weixin_ways*pweixin" @if($list->store_id_w==$v->external_id)selected="selected"@endif>{{$v->alias_name}}(平安微信)</option>
                                            @endif
                                        @endforeach
                                    @endif
                                        @if($pufa)
                                            @foreach($pufa as $v)
                                                <option class="am-u-sm-3" value="store_id_w*{{$v->store_id}}*weixin_ways*pfweixin" @if($list->store_id_w==$v->store_id)selected="selected"@endif>{{$v->merchant_short_name}}(浦发)</option>
                                            @endforeach
                                        @endif
                                </select>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label for="user-phone" class="am-u-sm-5 am-form-label">京东支付通道</label>
                            <div class="am-u-sm-7" >
                                <select data-am-selected="{searchBox: 2,maxHeight: 200,maxWidth:100,btnWidth: '200', btnSize: 'sm', btnStyle: 'secondary'}" id="jd" style="display: none;" name="jd" onchange="changeb()">
                                    <option value="" ></option>
                                    @if($pingan)
                                        @foreach($pingan as $v)
                                            @if($v->alias_name)
                                                <option value="store_id_j*{{$v->external_id}}*jd_ways*pjd" @if($list->store_id_j==$v->external_id)selected="selected"@endif>{{$v->alias_name}}(平安京东)</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label for="user-phone" class="am-u-sm-5 am-form-label">翼支付通道</label>
                            <div class="am-u-sm-7">
                                <select data-am-selected="{searchBox: 2,maxHeight: 200,maxWidth:100,btnWidth: '200', btnSize: 'sm', btnStyle: 'secondary'}" id="bestpay" style="display: none;" name="bestpay" onchange="changec()">
                                    <option value="" ></option>
                                    @if($pingan)
                                        @foreach($pingan as $v)
                                            @if($v->alias_name)
                                                <option value="store_id_b*{{$v->external_id}}*bestpay_ways*pbestpay" @if($list->store_id_b==$v->external_id)selected="selected"@endif>{{$v->alias_name}}(平安翼支付)</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label for="user-phone" class="am-u-sm-5 am-form-label">开启或关闭支付</label>
                            <div class="am-u-sm-7">
                                <select data-am-selected="{searchBox: 3,maxHeight: 200,maxWidth:100,btnWidth: '200', btnSize: 'sm', btnStyle: 'secondary'}" id="open" style="display: none;" name="open">
                                    <option value="1" @if($list->status=="1")selected="selected"@endif>开启</option>
                                    <option value="0" @if($list->status=="0")selected="selected"@endif>关闭</option>


                                </select>
                            </div>
                        </div>

                        {{csrf_field()}}
                        <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-5">
                                <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">确认修改
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--<script>--}}
    {{--function save() {--}}
    {{--$.post("{{route('setWaysPost')}}", {--}}
    {{--weixin: $("#weixin").val(),--}}
    {{--alipay: $("#alipay").val(),--}}
    {{--jd: $("#jd").val(),--}}
    {{--_token: "{{csrf_token()}}"--}}
    {{--},--}}
    {{--function (data) {--}}
    {{--alert(data.msg);--}}
    {{--}, "json");--}}
    {{--}--}}
    {{--</script>--}}
@endsection