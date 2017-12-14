@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>创建问题帮助</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form role="form" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>问题标题</label>
                                <input placeholder="请输入问题标题" value="" class="form-control"
                                       id="title" name="title"
                                       type="text" required>
                            </div>
                            <div class="form-group">
                                <label>问题简述</label>
                                <input  placeholder="请输入问题简述" value=""
                                        class="form-control" id="summary"
                                        name="summary"
                                        type="text" required>
                            </div>
                            <div class="form-group">
                                <label>问题详情</label>
                                <textarea  id="content" name="content" style="min-height: 100px"  placeholder="请输入问题详情" class="form-control" name="alipayrsaPublicKey"  type="text"></textarea>
                            </div>
                            <div>
                                <button onclick="addpost()" class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                        type="button" >
                                    <strong>保存</strong>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>

        function addpost() {
            $.post("{{route("createQuestions")}}",
                    {
                        _token: '{{csrf_token()}}',
                        title:$("#title").val(),
                        summary: $("#summary").val(),
                        content: $("#content").val(),
                    },
                    function (result) {
                        if (result.status == 1) {
                            layer.alert('保存成功', {icon: 6});
                        }
                    }, "json")
        }
    </script>
@endsection