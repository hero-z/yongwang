@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>修改信息</h5>
            </div>
            <span>{{session('warnning')}}</span>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                           <input type="hidden" id="store_id" value="{{$store->store_id}}" name="store_id">
                            <div class="form-group">
                                <label>店铺全称（<span style='color:red'>不要修改</span>）</label>
                                <input value="{{$store->store_name}}" class="form-control" type="text" id='store_name'>
                            </div>
                        <div class="hr-line-dashed"></div>

                        <form action="" method="post">
                            <div class="form-group">
                                <label>店铺简称（可修改）</label>
                                <input value="{{$store->merchant_short_name}}" id="merchant_short_name" class="form-control"
                                       type="text" name="merchant_short_name">
                            </div>
                            <input type="hidden" id="id" value="{{$info->id}}">
                        <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>收款码编号<span style="color:red">(修改后,原收款码将作废)</span></label>
                                <input value="{{$info->code_number}}" id="code" class="form-control"
                                       type="text" name="code">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>设置该店铺的费率：格式：0.00789</label>
                                <!-- <div>{{$store->rate}}</div> -->
                                <input value="{{$store->rate}}" id="rate" class="form-control"
                                       type="text" name="rate">
                            </div>
                        <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>渠道模式:  <?php if($store->ch_pay_auth==1) echo '是';else echo '否';?></label>
                            </div>
                            <div class="hr-line-dashed"></div>
                            @if($store->ch_pay_auth==0)
                            <div class="form-group">
                                <label>店铺秘钥</label>
                                <input value="{{$store->merchant_pwd}}" id="merchant_pwd" class="form-control"
                                       type="text" name="merchant_pwd">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户公众号app_id</label>
                                <input value="{{$store->wx_app_id}}" id="wx_app_id" class="form-control"
                                       type="text" name="wx_app_id">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>商户公众号secret</label>
                                <input value="{{$store->wx_secret}}" id="wx_secret" class="form-control"
                                       type="text" name="wx_secret">
                            </div>
                            <div class="hr-line-dashed"></div>
                            @endif

                            <div class="form-group">
                                <label>已经启用的支付方式：[如果是分店，这几项都不用管]<b style='color:red'>(商户进件时至少有一个支付方式是开通的)</b></label>
                                <?php foreach($paytype as $k=>$v): ?>
                                    <label>
                                        <input type="checkbox" name="paytype" <?php if(isset($v['status'])&&$v['status']==1){echo "checked='checked'";} ?> value="<?php echo $k; ?>" class="paytype"><?php echo current($v); ?>
                                    </label>
                                    &nbsp&nbsp&nbsp&nbsp&nbsp
                                <?php endforeach; ?>

                            </div>

                            <div class="form-group">
                        <div class="hr-line-dashed"></div>

                                <label>是否开启店铺收款</label>
                                <label>
                                    <input class="pay_status" type='radio' <?php if($store->pay_status==2) echo 'checked="checked"'; ?>  name='pay_status' value='2'/>开启
                                </label>
                                <label>
                                    <input class="pay_status" type='radio' <?php if($store->pay_status==1) echo 'checked="checked"'; ?> name='pay_status' value='1'/>关闭
                                </label>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div>
                                <button  class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                        id="tijiao" type="button">
                                    <strong>保存</strong>
                                </button>
                            </div>
                            {{csrf_field()}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="con"></div>
    @if($info)
    <div>
        <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(300)->generate(url('api/pufa/payway?code_number='.$info->code_number.'&store_id='.$store->store_id))) !!} ">
    </div>
    @endif
@section('js')
    <script>

        $(function(){
            $("#tijiao").click(function(){
                layer.confirm('确定要修改吗？', {
                            btn: ['确定', '取消'] //按钮
                        },function(){

                            // 循环取出支付方式的checkbox的值
                            var paytype=new Array();
                            $('.paytype').each(function(){
                                if($(this).is(':checked'))
                                {
                                    paytype.push($(this).val());
                                }
                            });
                            paytype=paytype.join(',');


                            // alert(typeof(paytype))
                            // return;

                                $.post("{{route('storeEdit')}}",
                                {
                                    _token: '{{csrf_token()}}',
                                    merchant_short_name: $("#merchant_short_name").val(),
                                    store_name: $("#store_name").val(),
                                    rate: $("#rate").val(),
                                    merchant_pwd: $("#merchant_pwd").val(),
                                    wx_app_id: $("#wx_app_id").val(),
                                    wx_secret: $("#wx_secret").val(),
                                    pay_status: $(".pay_status:checked").val(),
                                    paytype: paytype,
                                    id:$("#id").val(),
                                    codes:$("#code").val(),
                                    store_id: $("#store_id").val()
                                },
                                function (result) {
                                    if (result.status == 2) {
                                        layer.alert(result.message, {icon: 6});

                                        setTimeout(function(){
                                            window.location.reload();
                                            // window.location.href="{{route('storelist')}}";
                                        }, 2000);

                                    } else {
                                        layer.alert(result.message, {icon: 5});

                                    }
                                }, "json");
                            },function(){

                            });

            });
        });
    </script>
@endsection
@endsection