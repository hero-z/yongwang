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
                        @if($files)
                            <a class="fancybox" href="{{$files['licence']}}" title="营业执照">
                                <img alt="没有营业执照照片" style="max-width: 150px" src="{{$files['licence']}}">
                            </a>
                            <a class="fancybox" href="{{$files['main_image']}}" title="门头照">
                                <img alt="没有门头照" style="max-width: 150px" src="{{$files['main_image']}}">
                            </a>
                            <a class="fancybox" href="{{$files['sfz1']}}" title="身份证正面">
                                <img alt="没有身份证正面" style="max-width: 150px" src="{{$files['sfz1']}}">
                            </a>
                            <a class="fancybox" href="{{$files['sfz2']}}" title="身份证反面">
                                <img alt="没有身份证反面" style="max-width: 150px" src="{{$files['sfz2']}}">
                            </a>
                            <a class="fancybox" href="{{$files['sfz3']}}" title="手持身份证">
                                <img alt="没有没有手持身份证照" style="max-width: 150px" src="{{$files['sfz3']}}">
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>补交资料</h5>
                    </div>
                    <div class="ibox-content">

                        <a class="fancybox" href="" title="营业执照">
                            <img id="img"
                                 src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(500)->generate($code_url)) !!} ">
                        </a>
                        {{$code_url}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
@endsection