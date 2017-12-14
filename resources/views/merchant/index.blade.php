@extends('layouts.amaze')
@section('content')
        <!-- 内容区域 -->
            <div class="container-fluid am-cf">
                <div class="row">
                    <div class="am-u-lg-3 tpl-index-settings-button">
                        <a href="{{route('AlipayTradePayCreate')}}"><button type="button" class="page-header-button">
                            <span class="am-icon-cog am-animation-spin"></span>&nbsp;扫码枪收款</button>
                        </a>
                        <a href="https://isv.umxnt.com/download/umxnt.exe">收银机插件</a>
                    </div>
                </div>

            </div>

            <div class="row-content am-cf">
                <div class="row  am-cf">
                    <div class="am-u-sm-12 am-u-md-12 am-u-lg-4">
                        <div class="widget am-cf">
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">商户总流水</div>
                                <div class="widget-function am-fr">
                                </div>
                            </div>
                            <div class="widget-body am-fr">
                                <div class="am-fl">
                                    <div class="widget-fluctuation-period-text">

                                    </div>
                                </div>
                                <div class="am-fr am-cf">
                                    <div class="widget-fluctuation-description-amount text-success" am-cf>
                                        ￥{{$total}}

                                    </div>
                                    <div class="widget-fluctuation-description-text am-text-right">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="am-u-sm-12 am-u-md-6 am-u-lg-4">
                        <div class="widget widget-primary am-cf">
                            <div class="widget-statistic-header">
                                机具扫码流水
                            </div>
                            <div class="widget-statistic-body">
                                <div class="widget-statistic-value">
                                    ￥{{$scan}}
                                </div>
                                <div class="widget-statistic-description">
                                </div>
                                <span class="widget-statistic-icon am-icon-credit-card-alt"></span>
                            </div>
                        </div>
                    </div>
                    <div class="am-u-sm-12 am-u-md-6 am-u-lg-4">
                        <div class="widget widget-purple am-cf">
                            <div class="widget-statistic-header">
                             二维码支付流水
                            </div>
                            <div class="widget-statistic-body">
                                <div class="widget-statistic-value">
                                    ￥{{$code}}
                                </div>
                                <div class="widget-statistic-description">
                                </div>
                                <span class="widget-statistic-icon am-icon-support"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row am-cf">


                    <div class="am-u-sm-12 am-u-md-4">
                        <div class="widget am-cf">
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">支付占比</div>
                                <div class="widget-function am-fr">
                                    <a href="javascript:;" class="am-icon-cog"></a>
                                </div>
                            </div>
                            <div class="widget-body widget-body-md am-fr">

                                <div class="am-progress-title">支付宝<span class="am-fr am-progress-title-more">{{$a}}% / 100%</span></div>
                                <div class="am-progress">
                                    <div class="am-progress-bar" style="width: {{$a}}%"></div>
                                </div>
                                <div class="am-progress-title">微信支付<span class="am-fr am-progress-title-more">{{$b}}% / 100%</span></div>
                                <div class="am-progress">
                                    <div class="am-progress-bar  am-progress-bar-warning" style="width: {{$b}}%"></div>
                                </div>
                                <div class="am-progress-title">其他<span class="am-fr am-progress-title-more">{{$c}}% / 100%</span></div>
                                <div class="am-progress">
                                    <div class="am-progress-bar  am-progress-bar-success" style="width: {{$c}}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection