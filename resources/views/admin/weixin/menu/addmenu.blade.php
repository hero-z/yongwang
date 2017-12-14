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
    <style type="text/css">
        /* 图片展示样式 */
        .images_zone {
            position: relative;
            width: 120px;
            height: 120px;
            overflow: hidden;
            float: left;
            margin: 3px 5px 3px 0;
            background: #f0f0f0;
            border: 5px solid #f0f0f0;
        }

        .images_zone span {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
            width: 120px;
            height: 120px;
        }

        .images_zone span img {
            width: 120px;
            vertical-align: middle;
        }

        .images_zone a {
            text-align: center;
            position: absolute;
            bottom: 0px;
            left: 0px;
            background: rgba(255, 255, 255, 0.5);
            display: block;
            width: 100%;
            height: 20px;
            line-height: 20px;
            display: none;
            font-size: 12px;
        }

        /* 进度条样式 */
        .up_progress, .up_progress1, .up_progress2, .up_progress3, .up_progress4, .up_progress5, .up_progress6, .up_progress7, .up_progress8 {
            width: 300px;
            height: 13px;
            font-size: 10px;
            line-height: 14px;
            overflow: hidden;
            background: #e6e6e6;
            margin: 5px 0;
            display: none;
        }

        .up_progress .progress-bar, .up_progress1 .progress-bar1, .up_progress2 .progress-bar2, .up_progress3 .progress-bar3, .up_progress4 .progress-bar4, .up_progress5 .progress-bar5, .up_progress6 .progress-bar6, .up_progress7 .progress-bar7, .up_progress8 .progress-bar8 {
            height: 13px;
            background: #11ae6f;
            float: left;
            color: #fff;
            text-align: center;
            width: 0%;
        }
    </style>


    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>添加菜单</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>菜单名称</label>
                                <input  placeholder="注意字数限制,超额的会用...代替" class="form-control"
                                       name="name" id="name" required="required" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>菜单链接</label>
                                <input  placeholder="例如:https://isv.umxnt.com/admin/alipayopen" class="form-control"
                                        name="url" id="url" required="required" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <input type="hidden" name="id" id="id" value="{{isset($_GET['id'])?$_GET['id']:''}}">
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
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>部分菜单Url参考列表</h5>
            </div>
            <div class="ibox-content">
                <lable style="display: block">注意事项:</lable>
                <lable style="display: block">1、菜单都要填写Url，当添加有启用项的子菜单时母菜单Url不起作用。</lable>
                <lable style="display: block">2、以下只是部分参考，若果想换其他功能，请确保Url能正确访问</lable>
            </div>

            <div class="ibox-content">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>功能</th>
                        <th>Url</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>官方微信收款码</td>
                        <td>{{url('/merchant/PayCodeQr?type=weixin')}}</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>支付宝(当面付)收款码</td>
                        <td>{{url('/merchant/PayCodeQr?type=oalipay')}}</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>平安收款码</td>
                        <td>{{url('/merchant/PingAnQr')}}</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>银联收款码</td>
                        <td>{{url('/merchant/UnionPayFixed')}}</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>浦发收款码</td>
                        <td>{{url('/merchant/PayCodeQr?type=pufa')}}</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>收款记录</td>
                        <td>{{url('/merchant/orderlists')}}</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>商户后台</td>
                        <td>{{url('/merchant/index')}}</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>固定金额二维码</td>
                        <td>{{route('choosePayWay')}}</td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>公众号版流水查询</td>
                        <td>{{route('newMobileOrderlists')}}</td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>支付宝花呗分期（二维码）</td>
                        <td>{{route('alipayhbfq')}}</td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

@section('js')
    <script>
        function addpost() {
            if(!$("#name").val()){
                layer.msg('菜单名称必填!');
                $("#name").focus();
            }else if(!$("#url").val()){
                layer.msg('Url必填!');
                $("#url").focus();
            }else if(!checkurl($("#url").val())){
                layer.msg('请填写正确的url!');
                $("#url").focus();
            }else{

                $.post("{{route('WechatMenuAddpost')}}",
                        {
                            _token: '{{csrf_token()}}',

                            id: $("#id").val(),
                            name: $("#name").val(),
                            url:$("#url").val()
                        },
                        function (result) {
                            if (result.errcode==0) {
                                if(result.pid!=0)
                                    window.location.href = "{{route('WxAppMenuSubList',['id'=>isset($_GET['id'])?$_GET['id']:''])}}";
                                else
                                    window.location.href = "{{route('WxAppMenuList')}}";
                            } else {
                                layer.msg(result.errmsg);
                            }
                        }, "json")
            }


        }
        function checkurl(url) {
            var pattern = /^(http|ftp|https):\/\/(\w+\.\w+.\w+)/;
            return pattern.test(url);
        }
    </script>

    <!-- layerDate plugin javascript -->
    <script src="{{asset('js/plugins/layer/laydate/laydate.js')}}"></script>

    <!-- 全局js -->
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>

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
    {{--<script src="{{asset('js/plugins/nouslider/jquery.nouislider.min.js')}}"></script>--}}

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







