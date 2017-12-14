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
                        <h5>权限列表</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>权限名称</th>
                                    <th>权限说明</th>
                                    <th>权限描述</th>
                                  {{--  <th>创建时间</th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $v)
                                    <tr>
                                        <td>{{$v->id}}</td>
                                        <td><span class="pie">{{$v->name}}</span></td>
                                        <td><span class="pie">{{$v->display_name}}</span></td>
                                        <td>{{$v->description}}</td>
                                        {{--<td><span class="pie">{{$v->created_at}}</span></td>--}}
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
                                {{ $data->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('js')
@endsection