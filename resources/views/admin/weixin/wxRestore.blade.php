@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('restore')
    <div class="ibox-content">
        <div class="row">
            <form action="{{route('searchW')}}" method="post">
                <div class="col-sm-3">
                    <div class="input-group">
                        <input placeholder="请输入店铺名称" class="input-sm form-control" type="text" name="shopname"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                    </div>
                </div>
                {{csrf_field()}}
            </form>
        </div>
        <form action="{{route("wxRestoree")}}" method="post">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>店铺ID</th>
                        <th>店铺名称</th>
                        <th>商户号</th>
                        <th>更新时间</th>
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
                            <td>{{$v->store_name}}</td>
                            <td>{{$v->mch_id}}</td>
                            <td>{{$v->created_at}}</td>
                            <td>
                                <a href="{{url('/admin/weixin/wxRestoreee?id='.$v->id)}}"> <button class="btn btn-primary" type="button">还原</button></a>
                                <button onclick='del("{{$v->id}}")' class="btn btn-danger" type="button">彻底删除</button>
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
    <script type="text/javascript">
        function del(id) {
            //询问框
            // alert(id);
            layer.confirm('确定要彻底删除', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.get("{{route('deleteWx')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            if(data.success){
                                window.location.href = "{{Route('wxRestore')}}";
                            }else{
                                layer.msg("删除失败,没有权限")
                            }

                        }, "json");
            }, function () {

            });
        }
    </script>
@endsection
@section('js')
@endsection