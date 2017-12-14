@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>添加角色</h5>
            </div>
            <div class="ibox-content">
                <form class="form-horizontal" action="{{url('/admin/alipayopen/role')}}" method="post">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label class="col-sm-3 control-label">角色名称</label>
                        <div class="col-sm-8">
                            <input placeholder="请输入角色名称,如 admin" class="form-control" name="name" type="text">
                            <span class="help-block m-b-none" style="color: red">{{ $errors->first('name') }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">显示名称</label>
                        <div class="col-sm-8">
                            <input placeholder="显示名称，如 管理员" class="form-control" name="display_name" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">角色描述</label>
                        <div class="col-sm-8">
                            <input placeholder="显示描述，如 管理员是管理所有员工的账号" name="description" class="form-control" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-8">
                            <button class="btn btn-sm btn-info" type="submit">确认添加</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection