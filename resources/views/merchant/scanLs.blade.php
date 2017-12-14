@extends('layouts.amaze')
@section('css')
    <link href="{{asset('css/bootstrap.min.css?v=3.3.6')}}" rel="stylesheet">
@endsection
@section('content')
    <!-- 内容区域 -->
    <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
        <div class="widget am-cf">
            <div class="widget-head am-cf">
                <div class="widget-title am-fl">支付宝当面付账单信息</div>
                <div class="widget-function am-fr">
                    <a href="javascript:;" class="am-icon-cog"></a>
                </div>
            </div>
            <div class="widget-head am-cf">
                <div class="widget-title am-fl">
                    <form action="{{route('scanLs')}}" method="get">
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
                    @if($list['0']==null)
                        <tr><h3>亲,您的账单信息暂时为空哦</h3></tr>
                    @else
                        <tr>
                            <th>时间</th>
                            <th>金额</th>
                            <th>方式</th>
                            <th>状态</th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach($list as $v)
                        <tr class="gradeX">
                            <td>{{$v->updated_at}}</td>
                            <td>{{$v->total_amount}}</td>
                            <td>
                                @if($v->type==101)
                                    支付宝当面付
                                @elseif($v->type==102)
                                    支付宝口碑
                                @elseif($v->type==103)
                                    支付宝机具
                                @elseif($v->type==104)
                                    当面付固定码
                                @elseif($v->type==201)
                                    微信
                                @elseif($v->type==202)
                                    微信机具
                                @elseif($v->type==203)
                                    微信固定码
                                @elseif($v->type==301)
                                    平安支付宝
                                @elseif($v->type==302)
                                    平安微信
                                @elseif($v->type==303)
                                    平安京东
                                @elseif($v->type==304)
                                    平安翼支付
                                @elseif($v->type==305)
                                    平安支付宝机具
                                @elseif($v->type==306)
                                    平安微信机具
                                @elseif($v->type==307)
                                    平安京东机具
                                @elseif($v->type==401)
                                    银联扫码固定码
                                @elseif($v->type==402)
                                    银联扫码机具
                                @elseif($v->type==501)
                                    民生支付宝
                                @elseif($v->type==502)
                                    民生微信
                                @elseif($v->type==503)
                                    民生QQ钱包
                                @elseif($v->type==601)
                                    浦发支付宝
                                @elseif($v->type==602)
                                    浦发微信
                                @elseif($v->type==603)
                                    浦发支付宝扫码枪
                                @elseif($v->type==604)
                                    浦发微信扫码枪
                                @endif
                            </td>
                            <td>
                                @if($v->pay_status==1)
                                <button type="button" class="am-btn-success">支付成功</button>
                                @else
                                <button type="button" class="am-btn-danger">支付失败</button>
                                @endif
                            </td>


                        </tr>
                    @endforeach
                    @endif
                    <!-- more data -->
                    </tbody>
                </table>

                <ul class="am-pagination">
                    {{$list->appends(["status"=>$status])->links()}}
                </ul>
            </div>
        </div>
    </div>
@endsection