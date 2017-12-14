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
                        <form action="{{route('updateNewLand')}}" method="post">
                            {{csrf_field()}}
                            <span style="color:red">{{session('message')}}</span>
                            <div class="form-group">
                                <label>商户名称</label>
                                <input value="{{$info['store_name']}}" id="store_name" required placeholder="请填写商户名称" class="form-control"
                                       name="store_name" type="text">
                            </div>
                            @if ($errors->has('store_name'))
                                <span class="ui-icon-help">
                                        <strong>{{ $errors->first('store_name') }}</strong>
                                    </span>
                            @endif
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户号</label>
                                <input id="merc_id"  value="{{ $info['merc_id'] }}" required placeholder="请填写新大陆内部商户号" class="form-control" name="merc_id"
                                       type="text">
                            </div>
                            @if ($errors->has('merc_id'))
                                <span class="ui-icon-help">
                                        <strong>{{ $errors->first('merc_id') }}</strong>
                                    </span>
                            @endif
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系方式</label>
                                <input id="stoe_cnt_tel" value="{{ $info['stoe_cnt_tel'] }}" required placeholder="请填写商户的手机号" class="form-control" name="stoe_cnt_tel"
                                       type="text">
                            </div>
                            @if ($errors->has('stoe_cnt_tel'))
                                <span class="ui-icon-help">
                                        <strong>{{ $errors->first('stoe_cnt_tel') }}</strong>
                                    </span>
                            @endif
                            <div class="hr-line-dashed"></div>
                            <input type="hidden" name="id" value="{{$info['id']}}">
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