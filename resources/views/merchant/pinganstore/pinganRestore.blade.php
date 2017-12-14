@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('restore')
    <div class="ibox-content">
        <div class="row">
            <form action="{{route('pinganRestoreSearch')}}" method="post">
                <div class="col-sm-3">
                    <div class="input-group">
                        <input placeholder="请输入商户简称" class="input-sm form-control" type="text" name="shopname"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                    </div>
                </div>
                {{csrf_field()}}
            </form>
        </div>
        <form action="{{route("pinganRestoree")}}" method="post">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>商户id</th>
                        <th>商户简称</th>
                        <th>联系人名称</th>
                        <th>联系人手机号</th>
                        <th>状态</th>
                        <th>归属员工</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $v)
                        <tr>
                            <td>
                                <div class="icheckbox_square-green"><input type="checkbox"  name="data[]" value="{{$v->id}}"></div>
                            </td>
                            <td>{{$v['external_id']}}</td>
                            {{--  <td><span class="pie">{{$v['name']}}</span></td>--}}
                            <td><span class="pie">{{$v['alias_name']}}</span></td>
                            <td>{{$v['contact_name']}}</td>
                            <td><span class="pie">{{$v['contact_mobile']}}</span></td>
                            <td><span class="pie">{{$v['status']}}</span></td>
                            <td><span class="pie">{{$v['user_name']}}</span></td>
                            <td><a href="{{url('/admin/pingan/pinganRestoreee?id='.$v->id)}}"> <button class="btn btn-primary" type="button">还原</button></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <button class="btn btn-primary" type="submit">还原选中</button>
                <ul class="am-pagination pull-right" style="margin-top:-20px;">
                    {{$data->links()}}
                </ul>
            </div>
            {{csrf_field()}}
        </form>
    </div>

@endsection
@section('js')
@endsection