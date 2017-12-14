@extends('layouts.publicStyle')
@section('title','商户入驻')
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
                <h5>商户入驻</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post" id="form-product-add">
                            <div class="form-group">
                                <div class="col-sm-10">
                                    <select class="form-control m-b" onchange="sech(this.id)" id="province_code"
                                            name="province_code">
                                        <option value="110000">请选择省份</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-10">
                                    <select class="form-control m-b" onchange="sech(this.id)" id="city_code"
                                            name="city_code">
                                        <option value="110100">请选择市区</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-10">
                                    <select class="form-control m-b" id="district_code" name="district_code">
                                        <option value="110101">请选择县乡</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>详细地址:</label>
                                <input class="form-control" type="text" value="" required="required" name="address"
                                       id="address">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-10">
                                    <div class="radio">
                                        <label>
                                            <input checked="" value="1" id="is_t0" name="is_t0"
                                                   type="radio">T0商户</label> <label>
                                            <input value="0" id="is_t0" name="is_t0"
                                                   type="radio">T1商户</label>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户名称(必填):</label>
                                <input class="form-control" type="text" value="" required="required"
                                       name="merchant_name" id="merchant_name">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>商户简称(必填):</label>
                                <input class="form-control" type="text" value="" required="required" name="alias_name"
                                       id="alias_name">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>商户电话(必填):</label>
                                <input class="form-control" type="text" value="" required="required" name="telephone"
                                       id="telephone">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>商户邮箱(必填):</label>
                                <input class="form-control" type="text" value="" required="required" name="email"
                                       id="email">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>商户负责人(必填):</label>
                                <input class="form-control" type="text" value="" required="required" name="manager"
                                       id="manager">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>商户负责人手机号(必填):</label>
                                <input class="form-control" type="text" value="" required="required"
                                       name="manager_phone"
                                       id="manager_phone">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>商户负责人身份证号(必填):</label>
                                <input class="form-control" type="text" value="" required="required"
                                       name="manager_id_card"
                                       id="manager_id_card">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>商户负责人手持身份证(必选)</label>
                                <input type="hidden" required="required" size="50" name="manager_id_card_img"
                                       id="manager_id_card_img">
                                <input type="hidden" required="required" size="50" name="manager_id_card_img_local"
                                       id="manager_id_card_img_local">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload3" type="file" name="image"
                                       data-url="{{route('uploadImageUnionPay')}}"
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
                                <label>商户门头照照片(必选)</label>
                                <input type="hidden" required="required" size="50" name="store_img" id="store_img">
                                <input type="hidden" required="required" size="50" name="store_img_local"
                                       id="store_img_local">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload1" type="file" name="image"
                                       data-url="{{route('uploadImageUnionPay')}}"
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
                                <label>营业执照</label>
                                <input type="hidden" required="required" size="50" name="business_licence_img_local"
                                       id="business_licence_img_local">
                                <input type="hidden" required="required" size="50" name="business_licence_img"
                                       id="business_licence_img">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload2" type="file" name="image"
                                       data-url="{{route('uploadImageUnionPay')}}"
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
                            <div class="hr-line-dashed"></div>
                            <input type="hidden" id="out_merchant_id" name="out_merchant_id"
                                   value="<?php echo "u" . date('Ymdhis', time()) . rand(10000, 99999)?>">
                        </form>
                    </div>
                </div>
                <a href="javascript:void(0)" onclick="addpost()">
                    <button style="width: 100%;height: 40px;font-size: 18px;" type="button" class="btn btn-primary">
                        下一步绑定银行卡
                    </button>
                </a>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')
    <script>
        function addpost() {
            var out_merchant_id = $("#out_merchant_id").val();
            var province_code = $("#province_code").val();
            var city_code = $("#city_code").val();
            var address = $("#address").val();
            var district_code = $("#district_code").val();
            var is_t0 = $("#is_t0").val();
            var merchant_name = $("#merchant_name").val();
            var alias_name = $("#alias_name").val();
            var telephone = $("#telephone").val();
            var email = $("#email").val();
            var manager = $("#manager").val();
            var manager_phone = $("#manager_phone").val();
            var manager_id_card = $("#manager_id_card").val();
            var manager_id_card_img = $("#manager_id_card_img").val();
            var store_img = $("#store_img").val();
            var business_licence_img = $("#business_licence_img").val();
            var manager_id_card_img_local = $("#manager_id_card_img_local").val();
            var store_img_local = $("#store_img_local").val();
            var business_licence_img_local = $("#business_licence_img_local").val();
            $.post("{{route("UnionPayStore")}}",
                {
                    _token: '{{csrf_token()}}',
                    out_merchant_id: out_merchant_id,
                    province_code: province_code,
                    city_code: city_code,
                    address: address,
                    district_code: district_code,
                    is_t0: is_t0,
                    merchant_name: merchant_name,
                    alias_name: alias_name,
                    telephone: telephone,
                    email: email,
                    manager: manager,
                    manager_phone: manager_phone,
                    manager_id_card: manager_id_card,
                    manager_id_card_img: manager_id_card_img,
                    store_img: store_img,
                    business_licence_img: business_licence_img,
                    manager_id_card_img_local: manager_id_card_img_local,
                    store_img_local: store_img_local,
                    business_licence_img_local: business_licence_img_local,
                },
                function (result) {
                    if (result.status == 1) {
                        window.location.href = "{{url('admin/UnionPay/bindCard?out_merchant_id=')}}" + $("#out_merchant_id").val();
                        {{--window.location.href = "{{route('PingAnSuccess')}}";--}}
                        {{--window.location.href = "{{route('PingAnSuccess')}}";--}}
                    } else {
                        layer.msg(result.msg);
                    }
                }, "json")
        }
    </script>

    <script type="text/javascript">
        publicfileupload("#fileupload1", 2, ".files1", "#store_img","#store_img_local", '.up_progress1 .progress-bar1', ".up_progress1");
        publicfileupload("#fileupload2", 3, ".files2", "#business_licence_img","#business_licence_img_local", '.up_progress2 .progress-bar2', ".up_progress2");
        publicfileupload("#fileupload3", 1, ".files3", "#manager_id_card_img", "#manager_id_card_img_local",'.up_progress3 .progress-bar3', ".up_progress3");
        function publicfileupload(fileid, file_type, imgid, postimgid,imglocal, class1, class2) {
            //图片上传
            $(fileid).fileupload({
                formData: {store_id: $("#store_id").val(), file_type: file_type, _token: "{{csrf_token()}}"},
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
                        jQuery(postimgid).val(d.key);
                        jQuery(imglocal).val(d.image_url);
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
            $.post("{{route("getProvince")}}", {_token: $("#token").val()}, function (data) {
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
            $.post("{{route("getCity")}}", {_token: $("#token").val(), areaCode: areaCode}, function (data) {
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
            $.post("{{route("getCity")}}", {_token: $("#token").val(), areaCode: areaCode}, function (data) {
                for (var key in data) {
                    var selObj = $("#district_code");
                    var value = data[key].areaCode;
                    var text = data[key].areaName;
                    selObj.append("<option value='" + value + "'>" + text + "</option>");
                }
            }, "json");
        }
        //获得分类
        function getCategory() {
            $.post("{{route("getCategory")}}", {_token: $("#token").val()}, function (data) {
                for (var key in data) {
                    var selObj = $("#category_id");
                    var value = data[key].category_id;
                    var text = data[key].link;
                    selObj.append("<option value='" + value + "'>" + text + "</option>");
                }
            }, "json");
        }
    </script>


@endsection
@endsection