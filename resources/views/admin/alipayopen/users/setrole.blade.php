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
                        <h5>用户对应角色表</h5>
                    </div>
                    <div class="ibox-content">
                        <form method="post" class="form-horizontal" action="{{url('admin/alipayopen/setRolePost')}}">
                            {{csrf_field()}}
                            <input type="hidden" name="user_id" value="<?php echo $user->id?>">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">用户信息</label>
                                <div class="col-sm-10">
                                    <h4>{{$user->name}}</h4>({{$user->email}})
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">角色列表</label>
                                <div class="col-sm-10">
                                            @foreach($role as $v)
                                                <label class="checkbox-inline">
                                                    <input value="{{$v->id}}" @if(in_array($v->id,$r_u)) checked @endif id="inlineCheckbox1" name="role"
                                                           type="radio">
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