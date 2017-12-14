@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('restore')
    <div class="ibox-content">
        <form action="{{route("RestoreNewLandBranchs")}}" method="post">
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
                            <td>{{$v['store_id']}}</td>
                            <td>{{$v['store_name']}}</td>
                            <td>{{$v['merc_id']}}</td>
                            <td>{{$v['created_at']}}</td>
                            <td>
                                <a href="{{url('/admin/newland/RestoreNewLandBranch?id='.$v['id'])}}"> <button class="btn btn-primary" type="button">还原</button></a>
                                <button onclick='del("{{$v['id']}}")' class="btn btn-danger" type="button">彻底删除</button>
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
                $.get("{{route('deleteNewLandBranch')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            if(data.success){
                                window.location.href = "{{Route('NewLandBranchRestore')}}";
                            }else{
                                layer.msg(data.message);
                            }

                        }, "json");
            }, function () {

            });
        }
    </script>
@endsection
@section('js')
@endsection