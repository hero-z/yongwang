@extends('layouts.amaze')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('title','平安见证宝管理')
@section('content')

    <!-- 内容区域 -->

    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-fl">平安见证宝管理</div>
                    <div class="widget-function am-fr">
                        <a href="javascript:;" class="am-icon-cog"></a>
                    </div>
                </div>
                <div class="widget-head am-cf">
                    <div class="widget-title am-fl">
                        <button type="submit" onclick="ShowDiv('verifymessage','mask');" class="am-btn am-btn-primary btn">银联短信验证</button>
                        <button type="submit" onclick="ShowDiv('verifymoney','mask')" class="am-btn am-btn-success btn">金额鉴权</button>
                        <button type="submit" onclick="ShowDiv('sub_merchant_set','mask');Query()" class="am-btn am-btn-danger btn">查询余额</button>
                        <button type="submit" onclick="ShowDiv('withdraw','mask');" class="am-btn am-btn-warning btn">提现</button>
                    </div>
                </div>

                <div class="widget-body  widget-body-lg am-fr">

                    <table width="100%" class="am-table am-table-compact am-table-striped tpl-table-black " id="example-r">
                        <thead>
                        <tr>
                            <th class="am-u-sm-3">见证宝会员名</th>
                            <th class="am-u-sm-3">见证宝编号</th>
                            <th class="am-u-sm-3">提现账户</th>
                            <th class="am-u-sm-3">提现银行</th>
                        </tr>
                        </thead>
                        <tbody>
                                <tr class="gradeX">
                                    @if($witnessInfo)
                                    <td class="am-u-sm-3">{{$witnessInfo->nick_name}}</td>
                                    <td class="am-u-sm-3">{{$witnessInfo->cust_id}}</td>
                                    <td class="am-u-sm-3">{{$witnessInfo->bank_card_no}}</td>
                                    <td class="am-u-sm-3">{{$witnessInfo->bank_name}}</td>
                                    @endif
                                </tr>
                        <!-- more data -->
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="dataTables_paginate paging_simple_numbers"
                                 id="DataTables_Table_0_paginate">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




    </div>
    {{--短信鉴权--}}
    <div id="verifymessage" class="ant-modal" style="width: 500px;  transform-origin: 1054px 0px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('verifymessage','mask')">
                <span class="ant-modal-close-x"></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title">平安见证宝验证鉴权短信</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-8 ant-form-item-label">
                            <label class="ant-form-item-required">短信验证码</label>
                        </div>
                        <div class="ant-col-12 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" value="" id="verify_code" name="verify_code" class="input ant-input ant-input-lg"  placeholder="请输入短信验证码" style="width:200px">
                                <span class="span" style="color:red;font-size: 12px;display: none">请输入短信验证码</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button"
                         style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="verify_message_submit"><span>验证</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--金额鉴权--}}
    <div id="verifymoney" class="ant-modal" style="width: 500px;  transform-origin: 1054px 0px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('verifymoney','mask')">
                <span class="ant-modal-close-x"></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title">平安见证宝验证鉴权金额</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-8 ant-form-item-label">
                            <label class="ant-form-item-required">到账金额</label>
                        </div>
                        <div class="ant-col-12 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" value="" id="verify_money" name="verify_money" class="input ant-input ant-input-lg"  placeholder="请输入提现金额" style="width:150px"><span>元</span>
                                <span class="span" style="color:red;font-size: 12px;display: none">请输入到账金额</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button"
                         style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="verify_money_submit"><span>验证</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--见证宝余额查询--}}
    <div id="sub_merchant_set" class="ant-modal" style="width: 500px;  transform-origin: 1054px 0px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('sub_merchant_set','mask')">
                <span class="ant-modal-close-x"></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title">见证宝子账户余额查询</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-8 ant-form-item-label">
                            <label class="ant-form-item-required">账户可用余额</label>
                        </div>
                        <div class="ant-col-12 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" id="total_balance" class="am-btn am-btn-success"></button>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-8 ant-form-item-label">
                            <label class="ant-form-item-required">账户可提现金额</label>
                        </div>
                        <div class="ant-col-12 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" id="total_tran_out_amount" class="am-btn am-btn-danger"></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--见证宝提现--}}
    <div id="withdraw" class="ant-modal" style="width: 500px;  transform-origin: 1054px 0px 0px;display: none">
        <div class="ant-modal-content">
            <button class="ant-modal-close"  onclick="CloseDiv('withdraw','mask')">
                <span class="ant-modal-close-x"></span>
            </button>
            <div class="ant-modal-header">
                <div class="ant-modal-title">平安见证宝提现</div>
            </div>
            <div class="ant-modal-body">
                <form class="ant-form ant-form-horizontal">
                    <div class="ant-row ant-form-item">
                        <div class="ant-col-8 ant-form-item-label">
                            <label class="ant-form-item-required">申请提现金额</label>
                        </div>
                        <div class="ant-col-12 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <input type="text" value="" id="tran_amount" name="tran_amount" class="input ant-input ant-input-lg"  placeholder="请输入提现金额,最小0.01元" style="width:200px"><span>元</span>
                                <span class="span" style="color:red;font-size: 12px;display: none">请输入提现金额,最小0.01</span>
                            </div>
                        </div>
                    </div>
                    <div class="ant-row ant-form-item modal-btn form-button"
                         style="margin-top: 24px; text-align: center;">
                        <div class="ant-col-22 ant-form-item-control-wrapper">
                            <div class="ant-form-item-control ">
                                <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="tran_amount_submit"><span>确认申请提现</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
<script src="{{asset('/js/plugins/layer/layer.min.js')}}" type="text/javascript"></script>
<script>
    $("#verify_message_submit").click(function(){
        $.post("{{url('admin/pingan/witness/verifymessage')}}", {_token: "{{csrf_token()}}",
                code:$("#verify_code").val()
            },
            function (data) {
                layer.msg(data.msg,{time:3000});
            }, 'json');
    });
    $("#verify_money_submit").click(function(){
        $.post("{{url('admin/pingan/witness/verifymoney')}}", {_token: "{{csrf_token()}}",
                money:$("#verify_money").val()
            },
            function (data) {
                layer.msg(data.msg,{time:3000});
            }, 'json');
    });
    function Query(){
        $.post("{{url('merchant/witness/querywitness')}}", {_token: "{{csrf_token()}}",
            },
            function (data) {
                if (data.success) {
                    $('#total_balance').text(data.data.total_balance+'元');
                    $("#total_tran_out_amount").text(data.data.total_tran_out_amount+"元");
                } else {
                    layer.msg(data.msg);
                }
            }, 'json');
    }
    $("#tran_amount_submit").click(function(){
        $.post("{{url('merchant/witness/withdraw')}}", {_token: "{{csrf_token()}}",tran_amount:$("#tran_amount").val()
            },
            function (data) {
                if (data.success) {
                    layer.msg(data.data);
                } else {
                    layer.msg(data.msg);
                }
            }, 'json');
    });
    function ShowDiv(show_div,bg_div){
        document.getElementById(show_div).style.display='block';
        document.getElementById(bg_div).style.display='block' ;
        var bgdiv = document.getElementById(bg_div);
        bgdiv.style.width = document.body.scrollWidth;
        $("#"+bg_div).height($(document).height());
    }
    //关闭弹出层
    function CloseDiv(show_div,bg_div){
        document.getElementById(show_div).style.display='none';
        document.getElementById(bg_div).style.display='none';
        $('#'+show_div).find('input').val();
        window.location.reload()

    }
</script>
@endsection
