@extends('union.parent')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>银联钱包店铺信息</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">

                        <form action="" method="post" id="form0">



                            <div class="form-group">
                                <label>店铺名称：</label>
                                <input value='<?php if(isset($data->store_name))echo $data->store_name?>'  class="form-control"  type="text" name="store_name">
                            </div>
                            <div class="hr-line-dashed"></div>



                            <div class="form-group">
                                <label>第三方商户号</label>
                                <input value='<?php if(isset($data->merchant_id))echo $data->merchant_id?>'  class="form-control"  type="text" name="merchant_id">
                            </div>
                            <div class="hr-line-dashed"></div>



                            <div class="form-group">
                                <label>第三方APPID</label>
                                <input value='<?php if(isset($data->app_id))echo $data->app_id?>'  class="form-control"  type="text" name="app_id">
                            </div>
                            <div class="hr-line-dashed"></div>



                            <div class="form-group">
                                <label>第三方APPKEY</label>
                                <input value='<?php if(isset($data->app_key))echo $data->app_key?>'  class="form-control"  type="text" name="app_key">
                            </div>
                            <div class="hr-line-dashed"></div>



                            <div class="form-group">
                                <label>店铺负责人：</label>
                                <input value='<?php if(isset($data->shop_user))echo $data->shop_user?>'  class="form-control"  type="text" name="shop_user">
                            </div>
                            <div class="hr-line-dashed"></div>



                            <div class="form-group">
                                <label>店铺负责人电话：</label>
                                <input value='<?php if(isset($data->mobile))echo $data->mobile?>'  class="form-control"  type="text" name="mobile">
                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="form-group">
                                <label for="exampleInputName2">店铺状态：</label>

                                <label>
                                    <input type="radio"  value="1" name="status" <?php if(isset($data->status)&&($data->status==1))echo 'checked="checked"'; ?> >  开启
                                </label>

                                <label>
                                    <input type="radio"  value="2" name="status"  <?php if(isset($data->status)&&($data->status==2))echo 'checked="checked"'; ?>  >  关闭
                                </label>
                            </div>



                            {{csrf_field()}}
                            <input type="hidden" name="id" value="<?php if(isset($data->id))echo $data->id?>">
                            <input type="hidden" name="pid" value="<?php if(isset($data->pid))echo $data->pid?>">
                            <input type="hidden" name="user_id" value="<?php if(isset($data->user_id))echo $data->user_id?>">
                            <input type="hidden" name="store_id" value="<?php if(isset($data->store_id))echo $data->store_id?>">

                            <div>
                                <button  class="btn btn-sm btn-primary pull-right" type="submit">
                                    提交
                                </button>
                            </div>
                        </form>



                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="con"></div> 
@endsection
@section('js')
    <script>

  $(function(){


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
                            location.href=data.url;return;
                                 location.reload();
                                 
                        });


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
 
    });



    </script> 
@endsection