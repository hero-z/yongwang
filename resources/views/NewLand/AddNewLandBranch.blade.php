@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>添加分店</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="{{route('AddNewLandBranch')}}" method="post">
                            {{csrf_field()}}
                            <span style="color:red">{{session('message')}}</span>
                            <div class="form-group">
                                <label>分店名称</label>
                                <input value="{{ old('store_name') }}" id="store_name" required placeholder="请填写商户名称" class="form-control"
                                       name="store_name" type="text">
                            </div>
                            @if ($errors->has('store_name'))
                                <span class="ui-icon-help">
                                        <strong>{{ $errors->first('store_name') }}</strong>
                                    </span>
                            @endif
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系方式</label>
                                <input id="stoe_cnt_tel" value="{{ old('stoe_cnt_tel') }}" required placeholder="请填写商户的手机号" class="form-control" name="stoe_cnt_tel"
                                       type="text">
                            </div>
                            @if ($errors->has('stoe_cnt_tel'))
                                <span class="ui-icon-help">
                                        <strong>{{ $errors->first('stoe_cnt_tel') }}</strong>
                                    </span>
                            @endif
                            <div class="hr-line-dashed"></div>
                            <input type="hidden" value="{{$pid}}" name="pid">
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