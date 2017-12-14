@extends('layouts.amaze')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')
    <!-- 内容区域 -->
    <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
        <div class="widget am-cf">
            <div class="widget-head am-cf">
                <div class="widget-title am-fl">打印机设备列表</div>
                <div class="widget-function am-fr">
                    <a href="javascript:;" class="am-icon-cog"></a>
                </div>
            </div>
            <div class="widget-head am-cf">
                <a href="{{route("setMerchine")}}"><button type="button" class="am-btn am-btn-secondary">添加易联云</button></a>
                <a href="{{route("setUprint")}}"><button type="button" class="am-btn am-btn-default">添加U印云</button></a>
            </div>
            <div class="widget-body  widget-body-lg am-fr">

                <table width="100%" class="am-table am-table-compact am-table-striped tpl-table-black "
                       id="example-r">
                    <thead>
                        <tr>
                            <th>设备名称</th>
                            <th>绑定店铺id</th>
                            <th>绑定店铺名称</th>
                            <th>机器号</th>
                            <th>密钥</th>
                            <th>手机号</th>
                            <th>绑定时间</th>
                            <th>类型</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($list as $v)
                        <tr class="gradeX">

                            <td>{{$v->mname}}</td>
                            <td>{{$v->store_id}}</td>
                            <td>{{$v->store_name}}</td>
                            <td>{{$v->machine_code}}</td>
                            <td>{{$v->msign}}</td>
                            <td>{{$v->phone}}</td>
                            <td>{{$v->created_at}}</td>
                            <td>
                                @if($v->type=="yilainyun")
                                    易联云
                                @endif
                                @if($v->type=="Uprint")
                                    U印云
                                @endif
                            </td>

                        </tr>
                     @endforeach
                    <!-- more data -->
                    </tbody>
                </table>

                <ul class="am-pagination">
                    {{$list->links()}}
                </ul>
            </div>
        </div>
    </div>
@endsection