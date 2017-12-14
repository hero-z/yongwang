@extends('layouts.amaze')
@section('title','设置易联云设备')
@section('content')
    <div class="widget am-cf">
        <div class="widget-head am-cf">
            <div class="widget-title am-fl">添加易联云设备</div>
            <span style="color:green">{{session("warnning")}}</span>
        </div>
        <form class="am-form tpl-form-line-form" action="{{route('insertMerchine')}}" method="post">
            <label for="user-phone" class="">绑定店铺</label>
            <div class="doc-example">
                <select data-am-selected="{searchBox: 1,maxHeight: 200}" style="display: none;" name="merchine" required>
                    <option value=""></option>
                    @foreach($list as $v)
                        @if($v->store_type=="oalipay")
                            <option value="{{$v->store_id}}*{{$v->store_name}}">{{$v->store_name}}(支付宝当面付)</option>
                        @endif
                        @if($v->store_type=="salipay")
                            <option value="{{$v->store_id}}*{{$v->store_name}}">{{$v->store_name}}(支付宝口碑)</option>
                        @endif
                        @if($v->store_type=="weixin")
                            <option value="{{$v->store_id}}*{{$v->store_name}}">{{$v->store_name}}(微信)</option>
                        @endif
                        @if($v->store_type=="pingan")
                            <option value="{{$v->store_id}}*{{$v->store_name}}">{{$v->store_name}}(平安银行)</option>
                        @endif

                        @if($v->store_type=="pufa")
                            <option value="{{$v->store_id}}*{{$v->store_name}}">{{$v->store_name}}(浦发银行)</option>
                        @endif
                        @if($v->store_type=="unionpay")
                            <option value="{{$v->store_id}}*{{$v->store_name}}">{{$v->store_name}}(银联)</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <label for="user-phone" class="">设备名</label>

            <input class="am-form-field" placeholder="请输入设备名" type="text" name="mname" required>

            <label for="user-phone" class="">设备密钥</label>

            <input class="am-form-field" placeholder="请输入设备密钥" type="text" name="msign" required>
            <label for="user-phone" class="">设备号</label>
            <input class="am-form-field" placeholder="请输入设备号" type="text" name="merchine_code" required>
            <label for="user-phone" class="">商户手机号</label>
            <input class="am-form-field" placeholder="请输入商户手机号" type="text" name="phone" required>

            <div class="hr-line-dashed"></div>
            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">确认保存
                </button>
            </div>
            {{csrf_field()}}

        </form>
    </div>


@endsection