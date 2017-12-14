@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>添加商户信息</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="{{route('WxShopPost')}}" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>商户名称</label>
                                <input value="{{ old('store_name') }}" id="store_name" required placeholder="请填写商户名称" class="form-control"
                                       name="store_name" type="text">
                            </div>
                            @if ($errors->has('mch_id'))
                                <span class="ui-icon-help">
                                        <strong>{{ $errors->first('store_name') }}</strong>
                                    </span>
                            @endif
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户号</label>
                                <input id="mch_id"  value="{{ old('mch_id') }}" required placeholder="请填写微信支付商户号" class="form-control" name="mch_id"
                                       type="text">
                            </div>
                            @if ($errors->has('mch_id'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('mch_id') }}</strong>
                                    </span>
                            @endif
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>公众号</label>
                                <input id="app_id" value="{{ old('app_id') }}" placeholder="请填写app_id" class="form-control" name="app_id"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>机具信息</label>
                                <input id="device_info" value="{{ old('device_info') }}" placeholder="请填写门店号或收银设备ID" class="form-control"
                                       name="device_info" type="text">
                            </div>
                            <div>
                                <button class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                        type="submit">
                                    <strong>保存</strong>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')

@endsection
@endsection