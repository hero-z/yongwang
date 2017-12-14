@extends('layouts.publicStyle')
@section('title','商户注册')
@section('css')
@endsection
@section('content')
    <script src="{{asset('/jQuery-File-Upload/js/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js//jquery.iframe-transport.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js/jquery.fileupload.js')}}" type="text/javascript"></script>
    <script src="{{asset('uploadify/jquery.uploadify.min.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('uploadify/uploadify.css')}}">
   
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>商户入驻民生银行（支付宝+微信）</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                            <input type="hidden" id="user_id" value="{{$user_id}}">
                            <input type="hidden" id="code_number" value="{{$code_number}}">
<!-- 
                        <div class="form-group">
                            <label>行业类别</label>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="industrId" id="category_id">
                                    <option value='0'>请选择分类</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

 -->


                        <div class="form-group">
                            <label>清算方式：</label>
                            <div class='radio-inline'>
                                <label>
                                    <input type='radio' name='tradetype' value='1' checked='checked'> T0直清
                                </label>
                                &nbsp&nbsp&nbsp&nbsp
                                <label>
                                    <input type='radio' name='tradetype' value='2'> T1直清
                                </label>
                            </div>

                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <label>商户全称：</label>
                            <input placeholder="与营业执照相同" class="form-control" name="merchantName" id="merchantName" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>商户简称：</label>
                            <input placeholder="流水中会看到" class="form-control" name="shortName" id="shortName" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>



<div class="form-group">
    <label>门店所在省市区</label>
    <div id="region">
        <select style="width:100px; " id="province__" name="province__">
            <option value="0">请选择省份</option>
        </select>
        <select id="city__" name="city__">
            <option value="0">请选择城市</option>
        </select>
        <select id="district__" name="district__">
            <option value="0">请选择区</option>
        </select>
    </div>
</div>            
<div class="hr-line-dashed"></div>




                        <div class="form-group">
                            <label>商户地址（上面省市区不用再写）：</label>
                            <input placeholder="" class="form-control" name="merchantAddress" id="merchantAddress" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>手机号：</label>
                            <input placeholder="填写银行卡绑定的手机号" class="form-control" name="servicePhone" id="servicePhone" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>联系人类型：</label>
                            <div class='radio-inline'>
                                <label>
                                    <input type='radio' name='usertype' value='1' checked='checked'> 法人
                                </label>
                                &nbsp&nbsp&nbsp&nbsp
                                <label>
                                    <input type='radio' name='usertype' value='2'> 实际控制人
                                </label>
                                &nbsp&nbsp&nbsp&nbsp
                                <label>
                                    <input type='radio' name='usertype' value='3'> 代理人
                                </label>
                                &nbsp&nbsp&nbsp&nbsp
                                <label>
                                    <input type='radio' name='usertype' value='0'> 其他
                                </label>
                            </div>

                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>行业类别</label>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="category_1" id="category_1">
                                    <option value='0'>请选择分类</option>
                                </select>
                            </div>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="category_2" id="category_2">
                                    <option value='0'>请选择分类</option>
                                </select>
                            </div>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="category" id="category">
                                    <option value='0'>请选择分类</option>
                                </select>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>身份证号码：</label>
                            <input placeholder="" class="form-control" name="idCard" id="idCard" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

<div id='bank_t1'>

                        <div class="form-group">
                            <label>开户行名称：</label>
                            <input placeholder="" class="form-control" name="bankType" id="bankType" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>



                        <div class="form-group">
                            <label>联行号：</label>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="bankName" id="bankName">
                                    <option value='0'>请选择分类</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

</div>

                        <div class="form-group">
                            <label>银行卡号：</label>
                            <input placeholder="" class="form-control" name="accNo" id="accNo" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>持卡人姓名：</label>
                            <input placeholder="" class="form-control" name="accName" id="accName" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>备注：</label>
                            <input placeholder="" class="form-control" name="ext" id="ext" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <input type='hidden' id='code_number' value='{{$code_number}}'>
                        <input type='hidden' id='user_id' value='{{$user_id}}'>
                        
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
@endsection






