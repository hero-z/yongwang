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
                        <form action="{{route('BranchAdd')}}" method="post">

                            <input type="hidden" id="pid" name="pid" value="<?php echo $_GET['pid']?>">
                            <div class="form-group">
                                <label>分店商铺简称</label>
                                <input class="form-control" type="text" value="" required="required" name="merchant_short_name" id="merchant_short_name">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系人:</label>
                                <input class="form-control" type="text" value="" required="required" name="shop_user" id="shop_user">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系方式:</label>
                                <input class="form-control" type="text" value="" required="required" name="bank_tel" id="bank_tel">
                            </div>


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
                $.post("{{route('BranchAdd')}}", {
                        pid: $("#pid").val(),
                        merchant_short_name: $("#merchant_short_name").val(),
                        shop_user: $("#shop_user").val(),
                        bank_tel: $("#bank_tel").val(),
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