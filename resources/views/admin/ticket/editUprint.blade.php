@extends('layouts.amaze1')
@section('title','修改设备绑定')
@section('content')
    <div class="widget am-cf">
        <div class="widget-head am-cf">
            <div class="widget-title am-fl">修改设备绑定</div>
            <div class="widget-title am-fl">
                <span style="color:red">{{session("warnning")}}</span>
            </div>
        </div>
        <form class="am-form tpl-form-line-form" action="{{route('updateUprint')}}" method="post">
            <input type="hidden" name="id" value="{{$list->id}}">
            <label for="user-phone" class="">设备名</label>

            <input class="am-form-field" placeholder="请输入设备名" type="text" name="mname" value="{{$list->mname}}">

            <label for="user-phone" class="">设备号</label>
            <input class="am-form-field" placeholder="请输入设备号" type="text" value="{{$list->machine_code}}" name="merchine_code">
            <label for="user-phone" class="">二维码链接</label>
            <input class="am-form-field" placeholder="请输入正确的网址,如https://isv.umxnt.com/admin/alipayopen" type="text" name="code" value="{{$list->code}}">
            <label for="user-phone" class="">二维码上方文字</label>
            <input class="am-form-field" placeholder="过长排版会有问题,建议15字以内" type="text" name="code_description" value="{{$list->code_description}}">
            <label for="user-phone" class="">绑定店铺</label>
            <label for="user-phone" class="">商户手机号</label>
            <input class="am-form-field" placeholder="请输入商户手机号" type="text" name="phone" value="{{$list->phone}}" required>
            <div class="doc-example">
                <select data-am-selected="{searchBox: 1,maxHeight: 200}" style="display: none;" name="merchine">
                    <option value=""></option>
                    @foreach($oali as $v)
                        @if($v->name)
                            <option value="{{$v->store_id}}*{{$v->name}}" @if($v->store_id==$list->store_id) selected @endif>{{$v->name}}(支付宝当面付)</option>
                        @endif
                    @endforeach
                    @foreach($sali as $v)
                        @if($v->name)
                            <option value="{{$v->store_id}}*{{$v->name}}" @if($v->store_id==$list->store_id) selected @endif>{{$v->name}}(支付宝口碑)</option>
                        @endif
                    @endforeach
                    @foreach($weixin as $v)
                        @if($v->name)
                            <option value="{{$v->store_id}}*{{$v->name}}" @if($v->store_id==$list->store_id) selected @endif>{{$v->name}}(微信)</option>
                        @endif
                    @endforeach
                    @foreach($pingan as $v)
                        @if($v->name)
                            <option value="{{$v->store_id}}*{{$v->name}}" @if($v->store_id==$list->store_id) selected @endif>{{$v->name}}(平安银行)</option>
                        @endif
                    @endforeach
                    @foreach($pufa as $v)
                        @if($v->name)
                            <option value="{{$v->store_id}}*{{$v->name}}" @if($v->store_id==$list->store_id) selected @endif>{{$v->name}}(浦发银行)</option>
                        @endif
                    @endforeach
                    @foreach($unionpay as $v)
                        @if($v->name)
                            <option value="{{$v->store_id}}*{{$v->name}}" @if($v->store_id==$list->store_id) selected @endif>{{$v->name}}(银联)</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <label for="user-phone" class="">打印张数</label>
            <div class="doc-example">
                <select  data-am-selected name="number" required>
                    <option value="1" @if($list->number==1) selected @endif>1</option>
                    <option value="2" @if($list->number==2) selected @endif>2</option>
                    <option value="3" @if($list->number==3) selected @endif>3</option>
                    <option value="4" @if($list->number==4) selected @endif>4</option>
                    <option value="5" @if($list->number==5) selected @endif>5</option>
                    <option value="6" @if($list->number==6) selected @endif>6</option>
                    <option value="7" @if($list->number==7) selected @endif>7</option>
                    <option value="8" @if($list->number==8) selected @endif>8</option>
                </select>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">确认修改
                </button>
            </div>
            {{csrf_field()}}

        </form>
    </div>


@endsection