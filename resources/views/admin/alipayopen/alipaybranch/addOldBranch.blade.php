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
                <h5>绑定分店</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            {{csrf_field()}}

                            <input type="hidden" id="pid" value="{{$pid}}" >
                        <input type="hidden" id="type" value="{{$type}}">
                            <div class="form-group">
                                <label class="font-noraml">绑定店铺</label>

                                <div class="input-group">
                                    <select data-placeholder="选择店铺..." class="chosen-select" style="width:350px;" tabindex="2" name="store" id="store" >
                                        <option value="">请选择店铺</option>
                                           @foreach($list as $v)
                                            @if($v->id!=$pid)
                                            <option value="{{$v->id}}" hassubinfo="true">{{$v->name}}</option>
                                            @endif
                                           @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div>
                                <button class="btn btn-sm btn-primary m-t-n-xs"
                                        type="button" onclick="addpost()">
                                    <strong>确认添加</strong>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@section('js')
<script type="text/javascript">
    function addpost(){
               type=$("#type").val();
        $.post("{{route('addOldBranch')}}",
                {
                    _token: '{{csrf_token()}}',
                    id:$("#store").val(),
                    type:$("#type").val(),
                    pid:$("#pid").val()
                },
                function (result) {
                    if (result.success==1) {
                        //询问框
                        layer.confirm('添加成功', {
                            btn: ['列表页', '当前页'] //按钮
                        }, function () {
                            if(type=="ali"){
                                window.location.href = "{{route('oauthlist')}}";
                            }
                            if(type=="weixin"){
                                window.location.href = "{{route('WxShopList')}}";
                            }
                            if(type=="pingan"){
                                window.location.href = "{{route('PingAnStoreIndex')}}";
                            }
                            if(type=="sali"){
                                window.location.href = "{{url('admin/alipayopen/store')}}";
                            }
                            if(type=="pufa"){
                                window.location.href = "{{route('storelist')}}";
                            }
                            if(type=="unionpay"){
                                window.location.href = "{{route('UnionPayStoreIndex')}}";
                            }
                            if(type=="webank"){
                                window.location.href = "{{route('webankindex')}}";
                            }
                        }, function () {
                            layer.msg("浏览当前页");
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







