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
        <input type="hidden" id="token" value="{{csrf_token()}}">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>民生银行通道配置信息</h5>
@include('minsheng.common.comlabel')
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            {{csrf_field()}}

                            <div class="form-group">
                                <label>T0标识：</label>
                                <input value="{{$data->cooperator_t0}}" id="cooperator_t0" placeholder=""
                                       class="form-control" name="cooperator_t0" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>T1标识：</label>
                                <input value="{{$data->cooperator_t1}}" id="cooperator_t1" placeholder=""
                                       class="form-control" name="cooperator_t1" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>民生网关：</label>
                                <input value="{{$data->request_url}}" id="request_url" placeholder=""
                                       class="form-control" name="request_url" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>所有商户单笔费用统一设置：</label>
                                <input value="{{$data->draw_fee}}" id="draw_fee" placeholder="费率不得低于和民生银行签订的费率，否则商户进件可能失败！"
                                       class="form-control" name="draw_fee" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>所有商户费率用统一设置：</label>
                                <input value="{{$data->trade_rate}}" id="trade_rate" placeholder="费率不得低于和民生银行签订的费率，否则商户进件可能失败！"
                                       class="form-control" name="trade_rate" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>微信appid</label>
                                <input value="{{$data->wx_app_id}}" id="wx_app_id" placeholder=""
                                       class="form-control" name="wx_app_id" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>微信秘钥：</label>
                                <input value="{{$data->wx_secret}}" id="wx_secret" placeholder=""
                                       class="form-control" name="wx_secret" type="text">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>服务商公钥：</label>
                                <textarea id="self_public_key" style="min-height: 300px" placeholder=""
                                          class="form-control" name="self_public_key">{{$data->self_public_key}}</textarea>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>服务商私钥：</label>
                                <textarea id="self_private_key" style="min-height: 300px" placeholder=""
                                          class="form-control" name="self_private_key">{{$data->self_private_key}}</textarea>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>民生银行公钥：</label>
                                <textarea id="third_public_key" style="min-height: 300px" placeholder=""
                                          class="form-control" name="third_public_key">{{$data->third_public_key}}</textarea>
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
            $.post("{{route('ms_config')}}",
                    {
                        _token: '{{csrf_token()}}',
                        self_public_key:$("#self_public_key").val(),
                        cooperator_t0:$("#cooperator_t0").val(),
                        wx_app_id:$("#wx_app_id").val(),
                        wx_secret:$("#wx_secret").val(),
                        cooperator_t1:$("#cooperator_t1").val(),
                        self_private_key:$("#self_private_key").val(),third_public_key:$("#third_public_key").val(),request_url: $("#request_url").val(),draw_fee: $("#draw_fee").val(),trade_rate: $("#trade_rate").val()
                    },
                    function (result) {
                        if (result.status == 1) {
                            layer.alert(result.message, {icon: 6});
                        }
                            layer.alert(result.message, {icon: 6});
                    }, "json")
        }
    </script>
@endsection
@endsection