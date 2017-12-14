@extends('layouts.publicStyle')
@section('title','服务商系统商户基本信息修改')
@section('css')
@endsection
@section('content')
@include('layouts.zeroModal')
    <script src="{{asset('/jQuery-File-Upload/js/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js//jquery.iframe-transport.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js/jquery.fileupload.js')}}" type="text/javascript"></script>
    <script src="{{asset('uploadify/jquery.uploadify.min.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('uploadify/uploadify.css')}}">
   
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>服务商系统商户基本信息修改</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">

                        <div class="form-group">
                            <label>商户简称：</label>
                            <input placeholder="流水中会看到" class="form-control" name="store_short_name" value='{{$store->store_short_name}}' id="store_short_name" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>商铺联系人：</label>
                            <input placeholder="" class="form-control" name="store_user" id="store_user" value='{{$store->store_user}}' type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>联系人手机号：</label>
                            <input placeholder="填写银行卡绑定的手机号" class="form-control" name="store_phone" value="{{$store->store_phone}}" id="store_phone" type="text">
                        </div>
                        <div class="hr-line-dashed"></div> 


                        <div class="form-group">
                            <label>是否开启店铺收款</label>
                            <label>
                                <input id="status" type='radio' <?php if($store->status==2) echo 'checked="checked"'; ?>  name='status' value='2'/>开启
                            </label>
                            <label>
                                <input id="status" type='radio' <?php if($store->status==1) echo 'checked="checked"'; ?> name='status' value='1'/>关闭
                            </label>
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <label>合作方标识：</label>
                            <div>{{$store->cooperator}}</div>
                        </div>
                        <div class="hr-line-dashed"></div> 

                        <div class="form-group">
                            <label>店铺费率：<span style='color:red'>（具体费率以单个支付通道的显示为准）</span></label>
                            <div>单笔费用：{{$store->draw_fee}}元；单笔费率：{{$store->trade_rate}}</div>
                        </div>
                        <div class="hr-line-dashed"></div> 

                        <?php if($store->pid!='0'): ?>
                        <div class="form-group">
                            <label>所属总店</label>
                            <div>{{$store->pid}}</div>
                        </div>
                        <div class="hr-line-dashed"></div> 
                        <?php endif; ?>


                        <div class="form-group">
                            <label>推广员：</label>
                            <div>
                                <?php if(isset($recommenders[$store->user_id])){echo $recommenders[$store->user_id];}else{echo '推广员不存在';} ?>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div> 


                        <input type='hidden' name='store_id' id='store_id' value='{{$store->store_id}}'/>
                        
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
    
    @if($info)
    <div>
        <!-- <div>{{url('api/minsheng/payway?code_number='.$info->code_number.'&store_id='.$store->store_id)}}</div> -->

        <h1>支付宝和微信支付的合成码</h1>
        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(300)->generate(url('api/minsheng/payway?code_number='.$info->code_number.'&store_id='.$store->store_id))) !!} ">
    </div>
    @endif
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
                zeroModal.confirm({
                        content: '请不要重复提交！',
                        // contentDetail: result.message,
                        okFn: function() {
                            return;
                        }
                    })                
                return;
            }
            tijiaotimes = 2;
            $.post(
                "{{route('ms_normalEdit')}}",
                {
                    // 推荐人
                    _token: '{{csrf_token()}}',
                    store_id: $("#store_id").val(),
                    status: $("#status").val(),
                    store_short_name: $("#store_short_name").val(),
                    store_phone: $("#store_phone").val(),
                    store_user: $("#store_user").val()
                },
                function (result) {

                    // 成功
                    if(result.status=='1')
                    {

                        zeroModal.success({
                            content: '修改成功！',
                            contentDetail: result.message,
                            okFn: function() {
                                window.location.href = "{{route('ms_normalEdit')}}?store_id="+$("#store_id").val();
                                // window.location="{{route('ms_store_edit')}}?pay_way_id="+$("#pay_id").val();
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