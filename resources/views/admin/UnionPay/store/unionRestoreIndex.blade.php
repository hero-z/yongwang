@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('restore')
    <div class="ibox-content">
        <div class="row">
            <form action="{{route("unionRestoreIndex")}}" method="post">
                <div class="col-sm-3">
                    <div class="input-group">
                        <input placeholder="请输入商户简称" class="input-sm form-control" type="text" name="shopname"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                    </div>
                </div>
                {{csrf_field()}}
            </form>
            <a href="{{route('PingAnStoreAdd')}}"><button class="btn btn-success " type="button"><span class="bold">添加商户</span></button></a>
            <a class="J_menuItem" href="{{route('QrLists')}}"> <button type="button" class="btn btn-outline btn-default">我的商户码</button></a>
            <a class="J_menuItem" href="{{route('unionPayBill')}}"> <button type="button" class="btn btn-outline btn-default">商户流水</button></a>
            @permission('pinganconfig')
            <a class="J_menuItem" href="{{route('UnionPaySet')}}"> <button type="button" class="btn btn-outline btn-default">银行通道配置</button></a>
            @endpermission
        </div>
        <form action="{{route("unionSelected")}}" method="post">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>门店id</th>
                        <th>商户简称</th>
                        <th>联系人名称</th>
                        <th>联系人手机号</th>
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
                            <td>{{$v->store_id}}</td>
                            <td><span class="pie">{{$v->alias_name}}</span></td>
                            <td>{{$v->manager}}</td>
                            <td><span class="pie">{{$v->manager_phone}}</span></td>
                            <td><span class="pie">{{$v->name}}</span></td>
                            <td>
                                <a href="{{url('/admin/UnionPay/unionRestore?id='.$v->id)}}"> <button class="btn btn-primary" type="button">还原</button></a>
                              @permission("deleteUnionpay")  <button onclick='del("{{$v['id']}}")' class="btn btn-danger" type="button">彻底删除</button>@endpermission
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <button class="btn btn-primary" type="submit">还原选中</button>
                <ul class="am-pagination pull-right" style="margin-top:-20px;">
                    {{$data->appends(["shopname"=>$shopname])->links()}}
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
                $.post("{{route('deleteUnionPay')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            if(data.success){
                                window.location.href = "{{route('unionRestoreIndex')}}";
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