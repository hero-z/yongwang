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
                        <h5>门店列表</h5>
                    </div>
                    <form action="{{route("pinganSearch")}}" method="post">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input placeholder="请输入商户简称" class="input-sm form-control" type="text" name="shopname"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                            </div>
                        </div>
                        {{csrf_field()}}
                    </form>
                    <a href="{{route('PingAnStoreAdd')}}"><button class="btn btn-success " type="button"><span class="bold">添加商户</span></button></a>
                    @permission("pinganRestore")
                    <a href="{{route("pinganRestore")}}"> <button type="button" class="btn btn-outline btn-default">还原商户</button></a>
                    @endpermission
                    <a class="J_menuItem" href="{{route('QrLists')}}"> <button type="button" class="btn btn-outline btn-default">我的商户码</button></a>
                    <a class="J_menuItem" href="{{route('PingAnOrderQuery')}}"> <button type="button" class="btn btn-outline btn-default">商户流水</button></a>
                    @permission('pinganconfig')
                    <a class="J_menuItem" href="{{route('pinganconfig')}}"> <button type="button" class="btn btn-outline btn-default">银行通道配置</button></a>
                    @endpermission
                    @permission('downloadBill')
                    <a class="J_menuItem" href="{{route('pingandownloadbill')}}"> <button type="button" class="btn btn-outline btn-default">对账单下载</button></a>
                    @endpermission
                    <a class="J_menuItem" href="{{route('pinganquerybill')}}"> <button type="button" class="btn btn-outline btn-default">订单查询</button></a>
                    @role('admin')
                    <a class="J_menuItem" href="{{url('admin/pingan/witnessinfo')}}"> <button type="button" class="btn btn-outline btn-danger">平安见证宝管理</button></a>
                    @endrole
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>商户id</th>
                                  {{--  <th>商户全称</th>--}}
                                    <th>商户简称</th>
                                    <th>联系人名称</th>
                                    <th>联系人手机号</th>
                                    <th>费率</th>
                                    <th>微信子商户</th>
                                    <th>归属员工</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($datapage)
                                    @foreach($datapage as $v)
                                        <tr>
                                            <td>{{$v->external_id}}</td>
                                            <td><span class="pie">{{$v->alias_name}}</span></td>
                                            <td>{{$v->contact_name}}</td>
                                            <td><span class="pie">{{$v->contact_mobile}}</span></td>
                                            <td><span class="pie">{{$v->merchant_rate}}</span></td>
                                            <td><span class="pie">{{$v->sub_mch_id}}</span></td>
                                            <td><span class="pie">{{$v->name}}</span></td>
                                            <td>
                                                @role('admin')
                                                @if(!$v->sub_mch_id)
                                                    <button  type="button" class="btn btn-outline btn-danger" onclick="ShowDiv('add_sub_merchant','mask');getBusiness('{{$v->external_id}}')">微信子商户入驻</button>
                                                @endif
                                                @if(!$v->subscribe_appid_status||!$v->sub_appid_status||!$v->jsapi_path_status)
                                                    <button  type="button" class="btn btn-outline btn-default"  onclick="ShowDiv('sub_merchant_set','mask');getsubappid('{{$v->sub_merchant_id}}')">微信子商户配置</button>
                                               @endif
                                                @endrole
                                                @permission("pinganStoreInfo")
                                                <a href="{{url('admin/pingan/editPingan?id='.$v->id)}}">
                                                    <button  type="button" class="btn btn-outline btn-success">收款信息</button>
                                                </a>
                                                @endpermission
                                                @permission("PinganRate")
                                                <a href="{{url('admin/pingan/setMerchantRate?id='.$v->id)}}">
                                                    <button type="button" class="btn btn-outline btn-info">费率调整</button>
                                                </a>
                                                @endpermission
                                                <a href="{{url('admin/pingan/MerchantFile?id='.$v->external_id)}}">
                                                    <button type="button" class="btn btn-outline btn-info">商户资料</button>
                                                </a>
                                                @permission("setStore")
                                                <a href="{{url('admin/pingan/SetStore?id='.$v->id)}}">
                                                    <button type="button" class="btn btn-outline btn-primary">收款设置
                                                    </button>
                                                </a>
                                                @endpermission
                                                @if($v->pid==0)
                                                    @permission("pinganBranch")
                                                <a href="{{url('admin/pingan/BranchIndex?pid='.$v->id)}}">
                                                    <button type="button" class="btn btn-outline btn-primary">分店管理
                                                    </button>
                                                </a>
                                                    @endpermission
                                                @endif
                                                @permission("oauthlistCashier")
                                                <a href="{{url('admin/alipayopen/CashierIndex?store_id='.$v->external_id.'&store_name='.$v->alias_name)}}">
                                                    <button type="button" class="btn btn-outline btn-primary">收银员管理
                                                    </button>
                                                </a>
                                               @endpermission
                                                @permission("payStatus")
                                                @if($v->pay_status==1)
                                                    <button id="cpay" onclick='co("{{$v->id}}",0)' type="button"
                                                            class="btn btn-outline btn-warning">关闭收款
                                                    </button>
                                                @endif
                                                @if($v->pay_status==0)
                                                    <button id="opay" onclick='co("{{$v->id}}",1)' type="button"
                                                            class="btn btn-outline btn-warning">开启收款
                                                    </button>
                                                @endif
                                                @endpermission
                                                @permission("DelPingan")
                                                <button onclick='del("{{$v->id}}")' type="button"
                                                        class="btn btn-outline btn-warning">删除
                                                </button>
                                                @endpermission
                                            </td>
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
                                {{$paginator->render()}}
                            </div>
                        </div>
                    </div>
                    @else
                        没有任何记录
                    @endif
                </div>
            </div>

        </div>
        {{--添加微信子商户--}}
        <div id="add_sub_merchant" class="ant-modal" style="width: 790px;  transform-origin: 1054px 0px 0px;display: none">
            <div class="ant-modal-content">
                <button class="ant-modal-close"  onclick="CloseDiv('add_sub_merchant','mask')">
                    <span class="ant-modal-close-x"></span>
                </button>
                <div class="ant-modal-header">
                    <div class="ant-modal-title">微信子商户入驻</div>
                </div>
                <div class="ant-modal-body">
                    <form class="ant-form ant-form-horizontal">

                        <div class="ant-row ant-form-item">
                            <div class="ant-col-6 ant-form-item-label">
                                <label class="ant-form-item-required">行业</label>
                            </div>
                            <input type="hidden" value="" id="external_id">
                            <div class="ant-col-16 ant-form-item-control-wrapper">
                                <div class="ant-form-item-control ">
                                    <div id="region">
                                        <select id="first_business" class="form-control select_c" name="first_business" >　
                                            <option id="" value="" >请选择...</option>
                                        </select>

                                        <select id="second_business" class="form-control select_c" name="second_business" >
                                            　　
                                            <option id='' value=''>请选择...</option>
                                        </select>

                                        <select id="third_business" class="form-control select_c" name="third_business" >
                                            　
                                            <option id='' value=''>请选择...</option>
                                        </select>
                                    </div>
                                    <span style="color:red;font-size:12px;display: none" id="span2" class="span">*请选择完整的行业信息</span>
                                </div>
                            </div>
                        </div>


                        <div class="ant-row ant-form-item modal-btn form-button"
                             style="margin-top: 24px; text-align: center;">
                            <div class="ant-col-22 ant-form-item-control-wrapper">
                                <div class="ant-form-item-control ">
                                    <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="sub_merchant_keep" onclick="SubMerchantKeep()"><span>提交</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{--微信子商户配置--}}
        <div id="sub_merchant_set" class="ant-modal" style="width: 850px;  transform-origin: 1054px 0px 0px;display: none">
            <div class="ant-modal-content">
                <button class="ant-modal-close"  onclick="CloseDiv('sub_merchant_set','mask')">
                    <span class="ant-modal-close-x"></span>
                </button>
                <div class="ant-modal-header">
                    <div class="ant-modal-title">微信子商户配置</div>
                </div>
                <div class="ant-modal-body">
                    <form class="ant-form ant-form-horizontal">
                            <input type="hidden" value="" id="sub_merchant_id">
                                <div class="ant-row ant-form-item">
                                    <div class="ant-col-8 ant-form-item-label">
                                        <label class="ant-form-item-required">sub_appid(支付微信公众号APPID)</label>
                                    </div>
                                    <div class="ant-col-12 ant-form-item-control-wrapper">
                                        <div class="ant-form-item-control ">
                                            <input type="text" value="" id="sub_appid" name="sub_appid" class="input ant-input ant-input-lg" readonly placeholder="" style="width:463px">
                                            <span class="span" style="color:red;font-size: 12px;display: none">请输入sub_appid</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ant-row ant-form-item">
                                    <div class="ant-col-8 ant-form-item-label">
                                        <label class="ant-form-item-required">subscribe_appid(支付完成推荐关注公众号APPID)</label>
                                    </div>
                                    <div class="ant-col-12 ant-form-item-control-wrapper">
                                        <div class="ant-form-item-control ">
                                            <input type="text" value="" id="subscribe_appid" name="subscribe_appid" class="input ant-input ant-input-lg" placeholder="请输入subscribe_appid" style="width:463px">
                                            <span class="span" style="color:red;font-size: 12px;display: none">请输入subscribe_appid</span>
                                        </div>
                                    </div>
                                </div>

                        <div class="ant-row ant-form-item modal-btn form-button"
                             style="margin-top: 24px; text-align: center;">
                            <div class="ant-col-22 ant-form-item-control-wrapper">
                                <div class="ant-form-item-control ">
                                    <button type="button" class="ant-btn ant-btn-primary ant-btn-lg" id="sub_merchant_set_keep" onclick="SubMerchantSetKeep()"><span>提交</span>
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
        function del(id) {
            //询问框
            layer.confirm('确定要删除', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{route('DelPinanStore')}}", {_token: "{{csrf_token()}}", id: id},
                        function (data) {
                            if(data.success){
                                window.location.href = "{{route('PingAnStoreIndex')}}";
                            }else{
                                layer.msg(data.erro_message)
                            }
                        }, "json");
            }, function () {

            });
        }


        function co(id, type) {
            if (type == 0) {
                //询问框
                layer.confirm('确定要关闭此商户的收款功能', {
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    $.post("{{route('PayStatus')}}", {_token: "{{csrf_token()}}", id: id, type: type},
                            function (data) {
                                window.location.href = "{{route('PingAnStoreIndex')}}";
                            }, "json");
                }, function () {

                });
            } else {
                //询问框
                layer.confirm('确定要开启此商户的收款功能', {
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    $.post("{{route('PayStatus')}}", {_token: "{{csrf_token()}}", id: id, type: type},
                            function (data) {
                                window.location.href = "{{route('PingAnStoreIndex')}}";
                            }, "json");
                }, function () {

                });
            }

        }
        function getBusiness(external_id){
            $("#external_id").val(external_id);
            $.post("{{url('admin/pingan/getbusiness')}}", {_token: "{{csrf_token()}}",
                },
                function (data) {
                    if (data.success) {
                        business=data.data;
                        for(var i=0;i<business.length;i++){
                            var option='<option  value='+ business[i].id + ' class="first_business">'+business[i].business_type+'</option>';
                            $('#first_business').append(option);
                        }
                    } else {
                        layer.msg(data.msg);
                    }
                }, 'json');
        };
        $('#first_business').change(function () {
            $('.second_business').remove();
            $('.third_business').remove();
            id=$(this).val();
            $.post("{{url('admin/pingan/getbusiness')}}", {_token: "{{csrf_token()}}",id:id},
                function (data) {
                    var second_business=[];
                    if (data.success) {
                        second_business=data.data;
                        for(var i=0;i<second_business.length;i++){
                            var option='<option  value='+ second_business[i].id + ' class="second_business">'+second_business[i].business_type+'</option>';
                            $('#second_business').append(option);
                        }
                    } else {
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');

        });
        $('#second_business').change(function () {
            $('.third_business').remove();
            id=$('#second_business').val();
            $.post("{{url('admin/pingan/getbusiness')}}", {_token: "{{csrf_token()}}",id:id},
                function (data) {
                    var third_business=[];
                    if (data.success) {
                        third_business=data.data;
                        for(var i=0;i<third_business.length;i++){
                            var option='<option  value='+ third_business[i].business_id + ' class="third_business">'+third_business[i].business_type+'</option>';
                            $('#third_business').append(option);
                        }
                    } else {
                        layer.msg(data.msg,{time:2000});
                    }
                }, 'json');

        });
        function SubMerchantKeep(){
            var obj=$("#add_sub_merchant");
            var  ck=true;
            obj.find('.select_c').each(function () {
                var select = $(this).val();
                if ( select == "" ) {
                    layer.msg('请选择完整的行业类型',{time:1000});
                    ck= false;
                }
            });
            business=$("#third_business").val();
            external_id=$('#external_id').val();
            if(ck){
                $.post("{{url('admin/pingan/createsubmerchant')}}", {_token: "{{csrf_token()}}",business:business,external_id:external_id},
                    function (data) {
                        if (data.success) {
                            layer.msg(data.data,{time:2000});
                        } else {
                            layer.msg(data.msg,{time:2000});
                        }
                    }, 'json');
            }

        }
        function getsubappid(sub_merchant_id){
            $("#sub_merchant_id").val(sub_merchant_id);
            $.post("{{url('admin/pingan/getsubappid')}}", {_token: "{{csrf_token()}}",
                },
                function (data) {
                    if (data.success) {
                       $("#sub_appid").val(data.data);
                    } else {
                        layer.msg(data.msg);
                    }
                }, 'json');
        }
        function SubMerchantSetKeep(){
                $.post("{{url('admin/pingan/submerchantset')}}", {_token: "{{csrf_token()}}",sub_merchant_id:$('#sub_merchant_id').val(),sub_appid:$("#sub_appid").val(),subscribe_appid:$("#subscribe_appid").val()},
                    function (data) {
                        if (data.success) {
                            layer.msg(data.data,{time:2000});
                        } else {
                            layer.msg(data.msg,{time:2000});
                        }
                    }, 'json');

        }
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