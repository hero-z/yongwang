@extends('layouts.publicStyle')
@section('title','资质上传')
@section('css')
@endsection
@section('content')
    <script src="{{asset('/jQuery-File-Upload/js/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js//jquery.iframe-transport.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js/jquery.fileupload.js')}}" type="text/javascript"></script>
    <script src="{{asset('uploadify/jquery.uploadify.min.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('uploadify/uploadify.css')}}">
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
                <h5>上传门店资质文件</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            <input type="hidden" id="external_id" value="<?php echo $_GET['external_id']?>">
                            <input type="hidden" id="code_number" value="<?php echo $_GET['code_number']?>">
                            <input type="hidden" id="user_id" value="<?php echo $_GET['user_id']?>">
                            <input type="hidden" id="name" value="<?php echo $_GET['name']?>">
                            <input type="hidden" id="alias_name" value="<?php echo $_GET['alias_name']?>">
                            <input type="hidden" id="service_phone" value="<?php echo $_GET['service_phone']?>">
                            <input type="hidden" id="category_id" value="<?php echo $_GET['category_id']?>">
                            <input type="hidden" id="id_card_name" value="<?php echo $_GET['id_card_name']?>">
                            <input type="hidden" id="id_card_num" value="<?php echo $_GET['id_card_num']?>">
                            <input type="hidden" id="store_address" value="<?php echo $_GET['store_address']?>">
                            <input type="hidden" id="province" value="<?php echo $_GET['province']?>">
                            <input type="hidden" id="city" value="<?php echo $_GET['city']?>">
                            <input type="hidden" id="district" value="<?php echo $_GET['district']?>">
                            <input type="hidden" id="province_code" value="<?php echo $_GET['province_code']?>">
                            <input type="hidden" id="city_code" value="<?php echo $_GET['city_code']?>">
                            <input type="hidden" id="district_code" value="<?php echo $_GET['district_code']?>">
                            <div class="form-group">
                                <label>门头照片(必选)</label>
                                <input type="hidden" required="required" size="50" name="main_image" id="main_image">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload5" type="file" name="image"
                                       data-url="{{route('uploadImagePingAn')}}"
                                       multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files5"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress5">
                                    <div class="progress-bar5"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>手持身份证照(必选)</label>
                                <input type="hidden" required="required" size="50" name="sfz3" id="sfz3">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload10" type="file" name="image"
                                       data-url="{{route('uploadImagePingAn')}}"
                                       multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files10"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress10">
                                    <div class="progress-bar10"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>身份证正面照</label>
                                <input type="hidden" required="required" size="50" name="sfz1" id="sfz1">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload3" type="file" name="image"
                                       data-url="{{route('uploadImagePingAn')}}"
                                       multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files3"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress3">
                                    <div class="progress-bar3"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>身份证反面照</label>
                                <input type="hidden" required="required" size="50" name="sfz2" id="sfz2">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload4" type="file" name="image"
                                       data-url="{{route('uploadImagePingAn')}}"
                                       multiple="true">
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
                                <label>营业执照</label>
                                <input type="hidden" required="required" size="50" name="licence" id="licence">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload1" type="file" name="image"
                                       data-url="{{route('uploadImagePingAn')}}"
                                       multiple="true">
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
                            <div class="form-group">
                                <label>商户服务协议或入账授权函</label>
                                <input type="hidden" required="required" size="50" name="orther1" id="orther1">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload9" type="file" name="image"
                                       data-url="{{route('uploadImagePingAn')}}"
                                       multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files9"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress9">
                                    <div class="progress-bar9"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        </form>
                    </div>
                </div>
                <a href="javascript:void(0)" onclick="addpost()">
                    <button style="width: 100%;height: 40px;font-size: 18px;" type="button" class="btn btn-primary">
                        下一步绑定银行卡号
                    </button>
                </a>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')
    <script>
        function addpost() {
            if(!$("#main_image").val()){
                layer.msg('门头照片照片必选!');
                $("#main_image").focus();
            }else if(!$("#sfz3").val()){
                layer.msg('手持身份证照片必选!');
                $("#sfz3").focus();
            }else{
                {{--                $.post("{{route("autoFilePost")}}",--}}
                                $.post("{{route("PAautoStorePost")}}",
                        {
                            _token: '{{csrf_token()}}',
                            external_id: $("#external_id").val(),

                            code_number: $("#code_number").val(),
                            user_id: $("#user_id").val(),

                            name: $("#name").val(),
                            alias_name: $("#alias_name").val(),
                            service_phone: $("#service_phone").val(),
                            category_id: $("#category_id").val(),
                            id_card_name: $("#id_card_name").val(),
                            id_card_num: $("#id_card_num").val(),
                            store_address: $("#store_address").val(),

                            store_front_img_url: $("#main_image").val(),
                            id_card_hand_img_url: $("#sfz3").val(),
                            business_license_img_url: $("#licence").val(),

                            sfz1: $("#sfz1").val(),
                            sfz2: $("#sfz2").val(),
                            orther1: $("#orther1").val(),
                            province:$("#province").val(),
                            city:$("#city").val(),
                            district:$("#district").val(),

                            province_code:$("#province_code").val(),
                            city_code:$("#city_code").val(),
                            district_code:$("#district_code").val()
                        },
                        function (result) {
                            if (result.success) {
                                window.location.href = "{{url('merchant/autom?external_id=')}}" + $("#external_id").val() + '&code_number=' + $("#code_number").val();
                                {{--window.location.href = "{{route('PingAnSuccess')}}";--}}
                                {{--window.location.href = "{{route('PingAnSuccess')}}";--}}
                            } else {
                                layer.msg(result.error_message);
                            }
                        }, "json")
            }
            // 验证身份证
            function isCardNo(card) {
                var pattern = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
                return pattern.test(card);
            }
            // 验证中文名称
            function isChinaName(name) {
                var pattern = /^[\u4E00-\u9FA5]{1,6}$/;
                return pattern.test(name);
            }

        }
        {{--function addpost() {--}}
            {{--$.post("{{route("PAautoFilePost")}}",--}}
                    {{--{--}}
                        {{--_token: '{{csrf_token()}}',--}}
                        {{--external_id: $("#external_id").val(),--}}
                        {{--licence: $("#licence").val(),--}}
                        {{--sfz1: $("#sfz1").val(),--}}
                        {{--sfz2: $("#sfz2").val(),--}}
                        {{--sfz3: $("#sfz3").val(),--}}
                        {{--main_image: $("#main_image").val(),--}}
                        {{--orther1: $("#orther1").val(),--}}
                        {{--code_number: $("#code_number").val()--}}
                    {{--},--}}
                    {{--function (result) {--}}
                        {{--if (result.success) {--}}
                            {{--window.location.href = "{{url('/merchant/PingAnQr')}}";--}}
                        {{--} else {--}}
                            {{--layer.msg(result.error_message);--}}
                        {{--}--}}
                    {{--}, "json")--}}
        {{--}--}}
    </script>

    <script type="text/javascript">
        publicfileupload("#fileupload1", ".files1", "#licence", '.up_progress1 .progress-bar1', ".up_progress1");
        publicfileupload("#fileupload3", ".files3", "#sfz1", '.up_progress3 .progress-bar3', ".up_progress3");
        publicfileupload("#fileupload4", ".files4", "#sfz2", '.up_progress4 .progress-bar4', ".up_progress4");
        publicfileupload("#fileupload5", ".files5", "#main_image", '.up_progress5 .progress-bar5', ".up_progress5");
        publicfileupload("#fileupload9", ".files9", "#orther1", '.up_progress9 .progress-bar9', ".up_progress9");
        publicfileupload("#fileupload10", ".files10", "#sfz3", '.up_progress10 .progress-bar10', ".up_progress10");
        function publicfileupload(fileid, imgid, postimgid, class1, class2) {
            //图片上传
            $(fileid).fileupload({
                formData: {external_id: $("#external_id").val(), _token: "{{csrf_token()}}"},
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

@endsection
@endsection