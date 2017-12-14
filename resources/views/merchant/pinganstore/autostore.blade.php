@extends('layouts.publicStyle')
@section('title','商户注册门店')
<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<script type="text/javascript" src="https://webapi.amap.com/demos/js/liteToolbar.js"></script>
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
    <script src="{{asset('/js/check.js')}}" type="text/javascript"></script>

    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>商户店铺资料</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            <input type="hidden" name="external_id" id="external_id"
                                   value="<?php echo 'p' . date('YmdHis', time())?>">
                            {{csrf_field()}}
                            {{--<div class="form-group">--}}
                                {{--<label class="font-noraml">绑定收银员</label>--}}
                                {{--<div class="input-group">--}}
                                    {{--<select data-placeholder="选择店铺..." class="chosen-select" style="width:350px;" tabindex="2" name="cashier" id="cashier">--}}
                                        {{--<option value="">请选择收银员</option>--}}

                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="form-group">--}}
                                {{--<label class="font-noraml">门店分类(必选)</label>--}}
                                {{--<div class="input-group">--}}
                                    {{--<select data-placeholder="选择分类..." class="chosen-select" style="width:350px;" tabindex="2" name="category_id" id="category_id">--}}
                                        {{--<option value="">请选择分类</option>--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            <div class="form-group" >
                                <label>门店分类(必选)</label>
                                <div class="col-sm-10">
                                    <select class="form-control m-b" value="" name="category_id" id="category_id">
                                        <option>请选择分类</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户全称(必填)</label>
                                <input placeholder="商户全称,须与商户相关执照一致" class="form-control" name="name" id="name"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户简称(必填)</label>
                                <input required="required" placeholder="商户简称,在支付宝、微信支付时展示" class="form-control"
                                       name="alias_name" id="alias_name"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>店铺地址(必填且同营业执照):</label>
                                <div class="row">
                                    <input required="required" placeholder="请在地图上点击选择地址" class="form-control" name="address" id="address" type="text">
                                </div>

                                <div style="padding-left:5%">
                                    <div id="mapcontainer"  style="width:90%;height:90%">

                                    </div>
                                </div>

                                <script type="text/javascript" src="https://webapi.amap.com/maps?v=1.3&key=ba41d3a8f7b10cf40bed43a6c30b420c"></script>

                                <script type="text/javascript">
                                    var map = new AMap.Map('mapcontainer',{
                                        resizeEnable: true,
                                        zoom: 13
//                                        center: [116.39,39.9]
                                    });
                                    //定位
                                    var geolocation;
                                    map.plugin('AMap.Geolocation', function() {
                                        geolocation = new AMap.Geolocation({
                                            enableHighAccuracy: true,//是否使用高精度定位，默认:true
                                            timeout: 10000,          //超过10秒后停止定位，默认：无穷大
                                            buttonOffset: new AMap.Pixel(10, 20),//定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
                                            zoomToAccuracy: true,      //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
                                            buttonPosition:'RB',
                                            showCircle: false       //定位成功后用圆圈表示定位精度范围，默认：true

                                        });
                                        map.addControl(geolocation);
                                        geolocation.getCurrentPosition();
                                        AMap.event.addListener(geolocation, 'complete', onComplete);//返回定位信息
                                        AMap.event.addListener(geolocation, 'error', onError);      //返回定位出错信息
                                    });
                                    //取地理位置
                                    map.plugin('AMap.Geocoder',function(){
                                        var geocoder = new AMap.Geocoder({
//                                            city: "010"//城市，默认：“全国”
                                        });
                                        var marker = new AMap.Marker({
                                            map:map,
                                            bubble:true
                                        });
                                        map.on('click',function(e){
                                            marker.setPosition(e.lnglat);
                                            geocoder.getAddress(e.lnglat,function(status,result){
                                                if(status=='complete'){
                                                    $('#address').val(result.regeocode.formattedAddress);
                                                }
                                            })
                                        })
                                    });
                                    function onComplete(data) {
                                        map.setCenter([data.position.getLng(),data.position.getLat()]);
                                    }
                                    //解析定位错误信息
                                    function onError(data) {
                                        $('#address').val('定位失败');
                                    }
                                </script>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>地址编码:</label>
                                <div class="col-sm-10">
                                    <select class="form-control m-b" value="" onchange="getdata(this.id,this.value)" name="province" id="province">
                                        <option value="0" >请选择省份</option>
                                        @foreach($provincelists as $v)
                                            <option value="{{$v->areaCode}}">{{$v->areaName}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-10">
                                    <select class="form-control m-b" value="" onchange="getdata(this.id,this.value)" name="city" id="city">
                                        <option value="0">请选择市区</option>
                                    </select>
                                </div>
                                <div class="col-sm-10">
                                    <select class="form-control m-b" value="" name="county" id="county">
                                        <option value="0">请选择县乡</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed" hidden></div>
                            <div class="form-group">
                                <label>身份证姓名(必填):</label>
                                <input class="form-control" type="text" value="" required="required"  name="sfzname" id="sfzname">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>身份证号码(必填):</label>
                                <input class="form-control" type="text" value="" required="required"  name="sfzno" id="sfzno">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系人手机号(必填)</label>
                                <input required="required" placeholder="客服电话" class="form-control"
                                       name="service_phone" id="service_phone" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>推广员ID(必填)</label>
                                <input required="required" placeholder="" class="form-control" value="{{$user_id}}" name="user_id" id="user_id"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                        </form>
                    </div>
                </div>
                <a href="javascript:void(0)" onclick="addpost()">
                    <button style="width: 100%;height: 40px;font-size: 18px;" type="button" class="btn btn-primary">
                        下一步上传资质文件
                    </button>
                </a>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')
    <script>
        function addpost() {
            if($("#category_id").val()=='请选择分类'){
                layer.msg('门店分类必选!');
                $("#category_id").focus();
            }else if(!$("#name").val()){
                layer.msg('商户全称必填!');
                $("#name").focus();
            }else if(!$("#alias_name").val()){
                layer.msg('商户简称必填!');
                $("#alias_name").focus();
            }else if(!$("#address").val()){
                layer.msg('店铺地址必填!');
                $("#address").focus();
            }else if($("#county").val()=='0'){
                layer.msg('地址编码不完善!');
                $("#province").focus()
            }else if(!$("#sfzname").val()){
                layer.msg('身份证姓名必填!');
                $("#sfzname").focus();
            }else if(!$("#sfzno").val()){
                layer.msg('身份证号必填!');
                $("#sfzno").focus();
            }else if(!$("#user_id").val()){
                layer.msg('推广员必填!');
                $("#user_id").focus();
            }else if(!isChinaName($("#sfzname").val().trim())){
                layer.msg('身份证姓名不合法!');
                $("#sfzname").focus();
            }else if(!isCardNo($("#sfzno").val().trim())){
                layer.msg('身份证号不合法!');
                $("#sfzno").focus();
            }else if (!IsTel($("#service_phone").val().trim())) {
                layer.msg('手机号码不正确');
                return false;
            }else{
//                address=$("#province option:selected").html()+$("#city option:selected").html()+$("#county option:selected").html();
                id_card_num=$("#sfzno").val().trim();
                service_phone=$("#service_phone").val().trim();
                province=$("#province option:selected").html();
                city=$("#city option:selected").html();
                district=$("#county option:selected").html();

                province_code=$("#province ").val();
                city_code=$("#city ").val();
                district_code=$("#county ").val();
                window.location.href = "{{url('merchant/autoFile?external_id=')}}" + $("#external_id").val() + '&code_number=' + $("#code_number").val() +'&user_id='+$('#user_id').val()+'&name='+$('#name').val()+'&alias_name='+$('#alias_name').val()+'&service_phone='+service_phone +'&category_id='+$('#category_id').val()+'&id_card_name='+$('#sfzname').val()+'&id_card_num='+id_card_num+'&store_address='+$('#address').val()+'&province='+province+'&city='+city+'&district='+district+'&province_code='+province_code+'&city_code='+city_code+'&district_code='+district_code;
            }
        }
        window.onload = get;
        function get() {
            getCategory();
        }
        //获得分类
        function getCategory() {
            $.post("{{route("getNewCategory")}}", {_token: $("#token").val()}, function (data) {
                for (var key in data) {
                    var selObj = $("#category_id");
                    var value = data[key].category_id;
                    var text = data[key].name;
                    selObj.append("<option value='" + value + "'>" + text + "</option>");
                }
            }, "json");
            {{--$.post("{{route("getNewCategory")}}", {_token: $("#token").val()}, function (data) {--}}
                {{--for (var key in data) {--}}
                    {{--var selObj = $("#category_id");--}}
                    {{--var value = data[key].category_id;--}}
                    {{--var text = data[key].link;--}}
                    {{--selObj.append("<option value='" + value + "'>" + text + "</option>");--}}
                {{--}--}}
            {{--}, "json");--}}
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
        //获取地址
        function getdata(id,value) {
            if(id=='province')
                cityorcounty='city';
            else
                cityorcounty='county';
            $('#county option').remove();
            $('#county').append("<option value='0'>请选择乡县</option>");
            $.post("{{route("getcitycountydata")}}", {id:value,_token: $("#token").val()}, function (data) {
                str='';
                for (var k=0;k<data.length;k++) {
                    str+="<option value="+data[k].areaCode+">"+data[k].areaName+"</option>";
                }
                if(cityorcounty=='city'){
                    $('#city option').remove();
                    $('#city').append("<option value='0'>请选择市区</option>"+str);
                }
                else{
                    $('#county option').remove();
                    $('#county').append("<option value='0'>请选择乡县</option>"+str);
                }

            }, "json");
        }
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
                        jQuery(imgid).empty();
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