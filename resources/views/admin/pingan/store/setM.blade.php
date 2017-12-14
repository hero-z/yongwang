@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{$store->name}}-商户费率设置</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            {{csrf_field()}}
                            <input type="hidden" id="id" value="{{$store->id}}">
                            <div class="form-group">
                                <label>商户终端费率</label>
                                <input required placeholder="商户终端费率，不得超过总费率，不得低于成本费率" class="form-control" name="merchant_rate" value="{{$store->merchant_rate}}" id="merchant_rate"
                                       type="text">
                            </div>

                            <div>
                                <button class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                        type="button" onclick="addpost()">
                                    <strong>保存</strong>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-4">
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <i class="fa fa-warning"></i>  {{$store->name}}在平安银行的实时费率为:{{$m}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')
    <script>
        function addpost() {
            $.post("{{route("setMerchantRatePost")}}",
                    {
                        _token: '{{csrf_token()}}',
                        merchant_rate: $("#merchant_rate").val(),
                        id:$("#id").val()
                    },
                    function (result) {
                        if (result.success) {
                            //询问框
                            layer.confirm('{{$store->name}}的费率设置成功，费率为:'+$("#merchant_rate").val(), {
                                btn: ['确定'] //按钮
                            }, function () {
                                window.location.href = "{{route('PingAnStoreIndex')}}";
                            });
                        } else {
                            layer.msg(result.error_message);
                        }
                    }, "json")
        }

    </script>
@endsection
@endsection