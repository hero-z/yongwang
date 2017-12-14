@extends('layouts.paipai')
@section('title','设置通道')
@section('content')

            <div class="widget am-cf">
                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">添加设备</div>
                    </div>
                    <form id="form0" class="am-form tpl-form-line-form" action="{{url('admin/yirui/paipai/add')}}" method="post">
                        <label for="user-phone" class="">绑定店铺</label>
                        <div class="doc-example">
                            <select data-am-selected="{searchBox: 1,maxHeight: 200}" style="display: none;" id='store_id' name="store_id" required>
                                <option value=""></option>
                                @foreach($all_store['unionpay'] as $v)
                                    @if($v->store_name)
                                        <option <?php if(isset($data->store_id)&&$data->store_id==$v->store_id) echo 'selected="selected"'; ?> value="{{$v->store_id}}">{{$v->store_name}}(银联)</option>
                                    @endif
                                @endforeach
                                @foreach($all_store['oali'] as $v)
                                    @if($v->store_name)
                                        <option <?php if(isset($data->store_id)&&$data->store_id==$v->store_id) echo 'selected="selected"'; ?> value="{{$v->store_id}}">{{$v->store_name}}(支付宝当面付)</option>
                                    @endif
                                @endforeach
                                @foreach($all_store['sali'] as $v)
                                    @if($v->store_name)
                                        <option <?php if(isset($data->store_id)&&$data->store_id==$v->store_id) echo 'selected="selected"'; ?> value="{{$v->store_id}}">{{$v->store_name}}(支付宝口碑)</option>
                                    @endif
                                @endforeach
                                @foreach($all_store['weixin'] as $v)
                                    @if($v->store_name)
                                        <option <?php if(isset($data->store_id)&&$data->store_id==$v->store_id) echo 'selected="selected"'; ?> value="{{$v->store_id}}">{{$v->store_name}}(微信)</option>
                                    @endif
                                @endforeach
                                @foreach($all_store['pingan'] as $v)
                                    @if($v->store_name)
                                        <option <?php if(isset($data->store_id)&&$data->store_id==$v->store_id) echo 'selected="selected"'; ?> value="{{$v->store_id}}">{{$v->store_name}}(平安银行)</option>
                                    @endif
                                @endforeach
                                @foreach($all_store['pufa'] as $v)
                                    @if($v->store_name)
                                        <option <?php if(isset($data->store_id)&&$data->store_id==$v->store_id) echo 'selected="selected"'; ?> value="{{$v->store_id}}">{{$v->store_name}}(浦发银行)</option>
                                    @endif
                                @endforeach
                                @foreach($all_store['weizhong'] as $v)
                                    @if($v->store_name)
                                        <option <?php if(isset($data->store_id)&&$data->store_id==$v->store_id) echo 'selected="selected"'; ?> value="{{$v->store_id}}">{{$v->store_name}}(微众)</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>


                        <label for="user-phone" class="">绑定收银员</label>
                        <div class="doc-example">
                            <select data-am-selected="{searchBox: 1,maxHeight: 200}" style="display: none;" name="m_id" id="m_id" required>
                                <option value=""></option>


                            </select>
                        </div>
                            <label for="user-phone" class="">设备名</label>

                            <input class="am-form-field" placeholder="请输入设备名" type="text" name="name" value="<?php if(isset($data->name))echo $data->name; ?>" required>
<div>

<label for="user-phone" class="">状态</label>
<label>
    <input <?php if(isset($data->status)&&$data->status==1) echo 'checked="checked"'; ?>  type="radio"  name="status" value="1" > 开启
</label>

<label>
    <input <?php if(isset($data->status)&&$data->status==2) echo 'checked="checked"'; ?> type="radio"  name="status" value="2" > 关闭
</label>

</div>

                            <label for="user-phone" class="">设备密钥</label>

                            <input class="am-form-field" placeholder="请输入设备密钥" type="text" name="device_pwd" value="<?php if(isset($data->device_pwd))echo $data->device_pwd; ?>" required>
                            <label for="user-phone" class="">设备号</label>
                            <input class="am-form-field" placeholder="请输入设备号" type="text" name="device_no" value="<?php if(isset($data->device_no))echo $data->device_no; ?>" required>

                        <div class="hr-line-dashed"></div>
                        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">


                            <input type='hidden' name='id' value="<?php if(isset($data->id)){echo $data->id;} ?>">
                        <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">提交
                        </button>
                        </div>
                        {{csrf_field()}}

                    </form>
                </div>
<script type="text/javascript">
var m_id="<?php if(isset($data->m_id))echo $data->m_id; ?>"

$(function(){

    $('#store_id').change(function(){
        store_id=$(this).val();
        if(!store_id)
        {
            return;
        }
        $.post("{{route('paipaistoremerchant')}}",
        {
            _token: '{{csrf_token()}}',
            store_id: store_id
        },
        function (data) {
            if(data.status!=1)
            {
                alert(data.message);
            }

            // console.log(data.data);

            var str='';
            for(var i=0 ; i<data.data.length;i++)
            {
                if(m_id&&(m_id==data.data[i].id))
                {
                    str+='<option selected="selected" value="'+data.data[i].id+'">'+data.data[i].name+'</option>'
                }
                else
                {
                    str+='<option value="'+data.data[i].id+'">'+data.data[i].name+'</option>'
                }
            }



            $('#m_id').append(str);




        }, "json")




    });


    $('#store_id').trigger('change');




















            $("#form0").ajaxForm({

                // target: '#preview', 
                beforeSubmit:function(){

                    var index = layer.load(1, {
                      shade: [0.1,'#6699cc'] //0.1透明度的白色背景
                    });

                }, 
                success:function(data){

                    layer.closeAll('loading');
                    if(data.status=='1')
                    {

                        layer.alert(data.message, {
                          skin: 'layui-layer-molv' //样式类名
                          ,closeBtn: 0
                        },function(){ 
                            if(data.url)
                            {
                                location.href=data.url;
                            }
                            else
                            {

                                 location.reload();
                            }
                        });

/*
                       */

                    }
                    else
                    {

                        layer.alert(data.message, {
                          skin: 'layer-ext-lan' //样式类名
                          ,closeBtn: 0
                        });

                    }

                    return; 
                }, 
                error:function(){

            } });
































})





</script>

@endsection