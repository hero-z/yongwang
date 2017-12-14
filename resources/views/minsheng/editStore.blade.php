@extends('layouts.publicStyle')
@section('title','商户基本信息修改')
@section('css')
@endsection
@section('content')
    <script src="{{asset('/jQuery-File-Upload/js/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js//jquery.iframe-transport.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js/jquery.fileupload.js')}}" type="text/javascript"></script>
    <script src="{{asset('uploadify/jquery.uploadify.min.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('uploadify/uploadify.css')}}">

@include('layouts.zeroModal')
   
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>修改商户{{$store->store_id}}的通道<span style="color:red">{{$pay->pay_way}}</span>信息</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">

                        <div class="form-group">
                            <label>商户简称：</label>
                            <input placeholder="流水中会看到" class="form-control" name="store_short_name" value='{{$pay->store_short_name}}' id="store_short_name" type="text">
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
            $.post(
                "{{route('ms_store_edit')}}",
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
                },
                function (result) {
                    // 成功
                    if(result.status=='1')
                    {

                        zeroModal.success({
                            content: '修改成功！',
                            contentDetail: result.message,
                            okFn: function() {
                                window.location="{{route('ms_store_edit')}}?pay_way_id="+$("#pay_id").val();
                                return;
                                // window.location='/merchant/AlipayTradePayCreate';
                            }
                        });
                    }
                    // 失败
                    else
                    {
tijiaotimes = 1;
                        zeroModal.error({
                            content: '失败',
                            contentDetail: result.message,
                            okFn: function() {
                                // window.location='http://www.baidu.com'
                                return;
                            }
                        });
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

@endsection
@endsection