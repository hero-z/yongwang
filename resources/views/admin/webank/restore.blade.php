@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('restore')
    <div class="ibox-content">
        <div class="row">
            <form action="{{route('webankRestore')}}" method="post">
                <div class="col-sm-3">
                    <div class="input-group">
                        <input placeholder="请输入商户简称" @if(isset($alias_name))value="{{$alias_name}}"@endif class="input-sm form-control" type="text" name="alias_name"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                    </div>
                </div>
                {{csrf_field()}}
            </form>
            {{--<a href="{{route('PingAnStoreAdd')}}"><button class="btn btn-success " type="button"><span class="bold">添加商户</span></button></a>--}}
            <a class="J_menuItem" href="{{route('QrLists')}}"> <button type="button" class="btn btn-outline btn-default">我的商户码</button></a>
            <a class="J_menuItem" href="{{route('webankindex')}}"> <button type="button" class="btn btn-outline btn-default">商户流水</button></a>
            @permission('webankconfig')
            <a class="J_menuItem" href="{{route('webankconfig')}}"> <button type="button" class="btn btn-outline btn-default">银行通道配置</button></a>
            @endpermission
        </div>
        <form action="{{route("webankallstoreback")}}" method="post">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>商户id</th>
                        <th>商户简称</th>
                        <th>联系人名称</th>
                        <th>联系人手机号</th>
                        {{--<th>状态</th>--}}
                        <th>归属员工</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($data)
                    @foreach($data as $v)
                        <tr>
                            <td>
                                <div class="icheckbox_square-green"><input type="checkbox"  name="data[]" value="{{$v->id}}"></div>
                            </td>
                            <td>{{$v['store_id']}}</td>
                            {{--  <td><span class="pie">{{$v['name']}}</span></td>--}}
                            <td><span class="pie">{{$v['alias_name']}}</span></td>
                            <td>{{$v['contact_name']}}</td>
                            <td><span class="pie">{{$v['contact_phone_no']}}</span></td>
                            {{--<td><span class="pie">{{$v['status']}}</span></td>--}}
                            <td><span class="pie">@if($users&&$users[$v->user_id]){{$users[$v->user_id]}}@endif</span></td>
                            <td>
                                @permission("webankRestore")
                                <a href="{{route('webankstoreback',['id'=>$v->store_id])}}"> <button class="btn btn-primary" type="button">还原</button></a>
                                @endpermission
                                @permission("dropwebankstore")
                                <button onclick='del("{{$v['id']}}")' class="btn btn-danger" type="button">彻底删除</button>
                                @endpermission
                            </td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
                <button class="btn btn-primary" type="submit">还原选中</button>
                <ul class="am-pagination pull-right" style="margin-top:-20px;">
                    @if($data)
                        {{$data->appends(['alias_name'=>$alias_name])->links()}}
                    @endif
                </ul>
            </div>
            {{csrf_field()}}
        </form>
    </div>
    <script type="text/javascript">
        function del(id) {
            //询问框
            // alert(id);
            layer.confirm('确定要彻底删除', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{route('pinganDelete')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            if(data.success){
                                window.location.href = "{{route('pinganRestore')}}";
                            }else{
                                layer.msg("删除失败,请检查是否有权限")
                            }

                        }, "json");
            }, function () {

            });
        }
    </script>
@endsection
@section('js')
@endsection