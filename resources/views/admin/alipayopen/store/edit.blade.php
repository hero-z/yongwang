@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
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
    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>重新提交门店</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="{{url('admin/alipayopen/store')}}" method="post">
                            {{csrf_field()}}
                            <input type="hidden" id="user_id" name="user_id" value="{{$shop['user_id']}}">
                            <input type="hidden" id="store_id" name="store_id"
                                   value="{{$shop['store_id']}}">
                            <input type="hidden" id="app_auth_token" name="app_auth_token"
                                   value="{{$shop['app_auth_token']}}">
                            <input type="hidden" id="request_id" name="request_id"
                                   value="<?php echo date('YmdHis', time())?>">
                            <div class="form-group">
                                <label>门店分类</label>
                                <div class="col-sm-10">
                                    <select class="form-control m-b" name="category_id" id="category_id">
                                        <option>请选择</option>
                                        @foreach($category as $v)
                                            <option @if($v['category_id']==$shop['category_id']) selected
                                                    @endif value="{{$v['category_id']}}">{{$v['link']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>品牌名</label>
                                <input msg="品牌名" id="istrue" value="{{$shop['brand_name']}}" placeholder="不填写则默认为“其它品牌”"
                                       class="form-control" name="brand_name" id="brand_name" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <script src="{{asset('uploadify/jquery.uploadify.min.js')}}"
                                        type="text/javascript"></script>
                                <link rel="stylesheet" type="text/css" href="{{asset('uploadify/uploadify.css')}}">
                                <label>品牌LOGO</label>
                                <input type="hidden" value="{{$shop['brand_logo']}}" required="required" size="50"
                                       name="brand_logo" id="brand_logo">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload" type="file" name="image" data-url="{{route('upload')}}"
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
                                <label>主店名称</label>
                                <input msg="主店名称" value="{{$shop['main_shop_name']}}" required="required"
                                       placeholder="比如：肯德基；主店名里不要包含分店名" class="form-control" name="main_shop_name"
                                       id="main_shop_name"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>分店名称</label>
                                <input msg="分店名称" required="required" placeholder="比如：万塘路店，与主门店名合并在客户端显示为：肯德基(万塘路店)"
                                       class="form-control"
                                       value="{{$shop['branch_shop_name']}}" name="branch_shop_name"
                                       id="branch_shop_name" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>门店地址</label>
                                <div id="description">
                                    <select style="width:100px; " onchange="sech(this.id)" id="province_code"
                                            name="province_code">
                                        @foreach($province_city_district['province'] as $v)
                                            <option @if($v['areaCode']==$shop['province_code']) selected
                                                    @endif value="{{$v['areaCode']}}">{{$v['areaName']}}</option>
                                        @endforeach
                                    </select>
                                    <select onchange="sech(this.id)" id="city_code" name="city_code">
                                        <option value="{{$province_city_district['city'][0]['areaCode']}}">{{$province_city_district['city'][0]['areaName']}}</option>
                                    </select>
                                    <select id="district_code" name="district_code">
                                        <option value="{{$province_city_district['district'][0]['areaCode']}}">{{$province_city_district['district'][0]['areaName']}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>详细地址</label>
                                <input msg="详细地址" required="required"
                                       placeholder="不含省市区。门店详细地址按规范格式填写地址，以免影响门店搜索及活动报名：例1：道路+门牌号，“人民东路18号”"
                                       value="{{$shop['address']}}" class="form-control" name="address" id="address"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>经度纬度</label>
                                <input msg="经度纬度" value="{{$shop['longitude'].','.$shop['latitude']}}"
                                       required="required" placeholder="经度纬度；最长15位字符（包括小数点）" class="form-control"
                                       name="longitude_latitude" id="longitude_latitude"
                                       type="text">
                                <a href="http://lbs.amap.com/console/show/picker" target="_blank">经纬度查询</a>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>门店电话号码</label>
                                <input msg="门店电话号码" required="required"
                                       placeholder="门店电话号码；支持座机和手机，只支持数字和+-号，在客户端对用户展现， 支持多个电话以英文逗号分隔。"
                                       value="{{$shop['contact_number']}}" class="form-control" name="contact_number"
                                       id="contact_number" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>门店店长电话号码</label>
                                <input placeholder="门店店长电话号码；用于接收门店状态变更通知，收款成功通知等通知消息， 不在客户端展示" class="form-control"
                                       value="{{$shop['notify_mobile']}}" name="notify_mobile" id="notify_mobile"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>门店首图</label>
                                <input type="hidden" value="{{$shop['main_image']}}" required="required" size="50"
                                       name="main_image" id="main_image">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload1" type="file" name="image" data-url="{{route('upload')}}"
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
                            <div class="form-group">
                                <label>店内照片</label>
                                <input type="hidden" value="{{$shop['audit_images1']}}" size="50" required="required"
                                       name="audit_images1" id="audit_images1">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload2" type="file" name="image" data-url="{{route('upload')}}"
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
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>内景照片1</label>
                                <input type="hidden" size="50" value="{{$shop['audit_images2']}}" name="audit_images2"
                                       required="required" id="audit_images2">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload3" type="file" name="image" data-url="{{route('upload')}}"
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
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>内景照片2</label>
                                <input type="hidden" size="50" value="{{$shop['audit_images3']}}" name="audit_images3"
                                       required="required" id="audit_images3">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload4" type="file" name="image" data-url="{{route('upload')}}"
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
                                <label>营业时间</label>
                                <input class="form-control" value="{{$shop['business_time']}}" required="required"
                                       name="business_time" id="business_time" type="text"
                                       placeholder="营业时间，支持分段营业时间，以英文逗号分隔。09:00-11:00,13:00-15:00">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>wifi</label>
                                <input class="form-control" name="wifi" id="wifi" type="text"
                                       value="{{$shop['wifi']}}" placeholder="门店是否支持WIFI，T表示支持，F表示不支持">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>停车</label>
                                <input class="form-control" value="{{$shop['parking']}}" name="parking" id="parking"
                                       type="text" placeholder="T表示支持，F表示不支持">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>门店其他的服务</label>
                                <input class="form-control" name="value_added" id="value_added" type="text"
                                       value="{{$shop['value_added']}}" placeholder="门店其他的服务，门店与用户线下兑现">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>人均消费</label>
                                <input class="form-control" name="avg_price" id="avg_price" type="text"
                                       value="{{$shop['avg_price']}}" placeholder="人均消费价格，最少1元，最大不超过99999元">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>营业执照</label>
                                <input type="hidden" value="{{$shop['licence']}}" size="50" required="required" name="licence" id="licence">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload5" type="file" name="image" data-url="{{route('upload')}}"
                                       data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true">
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
                                <label>营业执照上名称</label>
                                <input type="text" value="{{$shop['licence_name']}}" required="required" size="50"
                                       name="licence_name" id="licence_name" placeholder="门店营业执照名称">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>营业执照编号</label>
                                <input type="text" required="required" value="{{$shop['licence_code']}}" size="50"
                                       name="licence_code" id="licence_code" placeholder="门店营业执照编号">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>营业执照过期时间</label>
                                <input type="text" value="{{$shop['licence_expires']}}"
                                       placeholder="营业执照过期时间格式：2020-03-20" size="50" name="licence_expires"
                                       id="licence_expires">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>许可证</label>
                                <input type="hidden" size="50" value="{{$shop['business_certificate']}}"
                                       name="business_certificate" id="business_certificate">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload6" type="file" name="image" data-url="{{route('upload')}}"
                                       data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files6"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress6">
                                    <div class="progress-bar6"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>许可证有效期</label>
                                <input type="text" placeholder="许可证有效期，格式：2020-03-20" size="50"
                                       value="{{$shop['business_certificate_expires']}}"
                                       name="business_certificate_expires" id="business_certificate_expires">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>其他资料</label>
                                <input type="hidden" value="{{$shop['other_authorization']}}" size="50"
                                       name="other_authorization" id="other_authorization">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload7" type="file" name="image" data-url="{{route('upload')}}"
                                       data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files7"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress7">
                                    <div class="progress-bar7"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>门店授权函</label>
                                <input type="hidden" size="50" name="auth_letter" id="auth_letter">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload8" type="file" name="image" data-url="{{route('upload')}}"
                                       data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files8"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress8">
                                    <div class="progress-bar8"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>是否有包厢</label>
                                <input class="form-control" value="{{$shop['box']}}" name="box" id="box" type="text"
                                       placeholder="门店是否有包厢，T表示有，F表示没有">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>是否有无烟区</label>
                                <input class="form-control" value="{{$shop['no_smoking']}}" name="no_smoking"
                                       id="no_smoking" type="text"
                                       placeholder="T表示有无烟区，F表示没有无烟区">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>机具号</label>
                                <input class="form-control" value="{{$shop['implement_id']}}" name="implement_id"
                                       id="implement_id" type="text"
                                       placeholder="机具号，多个之间以英文逗号分隔">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>是否在其他平台开店</label>
                                <input class="form-control" value="{{$shop['is_operating_online']}}"
                                       name="is_operating_online" id="is_operating_online" type="text"
                                       placeholder="是否在其他平台开店，T表示有开店，F表示未开店。">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>其他平台开店的店铺链接url</label>
                                <input class="form-control" value="{{$shop['online_url']}}" name="online_url"
                                       id="online_url" type="text"
                                       placeholder="其他平台开店的店铺链接url，多个url使用英文逗号隔开">
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
    <div id="con"></div>
@section('js')
    <script>
        function addpost() {
            $.post("{{url('admin/alipayopen/store')}}",
                    {
                        _token: '{{csrf_token()}}',
                        app_auth_token: $("#app_auth_token").val(),
                        store_id: $("#store_id").val(),
                        category_id: $("#category_id").val()
                        ,
                        user_id:$("#user_id").val(),
                        brand_name: $("#brand_name").val(),
                        brand_logo: $("#brand_logo").val(),
                        main_shop_name: $("#main_shop_name").val()
                        ,
                        branch_shop_name: $("#branch_shop_name").val(),
                        province_code: $("#province_code").val(),
                        city_code: $("#city_code").val()
                        ,
                        district_code: $("#district_code").val(),
                        address: $("#address").val(),
                        longitude_latitude: $("#longitude_latitude").val()
                        ,
                        contact_number: $("#contact_number").val(),
                        notify_mobile: $("#notify_mobile").val()
                        ,
                        main_image: $("#main_image").val(),
                        audit_images1: $("#audit_images1").val(),
                        audit_images2: $("#audit_images2").val(),
                        audit_images3: $("#audit_images3").val(),
                        business_time: $("#business_time").val()
                        ,
                        wifi: $("#wifi").val(),
                        parking: $("#parking").val(),
                        value_added: $("#value_added").val()
                        ,
                        avg_price: $("#avg_price").val(),
                        licence: $("#licence").val(),
                        licence_code: $("#licence_code").val()
                        ,
                        licence_name: $("#licence_name").val(),
                        business_certificate: $("#business_certificate").val(),
                        business_certificate_expires: $("#business_certificate_expires").val()
                        ,
                        operate_notify_url: $("#operate_notify_url").val(),
                        implement_id: $("#implement_id").val(),
                        no_smoking: $("#no_smoking").val()
                        ,
                        box: $("#box").val(),
                        request_id: $("#request_id").val(),
                        auth_letter:$("#auth_letter").val(),
                        other_authorization: $("#other_authorization").val()
                        ,
                        licence_expires: $("#licence_expires").val()
                    },
                    function (result) {
                        if (result.code == 10000) {
                            //询问框
                            layer.confirm('保存成功！等待口碑审核！', {
                                btn: ['列表页', '当前页'] //按钮
                            }, function () {
                                window.location.href = "{{url('/admin/alipayopen/store')}}";
                            }, function () {
                                layer.msg('正在浏览提交的店铺资料');
                            });
                        } else {
                            layer.msg(result.sub_msg);
                        }
                    }, "json")
        }
    </script>

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
                        jQuery(postimgid).val(d.image_id);
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
                mouseenter:function(){
                    $(this).find('a').show();
                },
                mouseleave:function(){
                    $(this).find('a').hide();
                },
            },'.images_zone');
            $(imgid).on('click','.images_zone a',function(){
                $(this).parent().remove();
            });
        }
    </script>

    <script>
        window.onload = get;
        function get() {
            //getCategory();
            //   getProvince();
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
            $("#city_code").val("");$("#district_code").val("");
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

                    selObj.append("<option   value='" + value + "'>" + text + "</option>");
                }
            }, "json");
        }
    </script>

@endsection
@endsection