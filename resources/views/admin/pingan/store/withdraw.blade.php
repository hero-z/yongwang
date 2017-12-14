@extends('layouts.publicStyle')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        {{--遮罩层--}}
        <div id="mask" class="mask"></div>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>提现流水列表</h5>
                    </div>
                    @role('admin')
                    <button type="submit" onclick="ShowDiv('sub_merchant_set','mask');Query()" class="btn btn-success">查询余额</button>
                    <button type="submit" onclick="ShowDiv('withdraw','mask');" class="btn btn-danger ">提现</button>
                    @endrole
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>流水号</th>
                                    {{--  <th>商户全称</th>--}}
                                    <th>提现金额</th>
                                    <th>提现时间</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($withdrawInfo)
                                    @foreach($withdrawInfo as $v)
                                        <tr>
                                            <td>{{$v->withdraw_no}}</td>
                                            <td><span class="pie">{{$v->tran_amount}}</span></td>
                                            <td>{{$v->created_at}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="dataTables_paginate paging_simple_numbers"
                                 id="DataTables_Table_0_paginate">
                                {{$withdrawInfo->render()}}
                            </div>
                        </div>
                    </div>
                    @else
                        没有任何记录
                    @endif
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
                    <div class="ant-modal-title">见证宝账户返佣查询</div>
                </div>
                <div class="ant-modal-body">
                    <form class="ant-form ant-form-horizontal">
                        <div class="ant-row ant-form-item">
                            <div class="ant-col-8 ant-form-item-label">
                                <label class="ant-form-item-required">账户可用余额</label>
                            </div>
                            <div class="ant-col-12 ant-form-item-control-wrapper">
                                <div class="ant-form-item-control ">
                                    <button type="button" id="total_balance" class="btn btn-success"></button>
                                </div>
                            </div>
                        </div>
                        <div class="ant-row ant-form-item">
                            <div class="ant-col-8 ant-form-item-label">
                                <label class="ant-form-item-required">账户可提现金额</label>
                            </div>
                            <div class="ant-col-12 ant-form-item-control-wrapper">
                                <div class="ant-form-item-control ">
                                    <button type="button" id="total_tran_out_amount" class="btn btn-danger"></button>
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
          <script>
              function Query(){
                  $.post("{{url('admin/pingan/witness/querywitness')}}", {_token: "{{csrf_token()}}",
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
                  $.post("{{url('admin/pingan/witness/withdraw')}}", {_token: "{{csrf_token()}}",tran_amount:$("#tran_amount").val()
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
                    window.location.reload()

                }
            </script>

@endsection