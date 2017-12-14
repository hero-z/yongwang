@extends('layouts.public')
@section('content')
    <div class="col-sm-12">
        <button type="button" onclick="qr()" class="btn btn-w-m btn-success">生成空码</button>
@include('minsheng.common.comlabel')
 <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>浦发二维码列表</h5>
            </div>
            <div class="ibox-content">

                <table class="table" style="Word-break: break-all;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>生成用户</th>
                        <th>收款类型</th>
                        <th>生成数量</th>
                      {{--  <th>已经使用</th>--}}
                        <th>生成时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($lists as $v)
                        <tr>
                            <td>{{$v->id}}</td>
                            <td>{{$v->user_name}}</td>
                            <td>{{$v->from_info}}</td>
                            <td>{{$v->num}}</td>
                          {{--  <td>{{$v->s_num}}</td>--}}
                            <td>{{$v->created_at}}</td>
                            <td>
                                <a href="{{url('/admin/pufa/DownloadQr?cno='.$v->cno)}}" target="_self">
                                    <button type="button"
                                            class="btn btn-primary btn-rounded">下载空码
                                    </button>
                                </a>

                            </td>
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
                    {{ $lists->links() }}
                </div>
            </div>
        </div>
        @endsection
        @section('js')
            <script>
                function qr() {
                    var index = layer.load(1, {
                        shade: [0.1, '#fff'] //0.1透明度的白色背景
                    });
                    $.post("{{route('ms_createQr')}}", {_token: "{{csrf_token()}}"},
                            function (data) {
                                if (data.status == 1) {
                                    layer.confirm('生成二维码成功！', {
                                        btn: ['确定'] //按钮
                                    }, function () {
                                        window.location.href = "{{route('ms_QrLists')}}"
                                    });
                                } else {
                                    layer.msg(data.msg, {icon: 5});
                                }
                            }, "json");
                }
            </script>
@endsection