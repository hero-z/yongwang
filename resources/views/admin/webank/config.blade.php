@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <script src="{{asset('/jQuery-File-Upload/js/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js//jquery.iframe-transport.js')}}" type="text/javascript"></script>
    <script src="{{asset('/jQuery-File-Upload/js/jquery.fileupload.js')}}" type="text/javascript"></script>
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
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>微众微信配置</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form role="form" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>微众微信代理商号</label>
                                <input required placeholder="请输入您code_no" value="{{$wx->code_no}}" class="form-control" id="code_no1" name="code_no1"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>app_id</label>
                                <input required placeholder="请输入您app_id" value="{{$wx->app_id}}" class="form-control" id="app_id1" name="app_id1"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>secret</label>
                                <input required placeholder="请输入secret" value="{{$wx->secret}}" class="form-control" id="secret1" name="secret1" type="text">
                            </div>
                            <div class="form-group">
                                <label>证书密码</label>
                                <input required placeholder="请输入您证书密码" value="{{$wx->client_pass}}" class="form-control" id="client_pass1" name="client_pass1"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>微信服务器app_id</label>
                                <input placeholder="请输入您微信服务器app_id" value="{{$wx->wx_app_id}}" class="form-control" id="wx_app_id1" name="wx_app_id1" type="text">
                            </div>
                            <div class="form-group">
                                <label>微信服务器secret</label>
                                <input placeholder="请输入微信服务器secret" class="form-control"  value="{{$wx->wx_secret}}" id="wx_secret1" type="text" name="wx_secret1">
                            </div>
                            <div class="form-group">
                                <label>证书文件XXXXXX.crt</label>
                                <input type="text" size="50" name="client_cert1" value="{{$wx->client_cert}}" id="client_cert1">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload" type="file" name="file" data-url="{{route('webanksendfile')}}"
                                       data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress">
                                    <div class="progress-bar"></div>
                                </div>
                                <div style="clear:both;"></div>

                            </div>
                            <div class="form-group">
                                <label>密钥文件XXXXXX.key</label>
                                <input type="text" size="50" name="client_key1" value="{{$wx->client_key}}" id="client_key1">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload1" type="file" name="file" data-url="{{route('webanksendfile')}}"
                                       data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files1"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress1">
                                    <div class="progress-bar1"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div>
                                <button onclick="addpost(1)" class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                        type="button">
                                    <strong>保存</strong>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>微众支付宝配置</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form role="form" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>微众支付宝代理商号</label>
                                <input required placeholder="请输入您code_no" value="{{$ali->code_no}}" class="form-control" id="code_no2" name="code_no2"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>app_id</label>
                                <input required placeholder="请输入您app_id" value="{{$ali->app_id}}" class="form-control" id="app_id2" name="app_id2"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>secret</label>
                                <input required placeholder="请输入secret" value="{{$ali->secret}}" class="form-control" id="secret2" name="secret2" type="text">
                            </div>
                            <div class="form-group">
                                <label>证书密码</label>
                                <input required placeholder="请输入您证书密码" value="{{$ali->client_pass}}" class="form-control" id="client_pass2" name="client_pass2"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>微信服务器app_id</label>
                                <input placeholder="请输入您微信服务器app_id" value="{{$ali->wx_app_id}}" class="form-control" id="wx_app_id2" name="wx_app_id2" type="text">
                            </div>
                            <div class="form-group">
                                <label>微信服务器secret</label>
                                <input placeholder="请输入微信服务器secret" class="form-control"  value="{{$ali->wx_secret}}" id="wx_secret2" type="text" name="wx_secret2">
                            </div>
                            <div class="form-group">
                                <label>证书文件XXXXXX.crt</label>
                                <input type="text" size="50" name="client_cert2" value="{{$ali->client_cert}}" id="client_cert2">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload2" type="file" name="file" data-url="{{route('webanksendfile')}}"
                                       data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files2"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress2">
                                    <div class="progress-bar2"></div>
                                </div>
                                <div style="clear:both;"></div>

                            </div>
                            <div class="form-group">
                                <label>密钥文件XXXXXX.key</label>
                                <input type="text" size="50" name="client_key2" value="{{$ali->client_key}}" id="client_key2">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload3" type="file" name="file" data-url="{{route('webanksendfile')}}"
                                       data-form-data='{"_token": "{{csrf_token()}}"}' multiple="true">
                                <!-- 图片展示模块 -->
                                <div class="files3"></div>
                                <div style="clear:both;"></div>
                                <!-- 图片上传进度条模块 -->
                                <div class="up_progress3">
                                    <div class="progress-bar3"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div>
                                <button onclick="addpost(2)" class="btn btn-sm btn-primary pull-right m-t-n-xs"
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
@endsection
@section('js')
    <script>
        function addpost(id) {
            $.post("{{route('webankconfigpost')}}",
                    {
                        _token: '{{csrf_token()}}',
                        id:id,

                        code_no: $("#code_no"+id).val(),
                        app_id: $("#app_id"+id).val(),
                        secret:$("#secret"+id).val(),
                        client_cert: $("#client_cert"+id).val(),
                        client_key: $("#client_key"+id).val(),
                        client_pass: $("#client_pass"+id).val(),
                        wx_app_id: $("#wx_app_id"+id).val(),
                        wx_secret: $("#wx_secret"+id).val()
                    },
                    function (result) {
                        if (result.status == 1) {
                            layer.alert('保存成功', {icon: 6});
                        }
                    }, "json")
        }
    </script>
    <script type="text/javascript">
        publicfileupload("#fileupload", ".files", "#client_cert1", ".up_progress .progress-bar", ".up_progress");
        publicfileupload("#fileupload1", ".files1", "#client_key1", '.up_progress1 .progress-bar1', ".up_progress1");
        publicfileupload("#fileupload2", ".files2", "#client_cert2", ".up_progress2 .progress-bar2", ".up_progress2");
        publicfileupload("#fileupload3", ".files3", "#client_key2", '.up_progress3 .progress-bar3', ".up_progress3");
        function publicfileupload(fileid, imgid, postimgid, class1, class2) {
            //图片上传
            $(fileid).fileupload({
                dataType: 'json',
                add: function (e, data) {
                    var numItems = $('.files .images_zone').length;
                    if (numItems >= 2) {
                        alert('提交文件过多');
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
                        alert(d.error);
                    } else {
                        jQuery(postimgid).val(d.path);
                    }
                },
                progressall: function (e, data) {
                    console.log(data);
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $(class1).css('width', progress + '%');
                }
            });
        }
    </script>
@endsection