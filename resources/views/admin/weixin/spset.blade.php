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
                <h5>服务商配置</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form role="form" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>app_id</label>
                                <input placeholder="请输入您app_id" value="{{$c->app_id}}" class="form-control" id="app_id" name="app_id"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>secret</label>
                                <input placeholder="请输入secret" value="{{$c->secret}}" class="form-control" id="secret"
                                       name="secret"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>merchant_id</label>
                                <input placeholder="请输入您merchant_id" value="{{$c->merchant_id}}" class="form-control" id="merchant_id" name="merchant_id"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>notify_url</label>
                                <input placeholder="请输入您notify_url" value="{{$c->notify_url}}" class="form-control" id="notify_url"
                                       name="notify_url"
                                       type="text">
                            </div>
                            <div class="form-group">
                                <label>key</label>
                                <input placeholder="请输入key" class="form-control"  value="{{$c->key}}" id="key" type="text" name="key">
                            </div>
                            <div class="form-group">
                                <label>证书文件apiclient_cert.pem</label>
                                <input type="text" size="50" name="cert_path" value="{{$c->cert_path}}" id="cert_path">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload" type="file" name="file" data-url="{{route('uploadfile')}}"
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
                                <label>密钥文件apiclient_key.pem</label>
                                <input type="text" size="50" name="key_path" value="{{$c->key_path}}" id="key_path">
                                <!-- 图片上传按钮 -->
                                <input id="fileupload1" type="file" name="file" data-url="{{route('uploadfile')}}"
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
@endsection
@section('js')
    <script>

        function addpost() {
            $.post("{{route('spsetPost')}}",
                    {
                        _token: '{{csrf_token()}}',
                        app_id: $("#app_id").val(),
                        merchant_id: $("#merchant_id").val(),
                        key: $("#key").val(),
                        cert_path: $("#cert_path").val(),
                        secret:$("#secret").val()
                        ,
                        key_path: $("#key_path").val(),
                        notify_url: $("#notify_url").val()
                    },
                    function (result) {
                        if (result.status == 1) {
                            layer.alert('保存成功', {icon: 6});
                        }
                    }, "json")
        }
    </script>
    <script type="text/javascript">
        publicfileupload("#fileupload", ".files", "#cert_path", ".up_progress .progress-bar", ".up_progress");
        publicfileupload("#fileupload1", ".files1", "#key_path", '.up_progress1 .progress-bar1', ".up_progress1");
        function publicfileupload(fileid, imgid, postimgid, class1, class2) {
            //图片上传
            $(fileid).fileupload({
                dataType: 'json',
                add: function (e, data) {
                    var numItems = $('.files .images_zone').length;
                    if (numItems >= 10) {
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
                        alert("上传失败");
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