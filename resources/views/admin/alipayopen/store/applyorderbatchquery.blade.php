@extends('layouts.public')
@section('content')


    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>门店业务流水
                <small>通过该接口分页查询Leads、门店、商品相关操作流水信息</small>
            </h5>
        </div>
        <div class="ibox-content">

            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper form-inline" role="grid">
                <table class="table table-striped table-bordered table-hover dataTables-example dataTable"
                       id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
                    <thead>
                    <tr role="row">
                        <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 189px;" aria-label="渲染引擎：激活排序列升序" aria-sort="ascending">操作
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 333px;" aria-label="浏览器：激活排序列升序">支付宝流水id
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 308px;" aria-label="平台：激活排序列升序">店铺名称
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 142px;" aria-label="引擎版本：激活排序列升序">创建时间
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 144px;" aria-label="CSS等级：激活排序列升序">更新时间
                        </th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                            style="width: 144px;" aria-label="CSS等级：激活排序列升序">响应状态
                        </th>
                    </tr>
                    </thead>
                    <tbody id="appends">

                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="dataTables_info" id="DataTables_Table_0_info" role="alert" aria-live="polite"
                             aria-relevant="all">显示 1 到 20 项，共 1 项
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
                            <ul class="pagination">
                                <li class="paginate_button previous" aria-controls="DataTables_Table_0" tabindex="0"
                                    id="DataTables_Table_0_previous"><a href="#">上一页</a></li>
                                <li class="paginate_button " aria-controls="DataTables_Table_0" tabindex="0"><a
                                            href="#">1</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('js')

    <script>
        window.onload = get;
        function get(){
            getinfo();
        }
        function getinfo() {
            $.post("{{route("ApplyOrderBatchQuery")}}", {_token:"{{csrf_token()}}"}, function (data) {

                for (var key in data) {
                    var selObj = $("#appends");
                    selObj.append(
                            "<tr class='gradeA odd' >"+
                            "<td class=''>"+data[key].action+"</td>" +
                            "<td class=''>"+data[key].apply_id+"</td>" +
                            "<td class=''>"+data[key].biz_id+"</td>" +
                            "<td class=''>"+data[key].create_time+"</td>" +
                            "<td class=''>"+data[key].update_time+"</td>" +
                            "<td class=''>"+data[key].result_code+"</td>"+
                            "</tr>"
                          );
                }
            }, "json");
        }
    </script>
@endsection