@extends('layouts.public')
@section('title',"主页")
@section('content')
    <body>
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>商户资料图片</h5>

                    </div>
                    <div class="ibox-content">
                        @if($count)
                            <a class="fancybox" href="{{url($image[0])}}" title="身份证">
                                <img alt="没有身份证" style="max-width: 150px" src="{{url($image[0])}}">
                            </a>
                            <a class="fancybox" href="{{url($image[1])}}" title="门头照">
                                <img alt="没有门头照" style="max-width: 150px" src="{{url($image[1])}}">
                            </a>
                            <a class="fancybox" href="{{url($image[2])}}" title="营业执照">
                                <img alt="没有营业执照照片" style="max-width: 150px" src="{{url($image[2])}}">
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
    </body>
@endsection