@section('js')
    <script type="text/javascript">

        $(function(){
/////////////////////////////////////////////////////////
            function cate(id,htmlid)
            {

                $.post(
                    "{{route('ms_cate')}}",
                    {
                        _token: '{{csrf_token()}}',
                        id:id
                    },
                    function (data) {
                        $("#"+htmlid).children().remove();
                        $("#"+htmlid).append("<option value='0'>请选择</option>");
                        if (!data) {
                            return;
                        }
                        var str = '';
                        for (var key in data) {
                            str += "<option value='" + data[key].id + "'>" + data[key].name + "</option>";
                        }
                        $("#"+htmlid).append(str);

                    }, "json");
            }
            cate('0','category_1');

            $('#category_1').change(function(){
                var pid = $(this).val();
                cate(pid,'category_2');
            })

            $('#category_2').change(function(){
                var pid = $(this).val();
                cate(pid,'category');
            })

        })
/////////////////////////////////////////////////////////
        $(function(){
            $("input[name='tradetype']").change(function(){
                var zhi=$("input[name='tradetype']:checked").val();

                if(zhi==1)
                {
                    $('#bank_t1').hide();                    
                }
                else if(zhi==2)
                {
                    $('#bank_t1').show();                    
                }
            });
            $("input[name='tradetype']").trigger('change');
        })

////////////////////////联行号//////////////////
        $(function(){
            $('#bankType').blur(function(){
                if(!$("#bankType").val())
                {
                    alert('请先填写开户行名称！');return;
                }

                $.post(
                    "{{route('ms_bank')}}",
                    {
                        // 推荐人
                        _token: '{{csrf_token()}}',
                        bank_name: $("#bankType").val()
                    },
                    function (data) {
                        $("#bankName").children().remove();
                        $("#bankName").append("<option value='0'>请选择</option>");

                        if (!data) {
                            return;
                        }
                        var str = '';
                        for (var key in data) {
                            str += "<option value='" + data[key].id + "'>" + data[key].bank_name + "</option>";
                        }
                        $("#bankName").append(str);

                    }, "json");

            });
        })

        var tijiaotimes = 1;
        //表单提交=========start======
        function addpost() {
            if (tijiaotimes != 1) {
                alert('请不要重复提交！');
                return;
            }
            tijiaotimes = 2;


var preaddress=$('#province__ option:selected').text()+'(省)'+$('#city__ option:selected').text()+'(市)'+$('#district__ option:selected').text()+'(区)';



            $.post(
                "{{route('ms_info')}}",
                {
                    // 推荐人
                    _token: '{{csrf_token()}}',
                    // accountType: $("input[name='accountType']:checked").val(),
                    code_number: $("#code_number").val(),
                    user_id: $("#user_id").val(),
                    merchantName: $("#merchantName").val(),
                    shortName: $("#shortName").val(),
                    merchantAddress: preaddress + $("#merchantAddress").val(),
                    servicePhone: $("#servicePhone").val(),
                    category: $("#category").val(),
                    // bankType: $("#bankType").val(),
                    bankName: $("#bankName").val(),
                    idCard: $("#idCard").val(),
                    accNo: $("#accNo").val(),
                    accName: $("#accName").val(),
                    tradetype: $("input[name='tradetype']:checked").val(),
                    usertype: $("input[name='usertype']:checked").val(),
                    ext: $("#ext").val(),
                    district: $("#district__").val()

                },
                function (result) {
                    // tijiaotimes = 1;alert(result.status);return;

                    if (result.status == '1') {
                        window.location.href = "{{route('ms_info_success')}}";
                    }
                    else
                    {
                        tijiaotimes = 1;
                        alert(result.message);
                    }

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
        // 已经选择的编码    省       市    区
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
                                if(data[key].code==which)
                                {
                                    str += "<option selected='selected' value='" + data[key].code + "'>" + data[key].name + "</option>";
                                }
                                else
                                {
                                    str += "<option value='" + data[key].code + "'>" + data[key].name + "</option>";
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
        var url='{{route("ms_region")}}';
        // 区
        $('#city__').change(function(){
            var pid=$(this).val();
            var cin={
                level:'4',
                pid:pid
            };
            toserver(url,cin,'district__',choose[2]);
        })
        // 市
        $('#province__').change(function(){
            var pid=$(this).val();
            var cin={
                level:'3',
                pid:pid
            };
            toserver(url,cin,'city__',choose[1]);
        }) 
        // 省
        var cin={
            level:'2',
            pid:'1'//默认
        };
        toserver(url,cin,'province__',choose[0]);
    });
})();
</script>
@endsection
