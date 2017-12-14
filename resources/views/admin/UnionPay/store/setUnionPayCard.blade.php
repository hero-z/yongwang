@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{$name}}-商户设置</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            {{csrf_field()}}

                            <input type="hidden" id="id" @if($store)value="{{$store->id}}@endif">

                            <input type="hidden" id="out_merchant_id" name="out_merchant_id" @if($store)value="{{$store->store_id}}@endif">
                            <div class="form-group">
                                <label>银行卡卡号</label>
                                <input required placeholder="银行卡卡号" class="form-control" @if($store)value="{{$store->bank_card_no}}"@endif name="bank_card_no" id="bank_card_no"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>银行卡的开户人姓名</label>
                                <input required="required" placeholder="银行卡的开户人姓名" @if($store)value="{{$store->bank_card_name}}"@endif  class="form-control"
                                       name="bank_card_name" id="bank_card_name"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div>
                                <button class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                        type="button" onclick="addpost()">
                                    <strong>保存</strong>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')
    <script>
        function addpost() {
            $.post("setCard",
                    {
                        _token: '{{csrf_token()}}',
                        bank_card_no: $("#bank_card_no").val()
                        ,
                        bank_card_name: $("#bank_card_name").val(),
                        out_merchant_id: $("#out_merchant_id").val(),
                        id:$("#id").val()
                    },
                    function (result) {
                        if (result.success) {
                            //询问框
                            layer.confirm('保存成功', {
                                btn: ['确定'] //按钮
                            }, function () {
                                window.location.href = "{{route('UnionPayStoreIndex')}}";
                            });
                        } else {
                            layer.msg(result.error_message);
                        }
                    }, "json")
        }

    </script>
@endsection
@endsection