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
                <h5>商户入驻民生银行，该通道<span style="color:red">{{$pay->pay_way}}</span>入驻失败原因：<span style='color:red'>{{$pay->remark}}</span></h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">

                        <div class="form-group">
                            <label>商户全称：</label>
                            <input placeholder="与营业执照相同" class="form-control" name="store_name" id="store_name" value="{{$pay->store_name}}" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>商户简称：</label>
                            <input placeholder="流水中会看到" class="form-control" name="store_short_name" value='{{$pay->store_short_name}}' id="store_short_name" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>联系人名称：</label>
                            <input placeholder="联系人名称" class="form-control" name="contact_name" value='{{$pay->contact_name}}' id="contact_name" type="text">
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
                            <label>商户地址：</label>
                            <input placeholder="" class="form-control" name="store_address" value='{{$pay->store_address}}' id="store_address" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>手机号：</label>
                            <input placeholder="填写银行卡绑定的手机号" class="form-control" name="store_phone" value="{{$pay->store_phone}}" id="store_phone" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>


                            <div class="form-group">
                                <label>入驻类目--请选择3级分类</label>
                                <div class="col-sm-10">
                                    <select class="form-control m-b" name="category" id="category">
                                        @foreach($allcate as $cate)
                                            <option value="@if($pay->pay_way=='ZFBZF'){{trim($cate["ali_cate"])}}@else{{trim($cate["wx_cate"])}}@endif" @if(isset($cate['choice'])) selected @endif>
                                                @if($cate['level']==2)--@elseif($cate['level']==3)3----@endif{{$cate['name']}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>



                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>身份证号码：</label>
                            <input placeholder="" class="form-control" name="id_card" id="id_card" value='{{$pay->id_card}}' type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

<?php if($store->cooperator==$msconfig->cooperator_t1): ?>

                        <div class="form-group">
                            <label>开户行名称：</label>
                            <input placeholder="" class="form-control" name="bankType" id="bankType" value='{{$pay->bank_name}}' type="text">
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <label>联行号：</label>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="bankName" id="bankName">
                                    <option value='{{$pay->bank_type}}'>{{$pay->bank_name}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

<?php endif; ?>

                        <div class="form-group">
                            <label>银行卡号：</label>
                            <input placeholder="" class="form-control" name="bank_no" id="bank_no" value='{{$pay->bank_no}}' type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>持卡人姓名：</label>
                            <input placeholder="" class="form-control" name="store_user" id="store_user" value='{{$pay->store_user}}' type="text">
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <label>联系人类型：</label>
                            <div class='radio-inline'>
                                <label>
                                    <input type='radio' name='usertype' <?php if($pay->usertype==1){echo 'checked="checked"';} ?> value='1'> 法人
                                </label>
                                &nbsp&nbsp&nbsp&nbsp
                                <label>
                                    <input type='radio' name='usertype' <?php if($pay->usertype==2){echo 'checked="checked"';} ?>  value='2'> 实际控制人
                                </label>
                                &nbsp&nbsp&nbsp&nbsp
                                <label>
                                    <input type='radio' name='usertype' <?php if($pay->usertype==3){echo 'checked="checked"';} ?>  value='3'> 代理人
                                </label>
                                &nbsp&nbsp&nbsp&nbsp
                                <label>
                                    <input type='radio' name='usertype' <?php if($pay->usertype==0){echo 'checked="checked"';} ?>  value='0'> 其他
                                </label>
                            </div>

                        </div>
                        <div class="hr-line-dashed"></div>

                        <input type='hidden' name='pay_id' id='pay_id' value='{{$pay->id}}'/>
                        
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
                "{{route('ms_saveStoreAdd')}}",
                {
                    // 推荐人
                    _token: '{{csrf_token()}}',
                    pay_id: $("#pay_id").val(),
                    store_name: $("#store_name").val(),
                    store_short_name: $("#store_short_name").val(),
                    store_address: $("#store_address").val(),
                    store_phone: $("#store_phone").val(),
                    category: $("#category").val(),
                    // bankType: $("#bankType").val(),
                    bankName: $("#bankName").val(),//联行号  数字
                    id_card: $("#id_card").val(),
                    bank_no: $("#bank_no").val(),
                    store_user: $("#store_user").val(),
                    contact_name: $("#contact_name").val(),

                    usertype: $("input[name='usertype']:checked").val(),

                    preaddress:preaddress,
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
        var choose=new Array('{{$area['province']}}','{{$area['city']}}','{{$area['district']}}');

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
@endsection