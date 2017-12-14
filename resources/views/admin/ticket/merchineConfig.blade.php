@extends('layouts.amaze1')
@section('title','设置通道')
@section('content')
    <div class="widget am-cf">
        <div class="widget-head am-cf">
            <div class="widget-title am-fl">设备配置</div>
            <div class="widget-title am-fl">
                <span style="color:red">{{session("warnning")}}</span>
            </div>
        </div>
        <form class="am-form tpl-form-line-form" action="{{route('updateConfig')}}" method="post">
         <input type="hidden" name="id" value="{{$list->id}}">
            <label for="user-phone" class="">push_id</label>

            <input class="am-form-field" placeholder="" type="text" name="push_id" value="{{$list->push_id}}">

            <label for="user-phone" class="">push_key</label>

            <input class="am-form-field" placeholder="" value="{{$list->push_key}}" type="text" name="push_key">
            <label for="user-phone" class="">push_user_name</label>
            <input class="am-form-field" placeholder="" type="text" value="{{$list->push_user_name}}" name="push_user_name">
            <div class="hr-line-dashed"></div>
            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">确认配置
                </button>
            </div>
            {{csrf_field()}}

        </form>
    </div>


@endsection