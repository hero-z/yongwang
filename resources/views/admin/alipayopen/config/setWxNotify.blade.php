@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{$store_name}}_收银提醒配置信息</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            {{csrf_field()}}
                            <input type="hidden" id="store_id" value="{{$store_id}}">
                            <input type="hidden" id="set_type" value="{{$set_type}}">
                            <div class="form-group">
                                <label>店铺名称</label>
                                <input value="{{$store_name}}"   id="store_name"  class="form-control" name="receiver"  type="text">
                            </div>
                            <div class="form-group">
                                <label>收银员设置</label>
                                <input value="{{$WxPayNotify->receiver}}" disabled   id="receiver" placeholder="请填写收银员微信号多个以 ',' 隔开" class="form-control" name="receiver"  type="hidden">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>微信通知模板ID</label>
                                <input  id="template_id"  value="{{$WxPayNotify->template_id}}" placeholder="请填写微信通知模板ID,在公众号后台模板消息可以看到模板id" class="form-control" name="template_id"  type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>设置模板头部颜色</label>
                                <input  id="topColor" value="{{$WxPayNotify->topColor}}" placeholder="请设置模板头部颜色" class="form-control" name="topColor"  type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>设置详情链接</label>
                                <input  id="linkTo" value="{{$WxPayNotify->linkTo}}" placeholder="设置详情链接" class="form-control" name="linkTo"  type="text">
                            </div>
                            <div class="hr-line-dashed"></div>
                                <div class="form-group">
                                    <label>设置模板数据</label>
                                    <textarea style="min-height: 100px"  id="data" value="" placeholder="设置模板数据" class="form-control" name="data"  type="text">{{$WxPayNotify->data}}</textarea>
                                </div>
                                <div class="hr-line-dashed"></div>
                            <div>
                                <button onclick="addpost()" class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                        type="button">
                                    <strong>保存</strong>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')
    <script>

        function addpost() {
           $.post("{{route('setWxNotifyPost')}}",
                   {_token: '{{csrf_token()}}',receiver:$("#receiver").val(),template_id:$("#template_id").val(),topColor:$("#topColor").val(),linkTo:$("#linkTo").val()
                       ,data:$("#data").val(),store_id:$("#store_id").val(),store_type:$("#set_type").val(),store_name:$("#store_name").val()},
                   function (result) {
                       if(result.status==1){
                           layer.alert('保存成功', {icon: 6});

                       }else {
                           layer.alert('保存失败', {icon: 5});

                       }
                   }, "json")
        }
    </script>
@endsection
@endsection