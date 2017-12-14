@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>添加分店</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="{{route('ms_BranchAdd')}}" method="post">

                            <div class="form-group">
                                <label>分店商铺简称</label>
                                <input class="form-control" type="text" value="" required="required" name="store_short_name" id="store_short_name">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系人:</label>
                                <input class="form-control" type="text" value="" required="required" name="store_user" id="store_user">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系方式:</label>
                                <input class="form-control" type="text" value="" required="required" name="store_phone" id="store_phone">
                            </div>
<input type='hidden' name='store_id' value='{{$store_id}}' id='store_id'>

                            <div>
                                <button id='tijiao' class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                        type="button">
                                    <strong>保存</strong>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(function(){
            $('#tijiao').click(function(){
                $.post("{{route('ms_BranchAdd')}}", {
                        pid: $("#store_id").val(),//主店的store_id
                        store_short_name: $("#store_short_name").val(),
                        store_user: $("#store_user").val(),
                        store_phone: $("#store_phone").val(),
                        _token: '{{csrf_token()}}'
                    }, function (data) {
                        if (data.status == 1) {
                            setTimeout(function(){location.href=data.url}, 2000);
                        }
                        alert(data.message);
                    }, "json");
            })
        });
    </script>
@section('js')
@endsection
@endsection