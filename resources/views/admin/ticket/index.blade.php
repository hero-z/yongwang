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
                        <h5>易联云设备列表</h5>
                    </div>

                    <div class="col-sm-3">
                        @permission("addMerchine")
                        <a href="{{route("addMerchine")}}"  class="btn btn-sm btn-success" style="color:white;">添加设备</a>
                        @endpermission
                        <a href="{{route("merchineConfig")}}"  class="btn btn-sm btn-danger" style="color:white;">设备配置</a>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>设备名称</th>
                                    <th>绑定店铺id</th>
                                    <th>绑定店铺名称</th>
                                    <th>机器号</th>
                                    <th>密钥</th>
                                    <th>手机号</th>
                                    <th>创建时间</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                  @foreach($list as $v)
                                        <tr>
                                            <td><span class="pie">{{$v->mname}}</span></td>
                                            <td><span class="pie">{{$v->store_id}}</span></td>
                                            <td><span class="pie">{{$v->store_name}}</span></td>
                                            <td><span class="pie">{{$v->machine_code}}</span></td>
                                            <td><span class="pie">{{$v->msign}}</span></td>
                                            <td><span class="pie">{{$v->phone}}</span></td>
                                            <td><span class="pie">{{$v->created_at}}</span></td>
                                            <td><span class="pie">{{$v->updated_at}}</span></td>
                                                <th>
                                                    @permission("deleteMerchine")
                                                        <button type="button" class="btn btn-info" type="del" onclick="del({{$v->id}})">删除</button>
                                                    @endpermission
                                                    @permission("editMerchine")
                                                    <a href="{{url('/admin/ticket/editMerchine?id='.$v->id)}}">
                                                        <button type="button" class="btn">修改</button>
                                                    </a>
                                                    @endpermission
                                                </th>
                                        </tr>
                                      @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="dataTables_paginate paging_simple_numbers"
                                 id="DataTables_Table_0_paginate">


                         {{$list->links()}}

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script type="text/javascript">
        function del(id) {
            //询问框
            // alert(id);
            layer.confirm('确定要删除吗?', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.get("{{route('deleteMerchine')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            if(data.success){
                                window.location.href = "{{Route('ticketIndex')}}";
                            }else{
                                layer.msg("删除失败，请检查是否有权限")
                            }

                        }, "json");
            }, function () {

            });
        }
    </script>
@endsection
@section('js')

@endsection