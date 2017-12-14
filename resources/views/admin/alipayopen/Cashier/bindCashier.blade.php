@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <script src="{{asset('/jQuery-File-Upload/js/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js//jquery.iframe-transport.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js/jquery.fileupload.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" href="{{asset('/amazeui/assets/css/amazeui.datetimepicker.css')}}"/>
    <script src="{{asset('/amazeui/assets/js/locales/amazeui.datetimepicker.zh-CN.js')}}"></script>
    <script src="{{asset('/amazeui/assets/js/amazeui.datetimepicker.js')}}"></script>
    <script src="{{asset('/amazeui/assets/js/amazeui.datetimepicker.min.js')}}"></script>
    <link href="{{asset('/css/font-awesome.css?v=4.4.0')}}" rel="stylesheet">
    <link href="{{asset('/css/plugins/iCheck/custom.css')}}" rel="stylesheet">
    <link href="{{asset('/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
    <link href="{{asset('/css/plugins/colorpicker/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('/css/plugins/cropper/cropper.min.css')}}" rel="stylesheet">
    <link href="{{asset('/css/plugins/switchery/switchery.css')}}" rel="stylesheet">
    <link href="{{asset('/css/plugins/jasny/jasny-bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('/css/plugins/nouslider/jquery.nouislider.css')}}" rel="stylesheet">
    <link href="{{asset('/css/plugins/datapicker/datepicker3.css')}}" rel="stylesheet">
    <link href="{{asset('/css/plugins/ionRangeSlider/ion.rangeSlider.css')}}" rel="stylesheet">
    <link href="{{asset('/css/plugins/ionRangeSlider/ion.rangeSlider.skinFlat.css')}}" rel="stylesheet">
    <link href="{{asset('/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css')}}" rel="stylesheet">
    <link href="{{asset('/css/plugins/clockpicker/clockpicker.css')}}" rel="stylesheet">
    <link href="{{asset('/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('/css/style.css?v=4.1.0')}}" rel="stylesheet">

    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>绑定收银员</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            {{csrf_field()}}

                            <div class="form-group">
                                <label>店铺名称</label>
                                <input msg="" placeholder="" class="form-control"
                                           name="store" id="store" type="text" value="{{$store_name}}" aria-required="true">
                            </div>
                            <input type="hidden" name="store_id" id="store_id" value="{{$store_id}}" aria-required="true">
                            @if($first=="w")
                                <input type="hidden" name="desc_pay" id="desc_pay" value="官方微信支付">
                                <input type="hidden" name="store_type" id="store_type" value="weixin">
                            @endif
                            @if($first=="o")
                                <input type="hidden" name="desc_pay" id="desc_pay" value="支付宝当面付">
                                <input type="hidden" name="store_type" id="store_type" value="oalipay">
                            @endif
                            @if($first=="s")
                                <input type="hidden" name="desc_pay" id="desc_pay" value="支付宝口碑">
                                <input type="hidden" name="store_type" id="store_type" value="salipay">
                            @endif
                            @if($first=="p")
                                <input type="hidden" name="desc_pay" id="desc_pay" value="平安通道">
                                <input type="hidden" name="store_type" id="store_type" value="pingan">
                            @endif
                            @if($first=="f")
                                <input type="hidden" name="desc_pay" id="desc_pay" value="浦发通道">
                                <input type="hidden" name="store_type" id="store_type" value="pufa">
                            @endif
                            @if($first=="b")
                                <input type="hidden" name="desc_pay" id="desc_pay" value="微众通道">
                                <input type="hidden" name="store_type" id="store_type" value="webank">
                            @endif
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="font-noraml">绑定收银员</label>
                                <div class="input-group">
                                    <select data-placeholder="选择店铺..." class="chosen-select" style="width:350px;" tabindex="2" name="cashier" id="cashier">
                                        <option value="">请选择收银员</option>
                                        @foreach($cashier as $v)
                                        <option value="{{$v->id}}" hassubinfo="true">{{$v->name}}</option>
                                         @endforeach
                                    </select>
                                </div>
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



@section('js')
    <script>
        function addpost() {
            $.post("{{route('bindCashier')}}",
                    {
                        _token: '{{csrf_token()}}',
                        store_name: $("#store").val(),
                        store_id:$("#store_id").val(),
                        merchant_id: $("#cashier").val(),
                        desc_pay: $("#desc_pay").val(),
                        store_type:$("#store_type").val()
                    },
                    function (result) {
                        if (result.success==1) {
                            //询问框
                            layer.confirm('绑定成功', {
                                btn: ['确定'] //按钮
                            });
                        } else {
                            layer.msg(result.sub_msg);
                        }
                    }, "json")

        }

    </script>

    <!-- 全局js -->
    <script src="{{asset('js/jquery.min.js?v=2.1.4')}}"></script>
    <script src="{{asset('js/bootstrap.min.js?v=3.3.6')}}"></script>

    <!-- 自定义js -->
    <script src="{{asset('js/content.js?v=1.0.0')}}"></script>

    <!-- Chosen -->
    <script src="{{asset('js/plugins/chosen/chosen.jquery.js')}}"></script>

    <!-- JSKnob -->
    <script src="{{asset('js/plugins/jsKnob/jquery.knob.js')}}"></script>

    <!-- Input Mask-->
    <script src="{{asset('js/plugins/jasny/jasny-bootstrap.min.js')}}"></script>

    <!-- Data picker -->
    <script src="{{asset('js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>

    <!-- Prettyfile -->
    <script src="{{asset('js/plugins/prettyfile/bootstrap-prettyfile.js')}}"></script>

    <!-- NouSlider -->
    <script src="{{asset('js/plugins/nouslider/jquery.nouislider.min.js')}}"></script>

    <!-- Switchery -->
    <script src="{{asset('js/plugins/switchery/switchery.js')}}"></script>

    <!-- IonRangeSlider -->
    <script src="{{asset('js/plugins/ionRangeSlider/ion.rangeSlider.min.js')}}"></script>

    <!-- iCheck -->
    <script src="{{asset('js/plugins/iCheck/icheck.min.js')}}"></script>

    <!-- MENU -->
    <script src="{{asset('js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>

    <!-- Color picker -->
    <script src="{{asset('js/plugins/colorpicker/bootstrap-colorpicker.min.js')}}"></script>

    <!-- Clock picker -->
    <script src="{{asset('js/plugins/clockpicker/clockpicker.js')}}"></script>

    <!-- Image cropper -->
    <script src="{{asset('js/plugins/cropper/cropper.min.js')}}"></script>

    <script src="{{asset('js/demo/form-advanced-demo.js')}}"></script>

@endsection
@endsection