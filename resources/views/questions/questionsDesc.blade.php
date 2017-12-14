<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title> - 项目详情</title>
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="shortcut icon" href="favicon.ico">
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
    <link href="{{asset('css/font-awesome.css?v=4.4.0')}}" rel="stylesheet">

    <link href="{{asset('css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('css/style.css?v=4.1.0')}}" rel="stylesheet">

</head>

<body class="gray-bg">
<div class="row">
    <div class="col-sm-12">
        <div class="wrapper wrapper-content animated fadeInUp">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="m-b-md">
                               @permission('editQuestions') <a href="{{url('admin/questions/editQuestions?id='.$list->id)}}" class="btn btn-white btn-xs pull-right">编辑问题</a>@endpermission
                                <h2>{{$list->title}}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5">
                            <dl class="dl-horizontal">

                                <dt>编辑人：</dt>
                                <dd>{{$list->name}}</dd>
                                <dt>问题详情及答疑：</dt>
                                <dd>{{$list->content}}</dd>
                            </dl>
                        </div>
                        <div class="col-sm-7" id="cluster_info">
                            <dl class="dl-horizontal">

                                <dt>最后更新：</dt>
                                <dd>{{$list->updated_at}}</dd>
                                <dt>创建于：</dt>
                                <dd>{{$list->created_at}}</dd>
                            </dl>
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
    $(document).ready(function () {

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
