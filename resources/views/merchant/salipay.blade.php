@extends('layouts.amaze')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')
    <!-- 内容区域 -->
    <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
        <div class="widget am-cf">
            <div class="widget-head am-cf">
                <div class="widget-title am-fl">支付宝口碑店铺账单信息</div>
                <div class="widget-function am-fr">
                    <a href="javascript:;" class="am-icon-cog"></a>
                </div>
            </div>
            <div class="widget-head am-cf">
                <div class="widget-title am-fl">
                    <form action="{{route('alipaysLs')}}" method="get">
                        <select data-am-selected name="status">
                            <option value="1" @if($status=="1") selected @endif>支付成功</option>
                            <option value="2" @if($status=="2") selected @endif>支付失败</option>
                        </select>
                        <button type="submit" class="am-btn am-btn-secondary">筛选</button>
                    </form>
                </div>
            </div>
            <div class="widget-body  widget-body-lg am-fr">

                <table width="100%" class="am-table am-table-compact am-table-striped tpl-table-black "
                       id="example-r">
                    <thead>
                    @if($info['0']==null)
                        <tr><h3>亲,您的账单信息暂时为空哦</h3></tr>
                    @else
                    <tr>
                        <th class="am-u-sm-2">订单号</th>
                        <th class="am-u-sm-2">店铺名</th>
                        <th class="am-u-sm-2">收银员</th>
                        <th class="am-u-sm-1">金额</th>
                        <th class="am-u-sm-1">状态</th>
                        <th class="am-u-sm-1">类型</th>
                        <th class="am-u-sm-1">更新时间</th>
                        <th class="am-u-sm-2">备注</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($info as $v)
                        <tr class="gradeX">
                            <td class="am-u-sm-2">{{$v->out_trade_no}}</td>
                            <td class="am-u-sm-2">{{$v->main_shop_name}}</td>
                            <td class="am-u-sm-2">
                                @if($v->merchant_id)
                                    {{$cashier[$v->merchant_id]}}
                                @endif
                            </td>
                            <td class="am-u-sm-1">{{$v->total_amount}}</td>

                            @if($v->pay_status=="1")
                                <td class="am-u-sm-1 "><button type="button" class="am-btn-success">支付成功</button></td>
                            @endif
                            @if($v->pay_status=="2")
                                <td class="am-u-sm-1"><button type="button" class="am-btn-danger">取消支付</button></td>
                            @endif
                            @if($v->pay_status=="3")
                                <td class="am-u-sm-1"><button type="button" class="am-btn-danger">等待支付</button></td>
                            @endif
                            @if($v->pay_status=="4")
                                <td class="am-u-sm-1"><button type="button" class="am-btn-danger">订单关闭</button></td>
                            @endif
                            @if($v->pay_status=="5")
                                <td class="am-u-sm-1"><button type="button" class="am-btn-danger">已退款</button></td>
                            @endif
                            @if($v->pay_status==""||$v->pay_status==null)
                                <td class="am-u-sm-1"><button type="button" class="am-btn-danger">支付失败</button></td>
                            @endif
                            @if($v->type=="102")
                                <td class="am-u-sm-1">口碑二维码</td>
                            @endif
                            @if($v->type=="105")
                                <td class="am-u-sm-1">口碑扫码枪</td>
                            @endif
                            @if($v->type=="106")
                                <td class="am-u-sm-1">口碑固定金额</td>
                            @endif
                            <td class="am-u-sm-1">{{$v->updated_at}}</td>
                            <td class="am-u-sm-2">{{$v->remark}}</td>
                        </tr>
                    @endforeach
                    @endif
                    <!-- more data -->
                    </tbody>
                </table>

                <ul class="am-pagination">
                    @if($info)   {{ $info->appends(['status'=>$status])->links() }}@endif
                </ul>
            </div>
        </div>
    </div>
@endsection