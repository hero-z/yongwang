@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">设置费率区间(<span style="color:red">限制代理商和员工的费率范围,即员工和代理商的费率必须在这个区间内.如:0.25-0.8</span>)</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('dosetrate') }}">
                            {{ csrf_field() }}
                            <div class="form-group{{ $errors->has('minrate') ? ' has-error' : '' }}">
                                <label for="minrate" class="col-md-4 control-label">最低费率</label>

                                <div class="col-md-6">
                                    <input id="minrate" placeholder="如:0.25(单位%)即一万元需要25元费用" value="@if($min){{$min}}@endif" type="text" class="form-control" name="minrate" required>

                                    @if ($errors->has('minrate'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('minrate') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('maxrate') ? ' has-error' : '' }}">
                                <label for="maxrate" class="col-md-4 control-label">最高费率</label>

                                <div class="col-md-6">
                                    <input id="maxrate" placeholder='如:0.8(单位%)即一万元需要80元费用' type="text" value="@if($max){{$max}}@endif" class="form-control" name="maxrate" required>

                                    @if ($errors->has('maxrate'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('maxrate') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        保存
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
