<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title> - 项目</title>
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="shortcut icon" href="favicon.ico">
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
    <link href="{{asset('css/font-awesome.css?v=4.4.0')}}" rel="stylesheet">

    <link href="{{asset('css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('css/style.css?v=4.1.0')}}" rel="stylesheet">

</head>

<body class="gray-bg">

<div class="wrapper wrapper-content animated fadeInUp">
    <div class="row">
        <div class="col-sm-12">

            <div class="ibox">
                <div class="ibox-title">
                    <h5>所有问题</h5>
                    <div class="ibox-tools">
                    @permission('createQuestions')<a href="{{route('addQuestions')}}" class="btn btn-primary btn-xs">创建问题帮助</a>@endpermission
                    </div>
                </div>
                <div class="ibox-content">

                    <div class="project-list">

                        <table class="table table-hover">
                            <tbody>
                            @foreach($list as $v)
                            <tr>
                                <td class="project-title">
                                    <a href="project_detail.html">{{$v->title}}</a>
                                    <br/>
                                    <small>创建于 {{$v->created_at}}</small>
                                </td>
                                <td class="project-completion">
                                    <small>问题简述： </small>
                                    <div class="project-title">
                                        {{$v->summary}}
                                    </div>
                                </td>
                                <td class="project-actions">
                                    <a href="{{url('admin/questions/questionsDesc?id='.$v->id)}}" class="btn btn-white btn-sm"><i class="fa fa-folder"></i> 查看问题详情 </a>
                                   @permission("editQuestions") <a href="{{url('admin/questions/editQuestions?id='.$v->id)}}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> 编辑 </a>@endpermission
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
                            {{$list->links()}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 全局js -->
<script src="{{asset('js/jquery.min.js?v=2.1.4')}}"></script>
<script src="{{asset('js/bootstrap.min.js?v=3.3.6')}}"></script>


<!-- 自定义js -->
<script src="{{asset('js/content.js?v=1.0.0')}}"></script>


<script>
    $(document).ready(function(){

        $('#loading-example-btn').click(function () {
            btn = $(this);
            simpleLoad(btn, true)

            // Ajax example
//                $.ajax().always(function () {
//                    simpleLoad($(this), false)
//                });

            simpleLoad(btn, false)
        });
    });

    function simpleLoad(btn, state) {
        if (state) {
            btn.children().addClass('fa-spin');
            btn.contents().last().replaceWith(" Loading");
        } else {
            setTimeout(function () {
                btn.children().removeClass('fa-spin');
                btn.contents().last().replaceWith(" Refresh");
            }, 2000);
        }
    }
</script>



</body>
</html>
