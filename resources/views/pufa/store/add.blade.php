@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>添加浦发银行通道商户</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="{{url('api/pufa/addPost')}}" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token"  value="{{csrf_token()}}">

                            类型：<input type='text' name='picType' value='1' ><br/>

                            图片：<input type='file' name='picFile' multiple="true" ><br/>
                             <input type='submit' value='提交' ><br/>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="con"></div>
@section('js')
    

@endsection
@endsection