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
                <h5>添加卡券</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="{{route('createAlipass')}}" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>卡券类型</label>
                                <div class="col-sm-10">
                                    <select class="form-control m-b"  id="type" name="type" required="required" onchange="change()">
                                        <option value="discount">折扣券</option>
                                        <option value="fto">满减券</option>
                                        <option value="free">免费券</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="discount1" style="display:none">
                                <label>折扣</label>
                                <input msg="卡券活动描述" placeholder="请输入0到10之间的折扣数值,最多精确到小数点后一位" class="form-control"
                                       name="discount" id="discount" type="number" step="0.1" min="1" max="9">
                            </div>
                            <div class="form-group" id="fto1" style="display:none">
                                <label class="font-noraml">设置满减</label>
                                    <input type="number" class="input-sm form-control" name="full" id="full" placeholder="请输入大于1的整数" />
                                    <span class="input-group-addon">减</span>
                                    <input type="number" class="input-sm form-control" name="reduce" id="reduce" placeholder="请输入大于0的数字,最多精确到小数点后两位" />
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>卡券张数</label>
                                <input msg="卡券张数" placeholder="请输入阿拉伯数字" class="form-control"
                                       name="number" id="number" type="number" step="1" min="1">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">开始日期</label>
                                <div class="col-sm-10">
                                    <input class="form-control layer-date" placeholder="YYYY-MM-DD hh:mm:ss" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" id="start" name="start">
                                </div>
                            </div>


                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">结束日期</label>
                                <div class="col-sm-10">
                                    <input class="form-control layer-date" placeholder="YYYY-MM-DD hh:mm:ss" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" id="end" name="end">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>卡券标题</label>
                                <input msg="卡券标题" placeholder="给卡券一个标题,方便自己查看" class="form-control"
                                       name="title" id="title" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>卡券状态描述</label>
                                <input msg="卡券状态描述" placeholder="如:可使用" class="form-control"
                                       name="status" id="status" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>卡券活动描述</label>
                                <input msg="卡券活动描述" placeholder="如:凭此券可享受8.5折优惠" class="form-control"
                                       name="strip" id="strip" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>卡券详情描述</label>
                                <textarea class="form-control" rows="6" placeholder="如:1.该优惠有效期：截止至2014年06月18日；2.凭此券可以享受以下优惠：享门市价8.5折优惠不与其他优惠同享。详询商家。" name="description" id="description"></textarea>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>门店地址</label>
                                <input msg="门店地址" placeholder="选填" class="form-control"
                                       name="address" id="address" type="text">
                            </div>

                            <div class="hr-line-dashed"></div>




                            <div class="form-group">
                                <label class="font-noraml">绑定店铺</label>
                                <div class="input-group">
                                    <select data-placeholder="选择店铺..." class="chosen-select" style="width:350px;" tabindex="2" name="store" id="store">
                                        <option value="">请选择店铺</option>
                                        @foreach($list as $v)
                                        <option value="{{$v->app_auth_token}}" hassubinfo="true">{{$v->auth_shop_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="font-noraml">卡券背景颜色</label>
                                <div class="input-group colorpicker-demo3 colorpicker-element">
                                    <input value="" class="form-control" type="text" name="backgroundColor" id="backgroundColor">
                                    <span class="input-group-addon"><i style="background-color: rgb(91, 28, 151);"></i></span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">

                                <script src="{{asset('uploadify/jquery.uploadify.min.js')}}"
                                        type="text/javascript"></script>
                                <link rel="stylesheet" type="text/css" href="{{asset('uploadify/uploadify.css')}}">

                                <input type="hidden" required="required" size="50"  name="logo" id="logo">
                                <!-- 图片上传按钮 -->
                                <label>卡券logo</label>
                                <input id="fileupload" type="file" name="image" data-url="{{route('uploads')}}"
                                       data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress">
                                    <div class="progress-bar"></div>
                                </div>
                                <div style="clear:both;"></div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>海报图片</label>
                                <input type="hidden" required="required" size="50" name="strip_image" id="strip_image">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload1" type="file" name="image" data-url="{{route('uploads')}}"
                                       data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files1"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress1">
                                    <div class="progress-bar1"></div>
                                </div>
                                <div style="clear:both;"></div>
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
    <script type="text/JavaScript">
        window.onload=function(){
            var a=$("#type").val();
            if(a=="discount"){
                $("#fto1").css("display","none");
                $("#discount1").css("display","block");
                $("#full").val('');
                $("#reduce").val('');
            }
            if(a=="fto"){
                $("#discount1").css("display","none");
                $("#fto1").css("display","block");
                $("#discount").val('');
            }
            if(a=="free"){
                $("#discount1").css("display","none");
                $("#fto1").css("display","none");
                $("#full").val('');
                $("#reduce").val('');
                $("#discount").val('');
            }
        }
    </script>
    <script type="text/javascript">
       function change(){
           var a=$("#type").val();
           if(a=="discount"){
               $("#fto1").css("display","none");
              $("#discount1").css("display","block");
               $("#full").val('');
               $("#reduce").val('');
           }
           if(a=="fto"){
               $("#discount1").css("display","none");
               $("#fto1").css("display","block");
               $("#discount").val('');
           }
           if(a=="free"){
               $("#discount1").css("display","none");
               $("#fto1").css("display","none");
               $("#full").val('');
               $("#reduce").val('');
               $("#discount").val('');
           }
       }
    </script>
    <script>
        function addpost() {
            $.post("{{route('createAlipass')}}",
                    {
                        _token: '{{csrf_token()}}',
                        type: $("#type").val(),
                        title:$("#title").val(),
                        status: $("#status").val(),
                        startDate: $("#start").val(),
                        endDate: $("#end").val(),
                        strip: $("#strip").val(),
                        logo:$("#logo").val(),
                        strip_image:$("#strip_image").val(),
                        description:$("#description").val(),
                        address:$("#address").val(),
                        store:$("#store").val(),
                        number:$("#number").val(),
                        full:$("#full").val(),
                        reduce:$("#reduce").val(),
                        discount:$("#discount").val(),
                        backgroundColor:$("#backgroundColor").val()
                    },
                    function (result) {
                        if (result.success==1) {
                            //询问框
                            layer.confirm('保存成功', {
                                btn: ['列表页', '当前页'] //按钮
                            }, function () {
                                window.location.href = "{{route('adIndex')}}";
                            }, function () {
                                layer.msg('正在浏览提交的广告详情');
                            });
                        } else {
                            layer.msg(result.sub_msg);
                        }
                    }, "json")

        }

    </script>

    <script type="text/javascript">
        publicfileupload("#fileupload", ".files", "#logo", ".up_progress .progress-bar", ".up_progress");
        publicfileupload("#fileupload1", ".files1", "#strip_image", '.up_progress1 .progress-bar1', ".up_progress1");
        function publicfileupload(fileid, imgid, postimgid, class1, class2) {
            //图片上传
            $(fileid).fileupload({
                dataType: 'json',
                add: function (e, data) {
                    var numItems = $('.files .images_zone').length;
                    if (numItems >= 10) {
                        alert('提交照片不能超过3张');
                        return false;
                    }
                    $(class1).css('width', '0px');
                    $(class2).show();
                    $(class1).html('上传中...');
                    data.submit();
                },
                done: function (e, data) {
                    $(class2).hide();
                    $('.upl').remove();
                    var d = data.result;
                    if (d.status == 0) {
                        alert("上传失败");
                    } else {
                        var imgshow = '<div class="images_zone"><input type="hidden" name="imgs[]" value="' + d.image_url + '" /><span><img src="' + d.image_url + '"  /></span><a href="javascript:;">删除</a></div>';
                        jQuery(imgid).append(imgshow);
                        jQuery(postimgid).val(d.image_url);
                    }
                },
                progressall: function (e, data) {
                    console.log(data);
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $(class1).css('width', progress + '%');
                }
            });

            //图片删除
            $(imgid).on({
                mouseenter: function () {
                    $(this).find('a').show();
                },
                mouseleave: function () {
                    $(this).find('a').hide();
                },
            }, '.images_zone');
            $(imgid).on('click', '.images_zone a', function () {
                $(this).parent().remove();
            });
        }
    </script>

    <!-- 全局js -->




    <!-- layerDate plugin javascript -->
    <script src="{{asset('js/plugins/layer/laydate/laydate.js')}}"></script>
    <script>
        //外部js调用
        laydate({
            elem: '#hello', //目标元素。由于laydate.js封装了一个轻量级的选择器引擎，因此elem还允许你传入class、tag但必须按照这种方式 '#id .class'
            event: 'focus' //响应事件。如果没有传入event，则按照默认的click
        });

        //日期范围限制
        var start = {
            elem: '#start',
            format: 'YYYY/MM/DD hh:mm:ss',
            min: laydate.now(), //设定最小日期为当前日期
            max: '2099-06-16 23:59:59', //最大日期
            istime: true,
            istoday: false,
            choose: function (datas) {
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        };
        var end = {
            elem: '#end',
            format: 'YYYY/MM/DD hh:mm:ss',
            min: laydate.now(),
            max: '2099-06-16 23:59:59',
            istime: true,
            istoday: false,
            choose: function (datas) {
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };
        laydate(start);
        laydate(end);
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







