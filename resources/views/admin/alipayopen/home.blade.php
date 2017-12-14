@extends('layouts.public')
@section('content')
    <link rel="stylesheet" href="{{asset('/zeroModal/zeroModal.css')}}">
    <script src="{{asset('/zeroModal/zeroModal.js')}}"></script>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row row-sm text-center">
                            <div class="col-xs-6">
                                <div class="panel padder-v item bg-primary">
                                    <div class="h1 text-fff font-thin h1">{{$tstore_y}}</div>
                                    <span class="text-muted text-xs">昨日店铺</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item">
                                    <div class="font-thin h1">{{$ttotal_y}}</div>
                                    <span class="text-muted text-xs">昨日交易</span>
                                    <div class="bottom text-left">
                                        <i class="fa fa-caret-up text-warning m-l-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item">
                                    <div class="h1 text-info font-thin h1">{{$tstores}}</div>
                                    <span class="text-muted text-xs">店铺数量</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item bg-info">
                                    <div class="h1 text-fff font-thin h1">{{$ttotal_amount}}</div>
                                    <span class="text-muted text-xs">总交易额</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    @permission('appsUadate')
                    <div class="col-sm-6">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>软件更新</h5>
                            </div>
                            <div class="ibox-content">
                                <button type="button"
                                        class="btn btn-outline btn-default">{{$data->app_version}}</button>
                                <span id="update"></span>
                                <ul>
                                    <li>{{$data->msg}}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endpermission
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row row-sm text-center">
                            <div class="col-title">
                                <h3>支付宝</h3>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item bg-primary">
                                    <div class="h1 text-fff font-thin h1">{{$astore_y}}</div>
                                    <span class="text-muted text-xs">昨日店铺</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item">
                                    <div class="font-thin h1">{{$atotal_y}}</div>
                                    <span class="text-muted text-xs">昨日交易</span>
                                    <div class="bottom text-left">
                                        <i class="fa fa-caret-up text-warning m-l-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item">
                                    <div class="h1 text-info font-thin h1">{{$astores}}</div>
                                    <span class="text-muted text-xs">店铺数量</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item bg-info">
                                    <div class="h1 text-fff font-thin h1">{{$atotal_amount}}</div>
                                    <span class="text-muted text-xs">总交易额</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row row-sm text-center">
                            <div class="col-title">
                                <h3>微信</h3>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item bg-primary">
                                    <div class="h1 text-fff font-thin h1">{{$wstore_y}}</div>
                                    <span class="text-muted text-xs">昨日店铺</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item">
                                    <div class="font-thin h1">{{$wtotal_y}}</div>
                                    <span class="text-muted text-xs">昨日交易</span>
                                    <div class="bottom text-left">
                                        <i class="fa fa-caret-up text-warning m-l-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item">
                                    <div class="h1 text-info font-thin h1">{{$wstores}}</div>
                                    <span class="text-muted text-xs">店铺数量</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item bg-info">
                                    <div class="h1 text-fff font-thin h1">{{$wtotal_amount}}</div>
                                    <span class="text-muted text-xs">总交易额</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row row-sm text-center">
                            <div class="col-title">
                                <h3>平安银行</h3>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item bg-primary">
                                    <div class="h1 text-fff font-thin h1">{{$pstore_y}}</div>
                                    <span class="text-muted text-xs">昨日店铺</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item">
                                    <div class="font-thin h1">{{$ptotal_y}}</div>
                                    <span class="text-muted text-xs">昨日交易</span>
                                    <div class="bottom text-left">
                                        <i class="fa fa-caret-up text-warning m-l-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item">
                                    <div class="h1 text-info font-thin h1">{{$pstores}}</div>
                                    <span class="text-muted text-xs">店铺数量</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item bg-info">
                                    <div class="h1 text-fff font-thin h1">{{$ptotal_amount}}</div>
                                    <span class="text-muted text-xs">总交易额</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row row-sm text-center">
                            <div class="col-title">
                                <h3>浦发银行</h3>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item bg-primary">
                                    <div class="h1 text-fff font-thin h1">{{$fstore_y}}</div>
                                    <span class="text-muted text-xs">昨日店铺</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item">
                                    <div class="font-thin h1">{{$ftotal_y}}</div>
                                    <span class="text-muted text-xs">昨日交易</span>
                                    <div class="bottom text-left">
                                        <i class="fa fa-caret-up text-warning m-l-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item">
                                    <div class="h1 text-info font-thin h1">{{$fstores}}</div>
                                    <span class="text-muted text-xs">店铺数量</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel padder-v item bg-info">
                                    <div class="h1 text-fff font-thin h1">{{$ftotal_amount}}</div>
                                    <span class="text-muted text-xs">总交易额</span>
                                    <div class="top text-right w-full">
                                        <i class="fa fa-caret-down text-warning m-r-sm"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = get;
        function get() {

            $.post("{{route('updateInfo')}}", {_token: "{{csrf_token()}}"},
                function (data) {
                    if (data.status == 1) {
                        $("#update").append('<button type="button" onclick="updateFile()" class="btn btn-outline btn-success">更新系统</button>');
                    } else {
                        if (data.status != 404) {
                            layer.alert(data.msg, {icon: 5});
                        }
                    }
                }, 'json');
        }
        //更新文件
        function updateFile() {
            zeroModal.loading(3);
            $.post("{{route('appUpdateFile')}}", {_token: "{{csrf_token()}}"},
                function (data) {
                    if (data.status == 200) {
                        alert(data.msg);
                    } else {
                        alert(data.msg);
                    }
                    zeroModal.closeAll();
                    window.location.reload();
                }, 'json');
        }
    </script>
@endsection