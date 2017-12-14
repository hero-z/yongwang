@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>角色对应权限表</h5>
                    </div>
                    <div class="ibox-content">
                        <form method="post" class="form-horizontal" action="{{url('admin/alipayopen/assignmentpost')}}">
                            {{csrf_field()}}
                            <input type="hidden" name="role_id" value="<?php echo $_GET['role_id']?>">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">角色</label>
                                <div class="col-sm-10">
                                    <h4>{{$role->display_name}}</h4>({{$role->name}})
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">权限列表</label>
                                <div class="col-sm-10">
                                    @foreach($permission as $v)
                                        <label class="checkbox-inline">
                                            <input value="{{$v->id}}" @if(in_array($v->id,$p_id)) checked
                                                   @endif id="inlineCheckbox1" name="{{$v->name}}"
                                                   type="checkbox">
                                            <strong> {{$v->display_name}}</strong>({{$v->name}})
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                                <div class="form-group">
                                    <div class="col-sm-4 col-sm-offset-3">
                                        <button class="btn btn-primary" type="submit">提交</button>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection