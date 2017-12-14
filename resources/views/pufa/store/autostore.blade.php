@extends('layouts.publicStyle')
@section('title','浦发商户注册')
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
                <h5>商户自助注册</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <input type="hidden" id="code_number" value="<?php echo $_GET['code_number']?>">

<?php if(!empty($recommender['user_id'])): ?>
                            <input type="hidden" id="user_id" value="{{$recommender['user_id']}}">
<?php else: ?>
                        <div class="form-group">
                            <label>推广员信息(数字标识)</label>
                            <div class="col-sm-10">
                                <input placeholder="" class="form-control" name="user_id" id="user_id" type="text">
                            </div>
                        </div>
<?php endif; ?>
                        <div class="hr-line-dashed"></div>





                        
                        <div class="form-group">
                            <label>行业类别</label>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="industrId" id="category_id">
                                    <option value='0'>请选择分类</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="form-group">
                                <label>商户经营类型</label>

                                <div class='radio-inline'>
                                    <label>
                                        <input type='radio' name='mchDealType' value='1' checked='checked'> 实体
                                    </label>
                                    &nbsp&nbsp&nbsp&nbsp
                                    <label>
                                        <input type='radio' name='mchDealType' value='2'> 虚拟
                                    </label>
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group" hidden>
                                <label>渠道授权交易</label>

                                <div class='radio-inline'>
                                    <label>
                                        <input type='radio' name='chPayAuth' value='1' checked='checked'> 授权
                                    </label>
                                    &nbsp&nbsp&nbsp&nbsp
                                    <label>
                                        <input type='radio' name='chPayAuth' value='0'> 不授权
                                    </label>
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <label>商户名称</label>
                            <input placeholder="格式:地区+名称+行业 如:上海东方大酒店" class="form-control" name="merchantName"
                                   id="merchantName" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>商户简称</label>
                            <input placeholder="" class="form-control" name="merchantShortName" id="merchantShortName"
                                   type="text">
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label>商铺营运人</label>
                            <input placeholder="" class="form-control" name="principal" id="principal" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>商铺营业执照编号：</label>
                            <input placeholder="" class="form-control" name="business_license" id="business_license" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <label>手机号码：</label>
                            <input placeholder="" class="form-control" name="tel" id="tel" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <label style="color:red;">email邮箱：用于接收密码</label>
                            <input placeholder="" class="form-control" name="email" id="email" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label>门店所在省市区</label>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="province__" id="province__">
                                    <option value='0'>请选择省份</option>
                                </select>
                            </div>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="city__" id="city__">
                                    <option value='0'>请选择城市</option>
                                </select>
                            </div>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="district__" id="district__">
                                    <option value='0'>请选择区</option>
                                </select>
                            </div>
                        </div>
{{--<div class="form-group">--}}
    {{--<label>门店所在省市区</label>--}}
    {{--<div id="region">--}}
        {{--<select style="width:100px; " id="province__" name="province__">--}}
            {{--<option value="0">请选择省份</option>--}}
        {{--</select>--}}
        {{--<select id="city__" name="city__">--}}
            {{--<option value="0">请选择城市</option>--}}
        {{--</select>--}}
        {{--<select id="district__" name="district__">--}}
            {{--<option value="0">请选择区</option>--}}
        {{--</select>--}}
    {{--</div>--}}
{{--</div>            --}}
<div class="hr-line-dashed"></div>




                        <div class="form-group">
                            <label>商铺所在地址：</label>
                            <input placeholder="只需要填写省市区后面的" class="form-control" name="address" id="address" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>身份证号码：</label>
                            <input placeholder="" class="form-control" name="idCode" id="idCode" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label>开户银行</label>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="bankId" id="bankId">
                                    <option value='0'>选择银行</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>银行卡所在省份</label>
                            <div class="col-sm-10">
                                <select class="form-control m-b province" name="province" id="province">
                                    <option value='0'>选择省份</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label>银行卡所在市区</label>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="city" id="city">
                                    <option value='0'>选择市区</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>



                        <div class="form-group">
                            <label>联行号</label>
                            {{--<br/>--}}
                            {{--&nbsp&nbsp&nbsp&nbsp&nbsp <span>过滤已经搜到的联行号：</span><input placeholder="" id="sou" type="text">--}}

                            {{--&nbsp&nbsp&nbsp&nbsp&nbsp--}}
                            {{--<span>精确条件搜索：</span>--}}
                            {{--<input placeholder="" id="super_sou" type="text">--}}



                            <div class="col-sm-10">
                                <select class="form-control m-b" name="contactLine" id="contactLine">
                                    <option value='0'>请选择</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label>银行卡预留手机号码</label>
                            <input placeholder="" class="form-control" name="tel2" id="tel2" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>
<script type="text/javascript">

    $(function(){


        $('#city').blur(function(){
            var key_word=$("#bankId option:selected").html();
            var key_word2=$("#city option:selected").html();
            if(!key_word)
            {
                return;
            }
            // 获取联行号
            $.post(
                "{{route('pufabankrelation')}}",
                {_token: $("#token").val(), keyname: key_word,keyname2: key_word2},
                function (data) {
                    $("#contactLine").children().remove();
                    $("#contactLine").append("<option value='0'>请选择</option>");

                    if (!data) {
                        return;
                    }

                    var str = '';
                    for (var key in data) {
                        str += "<option value='" + data[key].bankid + "'>" + data[key].bankname + "</option>";
                    }
                    $("#contactLine").append(str);

                }, "json");




        })


    })

</script>
                        
                        <div class="form-group">
                            <label>账户类型</label>

                            <div class='radio-inline'>
                                <label>
                                    <input type='radio' name='accountType' value='1' checked='checked'> 企业
                                </label>
                                &nbsp&nbsp&nbsp&nbsp
                                <label>
                                    <input type='radio' name='accountType' value='2'> 个人
                                </label>
                            </div>

                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label>开户支行名称</label>
                            <input placeholder="" class="form-control" name="bankName" id="bankName" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <label>银行卡号</label>
                            <input placeholder="" class="form-control" name="accountCode" id="accountCode" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>



<!--                         <div class="form-group">
                            <label>备注：</label>
                            <input placeholder="" class="form-control" name="remark" id="remark" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

 -->
                        <div class="form-group">
                            <label>营业执照</label>
                            <input type="hidden" required="required" size="50" name="license" id="license">
                            <input type="hidden" required="required" size="50" name="license_pf" id="license_pf">
                            <!-- 图片上传按钮 -->
                            <input id="fileupload1" type="file" name="image" data-url="{{route('uploadImagePufa')}}"
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
                            <label>身份证正面</label>
                            <input type="hidden" required="required" size="50" name="indentityPhoto_a"
                                   id="indentityPhoto_a">
                            <input type="hidden" required="required" size="50" name="indentityPhoto_a_pf"
                                   id="indentityPhoto_a_pf">
                            <!-- 图片上传按钮 -->
                            <input id="fileupload2" type="file" name="image" data-url="{{route('uploadImagePufa')}}"
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


                        <div class="form-group">
                            <label>身份证背面</label>
                            <input type="hidden" required="required" size="50" name="indentityPhoto_b"
                                   id="indentityPhoto_b">
                            <input type="hidden" required="required" size="50" name="indentityPhoto_b_pf"
                                   id="indentityPhoto_b_pf">
                            <!-- 图片上传按钮 -->
                            <input id="fileupload3" type="file" name="image" data-url="{{route('uploadImagePufa')}}"
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
                            <label>手持身份证照片</label>
                            <input type="hidden" required="required" size="50" name="indentityPhoto_c"
                                   id="indentityPhoto_c">
                            <input type="hidden" required="required" size="50" name="indentityPhoto_c_pf"
                                   id="indentityPhoto_c_pf">
                            <!-- 图片上传按钮 -->
                            <input id="fileupload4" type="file" name="image" data-url="{{route('uploadImagePufa')}}"
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


                    </div>
                </div>
                <button style="width: 100%;height: 40px;font-size: 18px;" type="button" id='tijiao'
                        class="btn btn-primary">
                    确认信息提交资料
                </button>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')



<script type="text/javascript">
    $(function(){
        $('#sou').blur(function(){
            var sou=$('#sou').val();
            rule=new RegExp(sou); 
            $('#contactLine option').each(function(){
                var str=$(this).text();

                if(rule.test(str))
                {
                    $(this).show();
                }
                else
                {
                    $(this).hide();
                }
            })
        })
    })
</script>



    <script type="text/javascript">
        // 图片上传==========start====
        publicfileupload("#fileupload1", ".files1", "#license", "#license_pf", '.up_progress1 .progress-bar1', ".up_progress1");
        publicfileupload("#fileupload2", ".files2", "#indentityPhoto_a", "#indentityPhoto_a_pf", '.up_progress2 .progress-bar2', ".up_progress2");
        publicfileupload("#fileupload3", ".files3", "#indentityPhoto_b", "#indentityPhoto_b_pf", '.up_progress3 .progress-bar3', ".up_progress3");
        publicfileupload("#fileupload4", ".files4", "#indentityPhoto_c", "#indentityPhoto_c_pf", '.up_progress3 .progress-bar4', ".up_progress3");
        function publicfileupload(fileid, imgid, postimgid, postimgid_pf, class1, class2) {
            //图片上传

            $(fileid).fileupload({
                formData: {_token: "{{csrf_token()}}", code_number: $("#code_number").val()},
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
                    if (d.status == 1) {
                        var imgshow = '<div class="images_zone"><input type="hidden" name="imgs[]" value="' + d.image_url + '" /><span><img src="' + d.image_url + '"  /></span><a href="javascript:;">删除</a></div>';
                        jQuery(imgid).append(imgshow);
                        jQuery(postimgid).val(d.image_url);
                        $(postimgid_pf).val(d.pf_image_url);
                        return;
                    }
                    alert(d.message);
                },
                progressall: function (e, data) {
                    // console.log(data);
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
        // 图片上传==========end====
    </script>




    <script type="text/javascript">

        //获取分类======start==============
        function getCategory() {
            $.post(
                "{{route('PFCate')}}",
                {_token: $("#token").val()},
                function (data) {
                    if (!data) {
                        return;
                    }

                    var str = '';
                    for (var key in data) {
                        str += "<option value='" + data[key].industry + "'>" + data[key].rawstr + "</option>";
                    }
                    $("#category_id").append(str);

                }, "json");
        }

        $(function () {
            getCategory();
        });
        //获取分类======end==============


        //获取银行====start===
        function getBank() {
            $.post(
                "{{route('pufabank')}}",
                {_token: $("#token").val()},
                function (data) {
                    if (!data) {
                        return;
                    }

                    var str = '';
                    for (var key in data) {
                        str += "<option value='" + data[key].id + "'>" + data[key].bankname + "</option>";
                    }
                    $("#bankId").append(str);

                }, "json");
        }

        $(function () {
            getBank();
            // 获取联行号
            $('#bankId').change(function(){
                var keyname = $(this).find("option:selected").text();
                if (!keyname) {
                    return;
                }
                // 获取联行号
                $.post(
                    "{{route('pufabankrelation')}}",
                    {_token: $("#token").val(), keyname: keyname},
                    function (data) {
                        $("#contactLine").children().remove();
                        $("#contactLine").append("<option value='0'>请选择</option>");

                        if (!data) {
                            return;
                        }

                        var str = '';
                        for (var key in data) {
                            str += "<option value='" + data[key].bankid + "'>" + data[key].bankname + "</option>";
                        }
                        $("#contactLine").append(str);

                    }, "json");
            });

        });


        //获取银行====end===

        //获取省份====start===
        function getProvince() {
            $.post(
                "{{route('province')}}",
                {_token: $("#token").val()},
                function (data) {
                    if (!data) {
                        return;
                    }

                    var str = '';
                    for (var key in data) {
                        str += "<option value='" + data[key].id + "'>" + data[key].name + "</option>";
                    }
                    $(".province").append(str);

                }, "json");
        }

        $(function () {
            getProvince();
        });
        //获取省份====end===

        //获取市区====start===
        $("#province").change(function () {
                var pid = $(this).val();

                if (!pid) {
                    return;
                }
                $.post(
                    "{{route('city')}}",
                    {_token: $("#token").val(), pid: pid},
                    function (data) {
                        $("#city").children().remove();
                        $("#city").append("<option value='0'>选择市区</option>");

                        if (!data) {
                            return;
                        }

                        var str = '';
                        for (var key in data) {
                            str += "<option value='" + data[key].id + "'>" + data[key].name + "</option>";
                        }
                        $("#city").append(str);

                    }, "json");


            }
        );
        //获取市区====end===




        var tijiaotimes = 1;
        //表单提交=========start======
        function addpost() {
            if (tijiaotimes != 1) {
                alert('服务器正在努力处理，请不要重复提交！');
                return;
            }
            var preaddress=$('#province__ option:selected').text()+'(省)'+$('#city__ option:selected').text()+'(市)'+$('#district__ option:selected').text()+'(区)';
            tijiaotimes = 2;
            $.post(
                "{{route('PFautoStorePost')}}",
                {
                    // 推荐人
                    user_id: $("#user_id").val(),

                    _token: '{{csrf_token()}}',
                    code_number: $("#code_number").val(),

                    merchantName: $("#merchantName").val(),
                    mchDealType: $("input[name='mchDealType']:checked").val(),
                    chPayAuth: $("input[name='chPayAuth']:checked").val(),
                    license: $("#license").val(),
                    license_pf: $("#license_pf").val(),
                    merchantShortName: $("#merchantShortName").val(),
                    industrId: $("#category_id").val(),
                    province: $("#province").val(),
                    city: $("#city").val(),
                    address: $("#address").val(),
                    tel: $("#tel").val(),
                    email: $("#email").val(),
                    idCode: $("#idCode").val(),
                    indentityPhoto_a: $("#indentityPhoto_a").val(),
                    indentityPhoto_b: $("#indentityPhoto_b").val(),
                    indentityPhoto_c: $("#indentityPhoto_c").val(),
                    indentityPhoto_a_pf: $("#indentityPhoto_a_pf").val(),
                    indentityPhoto_b_pf: $("#indentityPhoto_b_pf").val(),
                    indentityPhoto_c_pf: $("#indentityPhoto_c_pf").val(),
                    principal: $("#principal").val(),

                    // 接口升级---加入店铺所在省市区
                    district:$('#district__').val(),
                    business_license:$('#business_license').val(),

                    preaddress:preaddress,


                    // bankld:$("input[name='bankld']:checked").val(),
                    bankId: $("#bankId").val(),
                    accountCode: $("#accountCode").val(),
                    accountType: $("input[name='accountType']:checked").val(),
                    bankName: $("#bankName").val(),
                    tel2: $("#tel2").val(),
                    // remark: $("#remark").val(),
                    contactLine: $("#contactLine").val()

                },
                function (result) {

                    if (result.status == 2) {
                        window.location.href = "{{route('storeSuccess')}}"+'?type='+$("input[name='chPayAuth']:checked").val();
                    }

                    tijiaotimes = 1;
                    layer.msg(result.message);
                    return;

                }, "json");

        }
        //表单提交=========end======


        $(function () {
            $('#tijiao').on('click', function () {
                addpost();
            });
        })


    </script>




<script type="text/javascript">
(function(){
    $(function(){   
        // 已经选择的自增长id    省       市    区
        var choose=new Array('3203=','3204=','3205=');//已选中的自增长id

        function toserver(url,cin,where,which)
        { 
            $.ajax({
                url:url,
                async:false,
                type:'post',
                cache:false,
                dataType:'json',
                data:
                    {
                     _token:'{{csrf_token()}}',
                     async:false,   
                     pid:cin.pid,   
                     level:cin.level
                    },
                success:function(cout)
                    {
                        $("#"+where).children().remove();
                        // 有数据
                        if(cout['status']=='1')
                        {
                            var str = '';
                            var data=cout['data'];
                            for (var key in data) {
                                if(data[key].id==which)
                                {
                                    str += "<option selected='selected' value='" + data[key].id + "'>" + data[key].name + "</option>";
                                }
                                else
                                {
                                    str += "<option value='" + data[key].id + "'>" + data[key].name + "</option>";
                                }
                            }
                            $("#"+where).append(str);
                        }
                        // 没数据
                        else
                        {
                            $("#"+where).append('<option value="0">无数据</option>');

                        }
                        $('#'+where).trigger('change');
                    },
                error:function(){alert('没有获取到省市区信息！')}
            });
        }
        // 请求地址
        var url='{{route("pf_region")}}';
        // 区
        $('#city__').change(function(){
            var pid=$(this).val();
            var cin={
                level:'3',
                pid:pid
            };
            toserver(url,cin,'district__',choose[2]);
        })
        // 市
        $('#province__').change(function(){
            var pid=$(this).val();
            var cin={
                level:'2',
                pid:pid
            };
            toserver(url,cin,'city__',choose[1]);
        }) 
        // 省
        var cin={
            level:'1',
            pid:'0'
        };
        toserver(url,cin,'province__',choose[0]);
    });
})();
</script>

@endsection
@endsection