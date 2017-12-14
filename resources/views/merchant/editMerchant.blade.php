@extends('layouts.amaze')
@section('title','扫码枪收款')
@section('content')
    <link rel="stylesheet" href="{{asset('/zeroModal/zeroModal.css')}}">
    <script src="{{asset('/zeroModal/zeroModal.js')}}"></script>
    <div class="row">

        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-fl">修改账户信息</div>
                    <div class="widget-function am-fr">
                        <a href="javascript:;" class="am-icon-cog"></a>
                    </div>
                </div>
                <div class="widget-body am-fr">

                    <form class="am-form tpl-form-line-form" role="form" action="{{route("updateMerchant")}}" method="post">
                        {{csrf_field()}}
                        <div class="am-form-group">
                            <div class="am-u-sm-9">
                               <span style="color:red">{{session("warnning")}}</span>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-3 am-form-label">手机号码</label>
                            <div class="am-u-sm-9">
                                <input class="tpl-form-input" value="{{$phone}}"  name="phone"
                                       type="text" required>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label class="am-u-sm-3 am-form-label">新密码</label>
                            <div class="am-u-sm-9">
                                <input placeholder="请输入新密码"  name="password" type="password">
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label for="user-weibo" class="am-u-sm-3 am-form-label">确认密码</label>
                            <div class="am-u-sm-9">
                                <input name="password_confirmation"  placeholder="请确认密码" type="password">
                                <div>

                                </div>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                                <button type="submit" data-am-modal="{target: '#my-alert'}"
                                        class="am-btn am-btn-primary tpl-btn-bg-color-success ">确认修改
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection