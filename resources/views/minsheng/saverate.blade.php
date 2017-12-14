@extends('layouts.publicStyle')
@section('title','商户基本信息修改')
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
                <h5>修改商户费率<span style='color:red'>支付宝、微信通道入驻成功的费率会被修改；实际费率显示以单个通道为准！！！</span></h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">

                        <div class="form-group">
                            <label>单笔费用：</label>
                            <input placeholder="流水中会看到" class="form-control" name="draw_fee" value='{{$store->draw_fee}}' id="draw_fee" type="text">
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label>单笔费率：</label>
                            <input placeholder="" class="form-control" name="trade_rate" value='{{$store->trade_rate}}' id="trade_rate" type="text">
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
@section('js')


    <script type="text/javascript">

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
                "{{route('ms_saveRate')}}",
                {
                    // 推荐人
                    _token: '{{csrf_token()}}',
                    store_id: $("#store_id").val(),
                    draw_fee: $("#draw_fee").val(),
                    trade_rate: $("#trade_rate").val()
                },
                function (result) {
                    // 成功
                    if(result.status=='1')
                    {

                        zeroModal.success({
                            content: '修改成功！',
                            contentDetail: result.message,
                            okFn: function() {
                                window.location.href = "{{route('ms_saveRate')}}?store_id="+$("#store_id").val();
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