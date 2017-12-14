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
            @if (session('info'))
                <div class="ibox-title">
                    <h5 style="color: red; text-align: center">{{session('info')}}</h5>
                </div>
            @endif
            <div class="ibox-title">
                <h5>商户店铺资料</h5>@if($name) (推广员:{{$name}}) @endif
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            {{csrf_field()}}
                            {{--<div class="form-group" >
                                <label>产品类型</label>
                                <div class="col-sm-10">
                                    <input placeholder="003-支付宝,004-微信" class="form-control" name="product_type" id="product_type"
                                        value="{{ old('product_type') }}"   type="text">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>--}}
                            <div class="form-group" >
                                <label>商户法人证件类型</label>
                                <div class="col-sm-10">
                                    <input placeholder="01-身份证,其他类型询问服务商" class="form-control" name="id_type" id="id_type"
                                           value="{{ old('id_type') }}"  type="text">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group" >
                                <label>商户法人证件号码</label>
                                <div class="col-sm-10">
                                    <input placeholder="身份证号,其他类型证件号" class="form-control" name="id_no" id="id_no"
                                           value="{{ old('id_no') }}"      type="text">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            {{--<div class="form-group" >--}}
                                {{--<label>法人代表</label>--}}
                                {{--<div class="col-sm-10">--}}
                                    {{--<input placeholder="法人代表" class="form-control" name="legal_represent" id="legal_represent"--}}
                                           {{--type="text">--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="hr-line-dashed"></div>--}}
                            <div class="form-group">
                                <label>商户全称</label>
                                <input placeholder="商户全称" class="form-control" name="merchant_name" id="merchant_name"
                                       value="{{ old('merchant_name') }}"  type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户简称</label>
                                <input required="required" placeholder="商户简称,在支付宝、微信支付时展示" class="form-control"
                                       name="alias_name" id="alias_name"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            {{--<div class="form-group" >
                                <label>门店类目</label>
                                <div class="col-sm-10">
                                    <input placeholder="支付宝对应支付宝类目，微信对应微信类目" class="form-control" name="category_id" id="category_id"
                                           value="{{ old('category_id') }}"  type="text">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>--}}
                            {{--<div class="form-group">
                                <label>商户类别码:</label>
                                <input class="form-control" placeholder="MCC类别码，请参考数据字典" value="{{ old('merchant_type_code') }}" type="text"  required="required"  name="merchant_type_code" id="merchant_type_code">
                            </div>
                            <div class="hr-line-dashed"></div>--}}
                            <div class="form-group">
                                <label>门店类目:</label>
                                <div class="row">
                                    <select class="form-control m-b" value="" onchange="getcate(this.id,this.value)" name="cate" id="cate">
                                        <option value="0">一级类目</option>
                                    @foreach($cates as $v)
                                            <option value="{{$v->id}}">{{$v->name}}</option>
                                        @endforeach
                                    </select>
                                    <select class="form-control m-b" value="" onchange="getcate(this.id,this.value)" name="secondcate" id="secondcate">
                                        <option value="0">二级类目</option>
                                    </select>
                                    <select class="form-control m-b" value="" name="thirdcate" id="thirdcate">
                                        <option value="0">三级类目</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户类别码:</label>
                                <div class="row">
                                    <select class="form-control m-b" value="" onchange="getmcc(this.id,this.value)" name="MCC" id="MCC">
                                        <option value="0">一级类别</option>
                                        @foreach($mcc as $v)
                                            <option value="{{$v->code}}">{{$v->name}}</option>
                                        @endforeach
                                    </select>
                                    <select class="form-control m-b" value="" onchange="getmcc(this.id,this.value)" name="secondmcc" id="secondmcc">
                                        <option value="0">二级类别</option>
                                    </select>
                                    <select class="form-control m-b" value="" name="thirdmcc" id="thirdmcc">
                                        <option value="0">三级类别</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>营业执照编号</label>
                                <input required="required" placeholder="营业执照编号" class="form-control"
                                       value="{{ old('licence_no') }}" name="licence_no" id="licence_no"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>店铺地址:</label>
                                <div class="row">
                                    <input required="required" value="{{ old('address') }}" placeholder="请在地图上点击选择地址" class="form-control" name="address" id="address" type="text">
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
                                <label>联系人姓名:</label>
                                <input class="form-control" placeholder="联系人姓名" type="text" value="{{ old('contact_name') }}" required="required"  name="contact_name" id="contact_name">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系人手机号</label>
                                <input required="required" placeholder="联系人手机号" class="form-control"
                                       value="{{ old('contact_phone') }}"    name="contact_phone" id="contact_phone" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>客服电话</label>
                                <input required="required" placeholder="客服电话" class="form-control"
                                       value="{{ old('service_phone') }}"  name="service_phone" id="service_phone" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>推广员ID(必填)</label>
                                <input required="required" placeholder="" class="form-control" value="@if($user_id) {{$user_id}} @endif" name="user_id" id="user_id"
                                       type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>省市区信息:</label>
                                <div class="row">
                                    <select class="form-control m-b" value="" onchange="getdata(this.id,this.value)" name="province" id="province">
                                        <option value="0" >请选择省份</option>
                                        @foreach($provincelists as $v)
                                            <option value="{{$v->areaCode}}">{{$v->areaName}}</option>
                                        @endforeach
                                    </select>
                                    <select class="form-control m-b" value="" onchange="getdata(this.id,this.value)" name="city" id="city">
                                        <option value="0">请选择市区</option>
                                    </select>
                                    <select class="form-control m-b" value="" name="county" id="county">
                                        <option value="0">请选择县乡</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" id="store_id" value="{{$store_id}}">
                            <input type="hidden" id="code_number" value="{{$code_number}}">
                            <input type="hidden" id="code_from" value="{{$code_from}}">
                            <div class="hr-line-dashed" hidden></div>
                        </form>
                    </div>
                </div>
                <a href="javascript:void(0)" onclick="addpost()">
                    <button style="width: 100%;height: 40px;font-size: 18px;" type="button" class="btn btn-primary">
                        下一步绑定银行卡信息
                    </button>
                </a>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')
    <script>
        function addpost() {
            /*if(!($("#product_type").val()=="003"||$("#product_type").val()=="004")){
                layer.msg('请输入正确的产品类型!');
                $("#product_type").focus();
            }else */if(!$("#id_type").val()){
                layer.msg('请输入证件类型!');
                $("#id_type").focus();
            }else if(!$("#id_no").val()){
                layer.msg('证件号必填!');
                $("#id_no").focus();
            }else if($("#id_type").val()=='01'&&!isCardNo($("#id_no").val().trim())){
                layer.msg('身份证号不合法!');
                $("#id_no").focus();
                return false;
            }else if(!$("#merchant_name").val()){
                layer.msg('商户全称必填!');
                $("#merchant_name").focus();
            }else if(!$("#alias_name").val()){
                layer.msg('商户简称必填!');
                $("#alias_name").focus();
            }/*else if(!$("#category_id").val()){
                layer.msg('门店类目必填!');
                $("#category_id").focus();
            }*/
            else if($("#thirdcate").val()=='0'){
                layer.msg('请选择三级类目!');
                $("#thirdcate").focus();
            }else if($("#thirdmcc").val()=='0'){
                layer.msg('请选择三级商户类别码!');
                $("#thirdmcc").focus();
            }
            /*else if(!$("#merchant_type_code").val()){
                layer.msg('商户类别码必填!');
                $("#merchant_type_code").focus();
            }*/else if(!$("#licence_no").val()){
                layer.msg('请输入营业执照编号!');
                $("#licence_no").focus();
            }else if(!$("#address").val()){
                layer.msg('店铺地址必填!');
                $("#address").focus();
            }else if(!$("#contact_name").val()){
                layer.msg('联系人姓名!');
                $("#contact_name").focus();
            }else if(!isChinaName($("#contact_name").val().trim())){
                layer.msg('联系人姓名不合法!');
                $("#contact_name").focus();
                return false;
            }else if(!$("#contact_phone").val()){
                layer.msg('请填写联系人手机号!');
                $("#contact_phone").focus();
            }else if (!IsTel($("#contact_phone").val().trim())) {
                layer.msg('手机号码不正确');
                return false;
            }else if(!$("#service_phone").val()){
                layer.msg('请填写客服电话!');
                $("#service_phone").focus();
            }else if (!IsTel($("#service_phone").val().trim())) {
                layer.msg('手机号码不正确');
                return false;
            }else if(!$("#user_id").val()){
                layer.msg('推广员必填!');
                $("#user_id").focus();
            }else if($("#county").val()=='0'){
                layer.msg('请选择省市区信息!');
                $("#county").focus();
            }else{
//                address=$("#province option:selected").html()+$("#city option:selected").html()+$("#county option:selected").html();
                {{--id_card_num=$("#sfzno").val().trim();--}}
                {{--service_phone=$("#service_phone").val().trim();--}}
                district=$("#city option:selected").html();
                window.location.href = "{{url('admin/webank/bindcard?')}}"  + 'id_type=' + $("#id_type").val()+ '&id_no=' + $("#id_no").val()+ '&merchant_name=' + $("#merchant_name").val() + '&merchant_type_code=' + $("#thirdmcc").val()+'&licence_no='+$('#licence_no').val()+ '&category_id=' + $("#thirdcate").val()+'&alias_name='+$('#alias_name').val()+'&address='+$('#address').val()+'&contact_name='+$('#contact_name').val()+'&contact_phone='+$('#contact_phone').val()+'&service_phone='+$('#service_phone').val() +'&user_id='+$('#user_id').val()+'&province='+$('#province').val()+'&city='+$('#city').val()+'&county='+$('#county').val()+'&district='+district+'&store_id='+$('#store_id').val()+'&code_number='+$('#code_number').val()+'&code_from='+$('#code_from').val();
                {{--window.location.href = "{{url('admin/webank/bindcard?product_type=')}}" + $("#product_type").val() + '&id_type=' + $("#id_type").val()+ '&id_no=' + $("#id_no").val()+ '&merchant_name=' + $("#merchant_name").val() + '&merchant_type_code=' + $("#merchant_type_code").val()+'&licence_no='+$('#licence_no').val()+ '&category_id=' + $("#category_id").val()+'&alias_name='+$('#alias_name').val()+'&address='+$('#address').val()+'&contact_name='+$('#contact_name').val()+'&contact_phone='+$('#contact_phone').val()+'&service_phone='+$('#service_phone').val() +'&user_id='+$('#user_id').val()+'&province='+$('#province').val()+'&city='+$('#city').val()+'&county='+$('#county').val()+'&district='+district+'&store_id='+$('#store_id').val()+'&code_number='+$('#code_number').val();--}}
            }
        }
        window.onload = get;
        function get() {
            getCategory();
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
        //获取类目
        function getcate(id,value) {
            if(id=='cate')
                sot='secondcate';
            else
                sot='thirdcate';
            $('#thirdcate option').remove();
            $('#thirdcate').append("<option value='0'>三级类目</option>");
            $.post("{{route("webankgetcate")}}", {id:value,_token: $("#token").val()}, function (data) {
                str='';
                if(sot=='secondcate'){
                    for (var k=0;k<data.length;k++) {
                        str+="<option value="+data[k].id+">"+data[k].name+"</option>";
                    }
                    $('#secondcate option').remove();
                    $('#secondcate').append("<option value='0'>二级类目</option>"+str);
                }
                else{
                    for (var k=0;k<data.length;k++) {
                        str+="<option value="+data[k].wx_category_id+"**"+data[k].ali_category_id+">"+data[k].name+"</option>";
                    }
                    $('#thirdcate option').remove();
                    $('#thirdcate').append("<option value='0'>三级类目</option>"+str);
                }

            }, "json");
        }
        //获取类别
        function getmcc(id,value) {
            if(id=='MCC')
                sot='secondmcc';
            else
                sot='thirdmcc';
            $('#thirdmcc option').remove();
            $('#thirdmcc').append("<option value='0'>三级类别</option>");
            $.post("{{route("webankgetmcc")}}", {id:value,_token: $("#token").val()}, function (data) {
                str='';
                if(sot=='secondmcc'){
                    for (var k=0;k<data.length;k++) {
                        str+="<option value="+data[k].code+">"+data[k].name+"</option>";
                    }
                    $('#secondmcc option').remove();
                    $('#secondmcc').append("<option value='0'>二级类别</option>"+str);
                }
                else{
                    for (var k=0;k<data.length;k++) {
                        str+="<option value="+data[k].code+">"+data[k].name+"</option>";
                    }
                    $('#thirdmcc option').remove();
                    $('#thirdmcc').append("<option value='0'>三级类别</option>"+str);
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