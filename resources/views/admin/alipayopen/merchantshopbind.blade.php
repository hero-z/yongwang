@extends('layouts.amaze1')
@section('title','设置通道')
@section('content')
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-fl">绑定店铺</div>
                </div>
                <div class="widget-body am-fr">
                    <form class="am-form tpl-form-line-form" action="{{route('mmpostdata')}}" onsubmit="return fun()" method="post">
                        <div class="am-form-group ">
                            <label for="user-phone" class="am-u-sm-6 am-form-label">
                                @if(session('error'))
                                    <span style="color:red;">{{session('error')}}</span>
                                @elseif(session('success'))
                                    <span style="color:green;">{{session('success')}}</span>
                                @endif
                            </label>

                        </div>

                        <div class="am-form-group ">
                            <label for="user-phone" class="am-u-sm-5 am-form-label">商户名:</label>
                            <label for="user-phone" class="am-form-label">{{$name}}</label>

                        </div>
                        <div class="am-form-group">
                            <label for="user-phone" class="am-u-sm-5 am-form-label">选择店铺</label>
                            <div class="am-u-sm-7">
                                <select  name="store_id" data-am-selected="{searchBox: 2,maxHeight: 200,maxWidth:100,btnWidth: '200', btnSize: 'sm', btnStyle: 'secondary'}" id="weixin" style="display: none;" onchange="change()" name="weixin">
                                    <option value=" " >--请选择--</option>
                                    @if(!empty($first))
                                        @foreach($first as $v)
                                            <option value="{{$v->store_id}}**{{$v->store_type}}" >{{$v->store_name}}</option>
                                        @endforeach
                                    @endif
                                    @if(!empty($second))
                                        @foreach($second as $v)
                                            <option value="{{$v->store_id}}**{{$v->store_type}}" >{{$v->store_name}}</option>
                                        @endforeach
                                    @endif
                                    @if(!empty($third))
                                        @foreach($third as $v)
                                            <option value="{{$v->store_id}}**{{$v->store_type}}" >{{$v->store_name}}</option>
                                        @endforeach
                                    @endif
                                    @if(!empty($four))
                                        @foreach($four as $v)
                                            <option value="{{$v->store_id}}**{{$v->store_type}}" >{{$v->store_name}}</option>
                                        @endforeach
                                    @endif
                                    @if(!empty($five))
                                        @foreach($five as $v)
                                            <option value="{{$v['store_id']}}**{{$v['store_type']}}" >{{$v['store_name']}}</option>
                                        @endforeach
                                    @endif
                                    @if(!empty($six))
                                        @foreach($six as $v)
                                            <option value="{{$v['store_id']}}**{{$v['store_type']}}" >{{$v['store_name']}}</option>
                                        @endforeach
                                    @endif
                                    @if(!empty($seven))
                                        @foreach($seven as $v)
                                            <option value="{{$v['store_id']}}**{{$v['store_type']}}" >{{$v['store_name']}}</option>
                                        @endforeach
                                    @endif
                                    @if(!empty($eight))
                                        @foreach($eight as $v)
                                            <option value="{{$v->store_id}}**{{$v->store_type}}" >{{$v->store_name}}</option>
                                        @endforeach
                                    @endif
                                    @if(!empty($nine))
                                        @foreach($nine as $v)
                                            <option value="{{$v['store_id']}}**{{$v['store_type']}}" >{{$v['store_name']}}</option>
                                        @endforeach
                                    @endif
                                    @if(!empty($ten))
                                        @foreach($ten as $v)
                                            <option value="{{$v['store_id']}}**{{$v['store_type']}}" >{{$v['store_name']}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="merchant_id" value="{{$id}}">
                        <input type="hidden" name="store_name" value="{{$name}}">
                        {{csrf_field()}}
                        <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-5">
                                <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">确认绑定
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <script type="text/javascript">

        ck=false;
        function change(){
            if($('select').val()!=''){
                ck=true;
            }else{
                ck=false;
            }
        }

        function  fun() {
            return ck;
        }
    </script>

@endsection