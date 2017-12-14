@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div id="tipshow">
            <h3>盒子使用步骤：</h3>
            <p>1.后台配置好店铺并绑定收银员</p>
            <p>2.配置盒子被扫地址并生成链接二维码(InspirySpUrlDecode:https://isv.umxnt.com/api/merchant/in)拿到机器上扫一下。</p>
            <p>3.配置盒子通信密码并生成二维码(InspirySpKey:88888)拿到机器上扫一下。</p>
            <p>4.重启盒子，即可！(以上地址生成工具：http://cli.im/)</p>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title" id="tipshowturn">
                        <h5>意锐的派派盒子设备列表</h5>
                    </div>

                    <div class="col-sm-3">
                        <a href="{{route('paipaiadd')}}"  class="btn btn-sm btn-success" style="color:white;">添加设备</a>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>设备名称</th>
                                    <th>设备状态</th>
                                    <th>绑定店铺id</th>
                                    <th>绑定收银员id</th>
                                    <th>设备号</th>
                                    <th>通信密钥</th>
                                    <th>创建时间</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                  @foreach($data as $v)
                                        <tr>
                                            <td><span class="pie">{{$v->name}}</span></td>
                                            <td><span class="pie"><?php if($v->status==1){echo '开启';}else{echo '关闭';} ?></span></td>
                                            <td><span class="pie"><?php if(isset($store_lst[$v->store_id])){echo $store_lst[$v->store_id];}else{ echo $v->store_id; }?></span></td>
                                            <td><span class="pie">{{$v->m_id}}</span></td>
                                            <td><span class="pie">{{$v->device_no}}</span></td>
                                            <td><span class="pie">{{$v->device_pwd}}</span></td>
                                            <td><span class="pie">{{$v->created_at}}</span></td>
                                            <td><span class="pie">{{$v->updated_at}}</span></td>
                                                <th>
                                                    <a href="{{url('admin/yirui/paipai/add?id='.$v->id)}}">
                                                        <button type="button" class="btn">修改</button>
                                                    </a>
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

                         {{$data->links()}}

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



        $('#tipshowturn').click(function(){
            $('#tipshow').hide();
        })
    </script>
@endsection
@section('js')

@endsection