@extends('layouts.antui')
@section('content')
    <script src={{asset('/js/jquery.min.js?v=2.1.4')}}></script>
    <script src="{{asset('/js/plugins/layer/layer.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/js/ajaxfileupload.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js//jquery.iframe-transport.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js/jquery.fileupload.js')}}" type="text/javascript"></script>

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
    <div class="am-list form">
        <form action="{{url('/admin/alipayopen/selfshoppost')}}" method="post">
            <input type="hidden" id="store_id" name="store_id"
                   value="<?php echo 'sp_' . date('YmdHis', time()) . rand(1000, 9999)?>">
            <input type="hidden" value="{{csrf_token()}}" id="_token" name="_token">
            <input type="hidden" value="<?php echo $_GET['user_id']?>" id="user_id" name="user_id">
            <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">门店名称</div>
                <div class="am-list-control">
                    <input placeholder="主门店名 比如：肯德基" type="text" id="main_shop_name" name="main_shop_name">
                </div>
                <div class="am-list-clear"><i class="am-icon-clear am-icon" style="visibility: hidden;"></i></div>
            </div>
            <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">分店名称</div>
                <div class="am-list-control">
                    <input placeholder="分店名称 比如：万塘路店" type="text" id="branch_shop_name" name="branch_shop_name">
                </div>
                <div class="am-list-clear"><i class="am-icon-clear am-icon" style="visibility: hidden;"></i></div>
            </div>
            <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">主要联系人</div>
                <div class="am-list-control">
                    <input placeholder="输入联系人姓名" type="text" id="contact_name" name="contact_name">
                </div>
                <div class="am-list-clear"><i class="am-icon-clear am-icon" style="visibility: hidden;"></i></div>
            </div>
            <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">电话号码</div>
                <div class="am-list-control">
                    <input placeholder="请输入联系方式" type="text" id="contact_number" name="contact_number">
                </div>
                <div class="am-list-clear"><i class="am-icon-clear am-icon" style="visibility: hidden;"></i></div>
            </div>
            <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">经营品类</div>
                <div class="am-list-control">
                    <input placeholder="主要经营品类" type="text" id="category_name" name="category_name">
                </div>
                <div class="am-list-clear"><i class="am-icon-clear am-icon" style="visibility: hidden;"></i></div>
            </div>
          {{--  <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">品牌名</div>
                <div class="am-list-control">
                    <input placeholder="请输入品牌名" type="text" id="brand_name" name="brand_name">
                </div>
                <div class="am-list-clear"><i class="am-icon-clear am-icon" style="visibility: hidden;"></i></div>
            </div>--}}
            <div class="am-list-body">
                <div class="am-list-item select">
                    <div class="am-list-content">
                        <select onchange="sech(this.id)" id="province_code" name="province_code">
                            <option value="110000">请选择省份</option>
                        </select>
                    </div>
                    <div class="am-list-arrow"><span class="am-icon arrow vertical"></span></div>
                </div>
            </div>
            <div class="am-list-body">
                <div class="am-list-item select">
                    <div class="am-list-content">
                        <select onchange="sech(this.id)" id="city_code" name="city_code">
                            <option value="110100">请选择市区</option>
                        </select>
                    </div>
                    <div class="am-list-arrow"><span class="am-icon arrow vertical"></span></div>
                </div>
            </div>
            <div class="am-list-body">
                <div class="am-list-item select">
                    <div class="am-list-content">
                        <select id="district_code" name="district_code">
                            <option value="110101">请选择县乡</option>
                        </select>
                    </div>
                    <div class="am-list-arrow"><span class="am-icon arrow vertical"></span></div>
                </div>
            </div>
            <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">详细地址</div>
                <div class="am-list-control">
                    <input placeholder="请输入详细地址" type="text" id="address" name="address">
                </div>
                <div class="am-list-clear"><i class="am-icon-clear am-icon" style="visibility: hidden;"></i></div>
            </div>
          {{--  <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">品牌LOGO</div>
                <input type="hidden" required="required" size="50" name="brand_logo" id="brand_logo">
                <!-- 图片上传按钮 -->
                <input id="fileupload" type="file" name="image" data-url="{{route('uploadlocal')}}"
                       multiple="true">
                <!-- 图片展示模块 -->
                <div class="files"></div>
                <div style="clear:both;"></div>
                <!-- 图片上传进度条模块 -->
                <div class="up_progress">
                    <div class="progress-bar"></div>
                </div>
                <div style="clear:both;"></div>

            </div>--}}
            <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">营业执照</div>
                <input type="hidden" size="50" required="required" name="licence" id="licence">
                <!-- 图片上传按钮 -->
                <input id="fileupload5" type="file" name="image" data-url="{{route('uploadlocal')}}" multiple="true">
                <!-- 图片展示模块 -->
                <div class="files5"></div>
                <div style="clear:both;"></div>
                <!-- 图片上传进度条模块 -->
                <div class="up_progress5">
                    <div class="progress-bar5"></div>
                </div>
                <div style="clear:both;"></div>
            </div>
            <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">许可证</div>
                <input type="hidden" size="50" name="business_certificate" id="business_certificate">
                <!-- 图片上传按钮 -->
                <input id="fileupload6" type="file" name="image" data-url="{{route('uploadlocal')}}"
                       multiple="true">
                <!-- 图片展示模块 -->
                <div class="files6"></div>
                <div style="clear:both;"></div>
                <!-- 图片上传进度条模块 -->
                <div class="up_progress6">
                    <div class="progress-bar6"></div>
                </div>
                <div style="clear:both;"></div>
            </div>
          {{--  <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">授权函</div>
                <input type="hidden" size="50" name="auth_letter" id="auth_letter">
                <!-- 图片上传按钮 -->
                <input id="fileupload8" type="file" name="image" data-url="{{route('uploadlocal')}}" multiple="true">
                <!-- 图片展示模块 -->
                <div class="files8"></div>
                <div style="clear:both;"></div>
                <!-- 图片上传进度条模块 -->
                <div class="up_progress8">
                    <div class="progress-bar8"></div>
                </div>
                <div style="clear:both;"></div>
            </div>--}}
            <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">门店首图</div>
                <input type="hidden" required="required" size="50" name="main_image" id="main_image">
                <!-- 图片上传按钮 -->
                <input id="fileupload1" type="file" name="image" data-url="{{route('uploadlocal')}}"
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
            <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">店内照片1</div>
                <input type="hidden" size="50" required="required" name="audit_images1" id="audit_images1">
                <!-- 图片上传按钮 -->
                <input id="fileupload2" type="file" name="image" data-url="{{route('uploadlocal')}}"
                       multiple="true">
                <!-- 图片展示模块 -->
                <div class="files2"></div>
                <div style="clear:both;"></div>
                <!-- 图片上传进度条模块 -->
                <div class="up_progress2">
                    <div class="progress-bar2"></div>
                </div>
                <div style="clear:both;"></div>
            </div>
            <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">店内照片2</div>
                <input type="hidden" size="50" name="audit_images2" required="required" id="audit_images2">
                <!-- 图片上传按钮 -->
                <input id="fileupload3" type="file" name="image" data-url="{{route('uploadlocal')}}"
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
            <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">店内照片3</div>
                <input type="hidden" size="50" name="audit_images3" required="required" id="audit_images3">
                <!-- 图片上传按钮 -->
                <input id="fileupload4" type="file" name="image" data-url="{{route('uploadlocal')}}"
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
           {{-- <div class="am-list-item am-input-autoclear">
                <div class="am-list-label">其他资料</div>
                <input type="hidden" size="50" name="other_authorization" id="other_authorization">
                <!-- 图片上传按钮 -->
                <input id="fileupload7" type="file" name="image" data-url="{{route('uploadlocal')}}" multiple="true">
                <!-- 图片展示模块 -->
                <div class="files7"></div>
                <div style="clear:both;"></div>
                <!-- 图片上传进度条模块 -->
                <div class="up_progress7">
                    <div class="progress-bar7"></div>
                </div>
                <div style="clear:both;"></div>
            </div>--}}
            <br>
                <button type="submit" class="am-button blue">提交保存</button>
        </form>
    </div>

    <script type="text/javascript">
        publicfileupload("#fileupload", ".files", "#brand_logo", ".up_progress .progress-bar", ".up_progress");
        publicfileupload("#fileupload1", ".files1", "#main_image", '.up_progress1 .progress-bar1', ".up_progress1");
        publicfileupload("#fileupload2", ".files2", "#audit_images1", '.up_progress2 .progress-bar2', ".up_progress2");
        publicfileupload("#fileupload3", ".files3", "#audit_images2", '.up_progress3 .progress-bar3', ".up_progress3");
        publicfileupload("#fileupload4", ".files4", "#audit_images3", '.up_progress4 .progress-bar4', ".up_progress4");
        publicfileupload("#fileupload5", ".files5", "#licence", '.up_progress5 .progress-bar5', ".up_progress5");
        publicfileupload("#fileupload6", ".files6", "#business_certificate", '.up_progress6 .progress-bar6', ".up_progress6");
        publicfileupload("#fileupload7", ".files7", "#other_authorization", '.up_progress7 .progress-bar7', ".up_progress7");
        publicfileupload("#fileupload8", ".files8", "#auth_letter", '.up_progress8 .progress-bar8', ".up_progress8");
        function publicfileupload(fileid, imgid, postimgid, class1, class2) {
            //图片上传
            $(fileid).fileupload({
                formData: {store_id: $("#store_id").val(), _token: "{{csrf_token()}}"},
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
    <script>
        window.onload = get;
        function get() {
            getProvince();
        }
        //获取所有省 并插入到select中
        function getProvince() {
            $.post("{{route("getProvince")}}", {_token: $("#_token").val()}, function (data) {
                for (var key in data) {
                    var selObj = $("#province_code");
                    var value = data[key].areaCode;
                    var text = data[key].areaName;
                    selObj.append("<option value='" + value + "'>" + text + "</option>");
                }
            }, "json");
        }
        //判断改变的类型  省或者市
        function sech(id) {
            var areaCode = document.getElementById(id).value;
            if (id == "province_code") {
                getCity(areaCode);//这里为省的id
            }
            if (id == "city_code") {
                getCounty(areaCode);//这里为市的id
            }
        }
        //获得指定省下面的市
        function getCity(areaCode) {
            $.post("{{route("getCity")}}", {_token: $("#_token").val(), areaCode: areaCode}, function (data) {
                for (var key in data) {
                    var selObj = $("#city_code");
                    var value = data[key].areaCode;
                    var text = data[key].areaName;
                    selObj.append("<option value='" + value + "'>" + text + "</option>");
                }
            }, "json");
        }
        //获得指定市下面的县
        function getCounty(areaCode) {
            $.post("{{route("getCity")}}", {_token: $("#_token").val(), areaCode: areaCode}, function (data) {
                for (var key in data) {
                    var selObj = $("#district_code");
                    var value = data[key].areaCode;
                    var text = data[key].areaName;
                    selObj.append("<option value='" + value + "'>" + text + "</option>");
                }
            }, "json");
        }
    </script>
@endsection