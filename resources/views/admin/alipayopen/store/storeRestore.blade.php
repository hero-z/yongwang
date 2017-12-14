@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('restore')
    <div class="ibox-content">
        <div class="row">
            <form action="{{route('storeRestoreSearch')}}" method="post">
                <div class="col-sm-3">
                    <div class="input-group">
                        <input placeholder="请输入门店名称" class="input-sm form-control" type="text" name="shopname"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                    </div>
                </div>
                {{csrf_field()}}
            </form>
        </div>
        <form action="{{route("storeRestore")}}" method="post">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>商户id</th>
                        <th>企业名称</th>
                        <th>门店名称</th>
                        <th>地址</th>
                        <th>联系方式</th>
                        <th>归属员工</th>
                        <th>状态</th>
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
                            <td><span class="pie">{{$v->licence_name}}</span></td>
                            <td><span class="pie">{{$v->main_shop_name}}</span></td>
                            <td>{{$v->address}}</td>
                            <td><span class="pie">{{$v->contact_number}}</span></td>
                            <td><span class="pie">{{$v->name}}</span></td>
                            @if($v->apply_id=="")
                                <td>
                                    <button type="button" class="btn btn-outline btn-warning">未提交到口碑
                                    </button>
                                </td>
                            @endif
                            @if($v->apply_id&&$v->audit_status=="")
                                <td>
                                    <button type="button" class="btn btn-outline btn-warning">审核中
                                    </button>
                                </td>
                            @endif
                            @if($v->audit_status=='AUDITING')
                                <td>
                                    <button type="button" class="btn btn-outline btn-warning">审核中
                                    </button>
                                </td>
                            @endif
                            @if($v->audit_status=='AUDIT_FAILED')
                                <td>
                                    <button type="button" onclick="info('{{$v->store_id}}')"
                                            class="btn btn-outline btn-danger">审核驳回
                                    </button>
                                </td>
                            @endif

                            @if($v->audit_status=='AUDIT_SUCCESS')
                                <td>
                                    <button type="button" class="btn btn-outline btn-success">开店成功
                                    </button>
                                </td>
                            @endif
                            @if($v->audit_status=='AUDIT_FAILED'||$v->apply_id=="")
                                <th>
                                    <a href="{{url('/admin/alipayopen/storeRestoree?id='.$v->id)}}">
                                        <button type="button" class="btn btn-info">还原</button>
                                    </a>
                                {{-- <a href="{{url('admin/alipayopen/skm?id='.$v['id'])}}">
                                         <button type="button" class="btn  btn-sm">商家门店收款码</button></a>
                                     <a href="">
                                         <button type="button" class="btn">固定金额收款码</button></a>--}}
                                    <button onclick='del("{{$v->id}}")' class="btn btn-danger" type="button">彻底删除</button>
                                </th>
                            @elseif($v->shop_id)
                                <th>
                                    <a href="{{url('/admin/alipayopen/storeRestoree?id='.$v->id)}}">
                                        <button type="button" class="btn btn-info">还原</button>
                                    </a>
                                    <button onclick='del("{{$v->id}}")' class="btn btn-danger" type="button">彻底删除</button>
                                </th>
                            @endif
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
                $.post("{{route('delShop')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            if(data.success){
                                window.location.href = "{{Route('restoreIndex')}}";
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