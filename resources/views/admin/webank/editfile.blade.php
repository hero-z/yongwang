@extends('layouts.publicStyle')
@section('title','资质文件上传')
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
        <input type="hidden" id="store_id" value="{{$store['store_id']}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>修改门店资质文件</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            <div class="form-group">
                                <label>门头照片</label>
                                <input type="hidden" required="required" value="{{$store['store_header']}}" size="50" name="store_header" id="store_header">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload5" type="file" name="image"
                                       data-url="{{route('webankdouploadfile')}}"
                                       multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files5">@if($store['store_header'])<img class="images_zone" width="30px" src="{{url($store['store_header'])}}">@endif</div>

                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress5">
                                    <div class="progress-bar5"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>手持身份证照</label>
                                <input type="hidden" required="required" value="{{$store['sfz3']}}" size="50" name="sfz3" id="sfz3">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload10" type="file" name="image"
                                       data-url="{{route('webankdouploadfile')}}"
                                       multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files10">@if($store['sfz3'])<img class="images_zone" width="30px" src="{{url($store['sfz3'])}}">@endif</div>
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
                                <input type="hidden" required="required" value="{{$store['sfz1']}}" size="50" name="sfz1" id="sfz1">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload3" type="file" name="image"
                                       data-url="{{route('webankdouploadfile')}}"
                                       multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files3">@if($store['sfz1'])<img class="images_zone" width="30px" src="{{url($store['sfz1'])}}">@endif</div>
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
                                <input type="hidden" required="required" value="{{$store['sfz2']}}" size="50" name="sfz2" id="sfz2">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload4" type="file" name="image"
                                       data-url="{{route('webankdouploadfile')}}"
                                       multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files4">@if($store['sfz2'])<img class="images_zone" width="30px" src="{{url($store['sfz2'])}}">@endif</div>
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
                                <input type="hidden" required="required" size="50" value="{{$store['licence']}}" name="licence" id="licence">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload1" type="file" name="image"
                                       data-url="{{route('webankdouploadfile')}}"
                                       multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files1">@if($store['licence'])<img class="images_zone" width="30px" src="{{url($store['licence'])}}">@endif</div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress1">
                                    <div class="progress-bar1"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        </form>
                    </div>
                </div>
                <a href="javascript:void(0)" onclick="addpost()">
                    <button style="width: 100%;height: 40px;font-size: 18px;" type="button" class="btn btn-primary">
                        提交店铺资料
                    </button>
                </a>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')
    <script>
        function addpost() {
            $.post("{{route("webankmerchantfilepost")}}", {
                        _token: '{{csrf_token()}}',
                        store_id: $("#store_id").val(),


                        sfz1: $("#sfz1").val(),
                        sfz2: $("#sfz2").val(),
                        sfz3: $("#sfz3").val(),
                        licence: $("#licence").val(),
                        store_header: $("#store_header").val()
                    },
                    function (result) {
                        if (result.status==1) {
                            window.location.href = "{{route('webankindex')}}";
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
    </script>

    <script type="text/javascript">

        publicfileupload("#fileupload1", ".files1", "#licence", '.up_progress1 .progress-bar1', ".up_progress1");
        publicfileupload("#fileupload3", ".files3", "#sfz1", '.up_progress3 .progress-bar3', ".up_progress3");
        publicfileupload("#fileupload4", ".files4", "#sfz2", '.up_progress4 .progress-bar4', ".up_progress4");
        publicfileupload("#fileupload5", ".files5", "#store_header", '.up_progress5 .progress-bar5', ".up_progress5");
        publicfileupload("#fileupload9", ".files9", "#orther1", '.up_progress9 .progress-bar9', ".up_progress9");
        publicfileupload("#fileupload10", ".files10", "#sfz3", '.up_progress10 .progress-bar10', ".up_progress10");
        function publicfileupload(fileid, imgid, postimgid, class1, class2) {
            //图片上传
            $(fileid).fileupload({
                formData: {store: $("#external_id").val(), _token: "{{csrf_token()}}"},
                dataType: 'json',
                add: function (e, data) {
                    var numItems = $('.files .images_zone').length;
                    if (numItems >= 2) {
                        alert('提交照片不能超过2张');
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
                        var imgshow = '<div class="images_zone"><input type="hidden" name="imgs[]" value="' + d.img_url + '" /><span><img src="' + d.img_url + '"  /></span><a href="javascript:;">删除</a></div>';
                        jQuery(imgid).empty();
                        jQuery(imgid).append(imgshow);
                        jQuery(postimgid).val(d.path);
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