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
                <h5>添加子商户</h5>
            </div>
            <div class="ibox-content">
                <h5>请仔细阅读:</h5>
                <label>
                    备注：<a target="_blank" href="https://mp.weixin.qq.com/zh_CN/htmledition/comm_htmledition/res/cardticket/wx_cardticket_assist_agent.pdf">微信卡券商户授权函</a>手填并加盖鲜章后，上传彩色扫描件或彩照。
                </label>
                <label style="display: block;">
                    1、授权函必须加盖企业公章，或个体户店铺章、发票专用章、财务章、合同章等具备法律效力的盖章，不可使用个人私章；
                </label>
                <label style="display: block;">
                    2、若子商户是个体工商户，且无上述公章，授权函可用个体工商户经营者签字代替公章，且须同时额外上传《个体工商户营业执照》及该执照内登记的经营者的身份证彩照。（本方案仅适用于子商户是个体工商户，且无公章的场景。其他场景必须在授权函加盖公章）
                </label>
                <label style="display: block;">
                    3、子商户若有公众号，且不愿意自己运营，通过授权方式让第三方代制，支持配置子商户公众号。配置后，1）该子商户的制券配额不再限制，2）该卡券详情页关联的公众号为子商户配置这个公众号。
                </label>
                <label style="display: block;">
                    4、请确保下面内容填写与《微信卡券商户授权函》内填写的内容保持一致，1）子商户名称（12个汉字内）,可为店铺名、品牌名等,此名称将显示在卡券券面上。2）子商户Logo可为店铺照片或品牌Logo，请确保该Logo与子商户名称保持一致,图片建议尺寸：300像素*300像素，大小不超过5MB。支持.jpg等格式的正方形照片。
                    3）子商户app_id,如果添加，该子商户卡券的页面上将引导关注或展示子商户公众号；若未添加，则仍关注或展示第三方公众号。请在微信公众平台-开发-基本配置内，查看公众号AppID。
                </label>
                <label style="display: block;">
                    5、由于图片上传到本地服务器和微信服务器，所需耗时是本地服务器相应消耗用时和微信服务器相应用时累计和,可能时间较长,为避免重复提交产生不可控后果请耐心的等待(>10s)。
                </label>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>子商户名称</label>
                                <input  placeholder="子商户名称（12个汉字内）,可为店铺名、品牌名等,此名称将显示在卡券券面上" class="form-control"
                                       name="brand_name" id="brand_name" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>子商户Logo</label>
                                <input type="hidden" required="required" size="50" name="logo_url" id="logo_url">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload" type="file" name="image" data-url="{{route('wxcarduploads',['type' => 1])}}"
                                       data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress">
                                    <div class="progress-bar"></div>
                                </div>
                                <div style="clear:both;"></div>
                                <label>图片建议尺寸：300像素*300像素，大小不超过5MB。支持.jpg等格式的正方形照片。</label>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>子商户AppID（选填）</label>
                                <input  placeholder="请在微信公众平台-开发-基本配置内，查看公众号AppID。" class="form-control"
                                       name="app_id" id="app_id" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>子商户类目</label>
                                <div class="col-sm-10">
                                    <select class="m-b" style="width: 150px;" onchange="getsecondcate(this)" id="primary_category_id" name="primary_category_id" required="required">
                                        <option value="0">一级类目</option>
                                        @foreach($category['category'] as $v)
                                            <option value="{{$v['primary_category_id']}}">{{$v['category_name']}}</option>
                                        @endforeach
                                    </select>
                                    <select class=" m-b" style="width: 150px;"  id="secondary_category_id" name="secondary_category_id" required="required">
                                        <option value="0">二级类目</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group"  >
                                <label>授权函</label>
                                <span style="margin-left: 30px">有公章</span>
                                <input type="radio" onclick="checkstatus(this.id)" checked name="officialseal" id="is" checked value="1">
                                <span style="margin-left: 10px">无公章</span>
                                <input type="radio" onclick="checkstatus(this.id)" name="officialseal" id="isnot" value="2">
                            </div>
                            <div class="form-group">
                                <label>微信卡券商户授权函</label>
                                <input type="hidden" required="required" size="50" name="protocol" id="protocol">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload2" type="file" name="image" data-url="{{route('wxcarduploads',['type' => 2])}}"
                                       data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files2"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress2">
                                    <div class="progress-bar2"></div>
                                </div>
                                <div style="clear:both;"></div>

                            </div>
                            <div class="form-group" style="display:none;" id="license">
                                <label>子商户营业执照</label>
                                <input type="hidden" required="required" size="50" name="agreement_media_id" id="agreement_media_id">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload3" type="file" name="image" data-url="{{route('wxcarduploads',['type' => 2])}}"
                                       data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files3"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress3">
                                    <div class="progress-bar3"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="form-group" style="display:none;" id="idcard">
                                <label>子商户身份证</label>
                                <input type="hidden" required="required" size="50" name="operator_media_id" id="operator_media_id">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload4" type="file" name="image" data-url="{{route('wxcarduploads',['type' => 2])}}"
                                       data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files4"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress4">
                                    <div class="progress-bar4"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>授权函有效期截止时间</label>
                                <div class="col-sm-10">
                                    <input class="form-control layer-date" id="end_time" name="end_time" placeholder="授权函有效期截止时间" value="" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})">
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
        var id=$("[name='officialseal']:checked").attr('id');
        var type=$("[name='officialseal']:checked").val();
        checkstatus(id);
        function addpost() {
            if(!$("#brand_name").val()){
                layer.msg('子商户名称必填!');
                $("#brand_name").focus();
            }else if(!$("#logo_url").val()){
                layer.msg('子商户Logo必填!');
                $("#logo_url").focus();
            }else if($("#primary_category_id").val()==0){
                layer.msg('子商户一级类目必填!');
                $("#primary_category_id").focus();
            }else if($("#secondary_category_id").val()==0){
                layer.msg('子商户二级类目必填!');
                $("#secondary_category_id").focus();
            }else if(!$("#protocol").val()){
                layer.msg('微信商户授权函必填!');
                $("#protocol").focus();
            }else if(id=='isnot'){
                if(!$('#agreement_media_id').val()){
                    layer.msg('子商户营业执照必填!');
                    $("#agreement_media_id").focus();
                }else if(!$('#operator_media_id').val()){
                    layer.msg('子商户身份证必填!');
                    $("#operator_media_id").focus();
                }
            }else if(!$("#end_time").val()){
                layer.msg('授权截止时间必填!');
                $("#end_time").focus();
            }else{

                $.post("{{route('postsubMerchantdata')}}",
                        {
                            _token: '{{csrf_token()}}',

                            brand_name: $("#brand_name").val(),
                            logo_url:$("#logo_url").val(),
                            app_id:$("#app_id").val(),
                            primary_category_id: $("#primary_category_id").val(),
                            secondary_category_id: $("#secondary_category_id").val(),
                            protocol: $("#protocol").val(),
                            agreement_media_id: $("#agreement_media_id").val(),
                            operator_media_id:$("#operator_media_id").val(),
                            end_time:$("#end_time").val(),

                            type:type
                        },
                        function (result) {
                            if (result.errcode==0) {
                                //询问框
                                layer.confirm('保存成功', {
                                    btn: ['列表页', '当前页'] //按钮
                                }, function () {
                                    window.location.href = "{{route('WxCardManage')}}";
                                }, function () {
                                    layer.msg('正在浏览提交的广告详情');
                                });
                            } else {
                                layer.msg(result.errmsg);
                            }
                        }, "json")
            }


        }
        function checkstatus(id) {
            if(id=='is'){
                $('#license').css('display','none');
                $('#idcard').css('display','none');
            }else{
                $('#license').css('display','block');
                $('#idcard').css('display','block');
            }
        }
        function getsecondcate(id) {
            var firstv=$(id).find("option:selected").val();
            $.post("{{route('getsecondcategory')}}",
                    {
                        _token: '{{csrf_token()}}',
                        firstv:firstv
                    },
                    function (result) {
                        if (result) {
                            str='';
                            for(var i=0;i<result.length;i++){
                                str+="<option value="+result[i]['secondary_category_id']+">"+result[i]['category_name']+"</option>";
                            }
                            $('#secondary_category_id option' ).remove();
                            $('#secondary_category_id').append("<option value='0'>二级类目</option>"+str);
                        }
                    }, "json")
        }
    </script>

    <script type="text/javascript">
        publicfileupload("#fileupload", ".files", "#logo_url", ".up_progress .progress-bar", ".up_progress");
        publicfileupload("#fileupload1", ".files1", "#strip_image", '.up_progress1 .progress-bar1', ".up_progress1");
        publicfileupload("#fileupload2", ".files2", "#protocol", '.up_progress2 .progress-bar2', ".up_progress2");
        publicfileupload("#fileupload3", ".files3", "#agreement_media_id", '.up_progress3 .progress-bar3', ".up_progress3");
        publicfileupload("#fileupload4", ".files4", "#operator_media_id", '.up_progress4 .progress-bar4', ".up_progress4");
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







