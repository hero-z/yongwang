@extends('layouts.amaze1')
@section('title','设置通道')
@section('content')
    <div class="widget am-cf">
        <div class="widget-head am-cf">
            <div class="widget-title am-fl">添加设备</div>
            <span style="color:green">{{session("warnning")}}</span>
        </div>
        <form class="am-form tpl-form-line-form" action="{{route('insertUprint')}}" method="post">
            <label for="user-phone" class="">绑定店铺</label>
            <div class="doc-example">
                <select data-am-selected="{searchBox: 1,maxHeight: 200}" style="display: none;" name="merchine" required>
                    <option value=""></option>
                    @foreach($oali as $v)
                        @if($v->name)
                            <option value="{{$v->store_id}}*{{$v->name}}">{{$v->name}}(支付宝当面付)</option>
                        @endif
                    @endforeach
                    @foreach($sali as $v)
                        @if($v->name)
                            <option value="{{$v->store_id}}*{{$v->name}}">{{$v->name}}(支付宝口碑)</option>
                        @endif
                    @endforeach
                    @foreach($weixin as $v)
                        @if($v->name)
                            <option value="{{$v->store_id}}*{{$v->name}}">{{$v->name}}(微信)</option>
                        @endif
                    @endforeach
                    @foreach($pingan as $v)
                        @if($v->name)
                            <option value="{{$v->store_id}}*{{$v->name}}">{{$v->name}}(平安银行)</option>
                        @endif
                    @endforeach
                    @foreach($pufa as $v)
                        @if($v->name)
                            <option value="{{$v->store_id}}*{{$v->name}}">{{$v->name}}(浦发银行)</option>
                        @endif
                    @endforeach
                    @foreach($unionpay as $v)
                        @if($v->name)
                            <option value="{{$v->store_id}}*{{$v->name}}">{{$v->name}}(银联)</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <label for="user-phone" class="">打印张数</label>
            <div class="doc-example">
                <select  data-am-selected name="number" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                </select>
                </div>
            <label for="user-phone" class="">设备名</label>

            <input class="am-form-field" placeholder="请输入设备名" type="text" name="mname" required>
            <label for="user-phone" class="">设备号</label>
            <input class="am-form-field" placeholder="请输入设备号" type="text" name="merchine_code" required>
            <div class="hr-line-dashed"></div>
            <label for="user-phone" class="">二维码链接</label>
            <input class="am-form-field" placeholder="请输入正确的网址,如https://isv.umxnt.com/admin/alipayopen" type="text" name="code" >
            <label for="user-phone" class="">二维码上方文字</label>
            <input class="am-form-field" placeholder="请输入....." type="text" name="code_description" >
            <div class="hr-line-dashed"></div>
            <label for="user-phone" class="">商户手机号</label>
            <input class="am-form-field" placeholder="请输入商户手机号" type="text" name="phone" required>
            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">确认保存
                </button>
            </div>
            {{csrf_field()}}

        </form>
    </div>


@endsection