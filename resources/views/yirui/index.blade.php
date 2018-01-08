@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div id="tipshow">
            <h3>盒子使用步骤：</h3>
            <p>1.后台配置好店铺并绑定收银员</p>
            <p>2.配置盒子被扫地址并生成链接二维码(InspirySpUrlDecode:{{url('api/merchant/init')}})拿到机器上扫一下。</p>
            <p>3.配置盒子通信密码并生成二维码(InspirySpKey:88888)拿到机器上扫一下。</p>
            <p>4.重启盒子，即可！(以上地址生成工具：http://cli.im/)</p>
            <p>5.可能用到的参数,操作步骤参考1-4！
                InspiryQuota:1000(固定金额单位分,1000为样例)
                InspirySpTimeOut:16(盒子等待响应最小时间,单位秒,如果收款频率足够高需要设置比此值更小的值请联系18251828302更新接口内容)
                </p>
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
                                    <th>商户账号id</th>
                                    <th>商户账号名称</th>
                                    <th>商户手机号</th>
                                    <th>设备号</th>
                                    <th>设备名称</th>
                                    <th>通信密钥</th>
                                    <th>创建时间</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($lists as $v)
                                    <tr>
                                        <td><span class="pie">{{$v->m_id}}</span></td>
                                        <td><span class="pie">{{$v->merchant_name}}</span></td>
                                        <td><span class="pie">{{$v->merchant_phone}}</span></td>
                                        <td><span class="pie">{{$v->device_no}}</span></td>
                                        <td><span class="pie">{{$v->name}}</span></td>
                                        <td><span class="pie">{{$v->device_pwd}}</span></td>
                                        <td><span class="pie">{{$v->created_at}}</span></td>
                                        <td><span class="pie">{{$v->updated_at}}</span></td>
                                        <th>
                                            <button type="button" onclick="del({{$v->id}})" class="btn btn-danger">删除</button>
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

                                {{$lists->links()}}

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
                $.post("{{route('paipaidel')}}",{_token:"{{csrf_token()}}",id:id},
                    function (data) {
                        if(data.success){
                            window.location.reload();
                        }else{
                            layer.msg(data.msg)
                        }
                },'json');
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