@extends('layouts.publicStyle')
@section('title','绑卡操作')
@section('css')
@endsection
@section('content')
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
                <h5>商户绑卡操作</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="" method="post">
                            <div class="form-group">
                                <label>户名(必填):</label>
                                <input class="form-control" type="text" value="" required="required"
                                       name="bank_card_name" id="bank_card_name">
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label>卡号(必填):</label>
                                <input class="form-control" type="text" value="" required="required" name="bank_card_no"
                                       id="bank_card_no">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <input type="hidden" id="out_merchant_id" name="out_merchant_id"
                                   value="<?php echo $_GET['out_merchant_id']?>">
                        </form>
                    </div>
                </div>
                <a href="javascript:void(0)" onclick="addpost()">
                    <button style="width: 100%;height: 40px;font-size: 18px;" type="button" class="btn btn-primary">
                        确认提交
                    </button>
                </a>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')
    <script>
        function addpost() {
            $.post("{{route("UnionPayBindCardPost")}}",
                {
                    _token: '{{csrf_token()}}',
                    out_merchant_id: $("#out_merchant_id").val(),
                    bank_card_name: $("#bank_card_name").val(),
                    bank_card_no: $("#bank_card_no").val(),
                    type:0
                },
                function (result) {
                    if (result.status) {
                        window.location.href = "{{route('UnionPayStoreSuccess')}}";
                    } else {
                        layer.msg(result.msg);
                    }
                }, "json")
        }
    </script>
@endsection
@endsection