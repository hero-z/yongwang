@extends('layouts.amaze1')
@section('title','设置通道')
@section('content')
    <div class="widget am-cf">
        <div class="widget-head am-cf">
            <div class="widget-title am-fl">添加已开口碑店铺</div>
            <div class="widget-title am-fl">
                <span style="color:red">{{session("warnning")}}</span>
            </div>
        </div>
        <form class="am-form tpl-form-line-form" action="{{route('insertOldShop')}}" method="post">
            <label for="user-phone" class="">授权店铺名称</label>
            <div class="doc-example">
                <select required="required" data-am-selected="{searchBox: 1,maxHeight: 200}" style="display: none;" name="auth_shop">
                    <option value=""></option>
                    @foreach($list as $v)
                        <option value="{{$v->app_auth_token}}*{{$v->auth_shop_name}}">{{$v->auth_shop_name}}</option>
                    @endforeach

                </select>
            </div>
            <label for="user-phone" class="">门店id </label>

            <input class="am-form-field" required="required" placeholder="请输入门店id" type="text" name="shop_id">

            <label for="user-phone" class="">门店编号</label>

            <input class="am-form-field" required="required" placeholder="请输入门店编号" type="text" name="store_id">

            <div class="hr-line-dashed"></div>
            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <label for="user-phone" class="">说明</label>
            </div>
            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <img src="http://isv.umxnt.com/amazeui/assets/img/shuoming.png" style="max-width:800px" id="img" >
            </div>
            <div class="hr-line-dashed"></div>
            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">确认保存
                </button>
            </div>
            {{csrf_field()}}

        </form>
    </div>

@endsection
@section('js')
@endsection