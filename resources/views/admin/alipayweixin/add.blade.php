@extends('layouts.amaze1')
@section('title','设置通道')
@section('content')
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-fl">多码合一</div>
                </div>
                <div class="widget-body am-fr">
                    <form class="am-form tpl-form-line-form" action="{{route('addTwo')}}" method="post">
                        <div class="am-form-group ">
                            <span style="color:red">{{session("warnning")}}</span>
                            <label for="user-phone" class="am-u-sm-5 am-form-label">支付宝通道</label>
                            <div class="am-u-sm-7" >
                                <select data-am-selected="{searchBox: 1,maxHeight: 200,maxWidth:100,btnWidth: '200', btnSize: 'sm', btnStyle: 'secondary'}" id="ali" style="display: none;" name="ali" onchange="change()">
                                    <option value=""></option>
                                    @if($oali)
                                        @foreach($oali as $v)
                                            @if($v->auth_shop_name)
                                                <option class="am-u-sm-3" value="store_id_a*{{$v->store_id}}*{{$v->auth_shop_name}}*alipay_ways*oalipay*{{$v->promoter_id}}" >{{$v->auth_shop_name}}(当面付)</option>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if($sali)
                                        @foreach($sali as $v)
                                          @if($v->main_shop_name)
                                            <option class="am-u-sm-3" value="store_id_a*{{$v->store_id}}*{{$v->main_shop_name}}*alipay_ways*salipay*{{$v->user_id}}" >{{$v->main_shop_name}}(口碑)</option>
                                           @endif
                                        @endforeach
                                    @endif
                                    @if($pingan)
                                        @foreach($pingan as $v)
                                          @if($v->alias_name)
                                            <option class="am-u-sm-3" value="store_id_a*{{$v->external_id}}*{{$v->alias_name}}*alipay_ways*palipay*{{$v->user_id}}" >{{$v->alias_name}}(平安)</option>
                                          @endif
                                        @endforeach
                                    @endif
                                    @if($pufa)
                                        @foreach($pufa as $v)
                                            @if($v->merchant_short_name)
                                                <option class="am-u-sm-3" value="store_id_a*{{$v->store_id}}*{{$v->merchant_short_name}}*alipay_ways*pfalipay*{{$v->user_id}}" >{{$v->merchant_short_name}}(浦发支付宝)</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="am-form-group">
                            <label for="user-phone" class="am-u-sm-5 am-form-label">微信支付通道</label>
                            <div class="am-u-sm-7" >
                                <select data-am-selected="{searchBox: 2,maxHeight: 200,maxWidth:100,btnWidth: '200', btnSize: 'sm', btnStyle: 'secondary'}" id="weixin" style="display: none;" name="weixin" onchange="changea()">
                                    <option value="" ></option>
                                    @if($weixin)
                                        @foreach($weixin as $v)
                                            @if($v->store_name)
                                            <option value="store_id_w*{{$v->store_id}}*{{$v->store_name}}*weixin_ways*weixin*{{$v->user_id}}" >{{$v->store_name}}(微信官方)</option>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if($pingan)
                                        @foreach($pingan as $v)
                                            @if($v->alias_name)
                                                <option value="store_id_w*{{$v->external_id}}*{{$v->alias_name}}*weixin_ways*pweixin*{{$v->user_id}}" >{{$v->alias_name}}(平安微信)</option>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if($pufa)
                                        @foreach($pufa as $v)
                                            @if($v->merchant_short_name)
                                                <option value="store_id_w*{{$v->store_id}}*{{$v->merchant_short_name}}*weixin_ways*pfweixin*{{$v->user_id}}" >{{$v->merchant_short_name}}(浦发微信)</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label for="user-phone" class="am-u-sm-5 am-form-label">京东支付通道</label>
                            <div class="am-u-sm-7" >
                                <select data-am-selected="{searchBox: 2,maxHeight: 200,maxWidth:100,btnWidth: '200', btnSize: 'sm', btnStyle: 'secondary'}" id="jd" style="display: none;" name="jd" onchange="changeb()">
                                    <option value="" ></option>
                                    @if($pingan)
                                        @foreach($pingan as $v)
                                            @if($v->alias_name)
                                                <option value="store_id_j*{{$v->external_id}}*{{$v->alias_name}}*jd_ways*pjd*{{$v->user_id}}" >{{$v->alias_name}}(平安京东)</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <label for="user-phone" class="am-u-sm-5 am-form-label">翼支付通道</label>
                            <div class="am-u-sm-7">
                                <select data-am-selected="{searchBox: 2,maxHeight: 200,maxWidth:100,btnWidth: '200', btnSize: 'sm', btnStyle: 'secondary'}" id="bestpay" style="display: none;" name="bestpay" onchange="changec()">
                                    <option value="" ></option>
                                    @if($pingan)
                                        @foreach($pingan as $v)
                                            @if($v->alias_name)
                                                <option value="store_id_b*{{$v->external_id}}*{{$v->alias_name}}*bestpay_ways*pbestpay*{{$v->user_id}}" >{{$v->alias_name}}(平安翼支付)</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        {{csrf_field()}}
                        <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-5">
                                <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">确认合成
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/JavaScript">
       function change(){
           $("#weixin").removeAttr("onchange");
           $("#jd").removeAttr("onchange");
           $("#bestpay").removeAttr("onchange");
               $('#weixin option').remove();
               $("#jd option").remove();
               $("#bestpay option").remove();
               $.post("{{route('xuanzhong')}}", {value:$("#ali").val(),_token: "{{csrf_token()}}"},
                       function (data) {
                           var str="<option value='' ></option>";
                           var stra="<option value='' ></option>";
                           var strb="<option value='' ></option>";
                           for(var i=0;i<data.length;i++){
                               if(i==2||i==4||i==8){
                                   if(data[i].value){
                                       str+="<option value='"+data[i].value+"'>"+data[i].name+"</option>"
                                   }
                               }
                               if(i==5&&data[i].value){
                                   stra+="<option value='"+data[i].value+"'>"+data[i].name+"</option>"
                               }
                               if(i==6&&data[i].value){
                                   strb+="<option value='"+data[i].value+"'>"+data[i].name+"</option>"
                               }
                           }
                           $("#weixin").append(str);
                           $("#jd").append(stra);
                           $("#bestpay").append(strb);

//                $("#twoId").html(str);
                       }, 'json');
       }
       function changea(){
           $("#ali").removeAttr("onchange");
           $("#jd").removeAttr("onchange");
           $("#bestpay").removeAttr("onchange");
           $('#ali option').remove();
           $("#jd option").remove();
           $("#bestpay option").remove();
           $.post("{{route('xuanzhong')}}", {value:$("#weixin").val(),_token: "{{csrf_token()}}"},
                   function (data) {
                       var str="<option value='' ></option>";
                       var stra="<option value='' ></option>";
                       var strb="<option value='' ></option>";
                       for(var i=0;i<data.length;i++){
                           if(i==0||i==1||i==2||i==7){
                               if(data[i].value){
                                   str+="<option value='"+data[i].value+"'>"+data[i].name+"</option>"
                               }
                           }
                           if(i==5&&data[i].value){
                               stra+="<option value='"+data[i].value+"'>"+data[i].name+"</option>"
                           }
                           if(i==6&&data[i].value){
                               strb+="<option value='"+data[i].value+"'>"+data[i].name+"</option>"
                           }
                       }
                       $("#ali").append(str);
                       $("#jd").append(stra);
                       $("#bestpay").append(strb);

//                $("#twoId").html(str);
                   }, 'json');
       }
       function changeb(){
           $("#weixin").removeAttr("onchange");
           $("#ali").removeAttr("onchange");
           $("#bestpay").removeAttr("onchange");
           $('#ali option').remove();
           $("#weixin option").remove();
           $("#bestpay option").remove();
           $.post("{{route('xuanzhong')}}", {value:$("#jd").val(),_token: "{{csrf_token()}}"},
                   function (data) {
                       var str="<option value='' ></option>";
                       var stra="<option value='' ></option>";
                       var strb="<option value='' ></option>";
                       for(var i=0;i<data.length;i++){
                           if(i==0||i==1||i==2||i==7){
                               if(data[i].value){
                                   str+="<option value='"+data[i].value+"'>"+data[i].name+"</option>"
                               }
                           }
                           if(i==2||i==4||i==8){
                               if(data[i].value){
                                   stra+="<option value='"+data[i].value+"'>"+data[i].name+"</option>"
                               }
                           }
                           if(i==6&&data[i].value){
                               strb+="<option value='"+data[i].value+"'>"+data[i].name+"</option>"
                           }
                       }
                       $("#ali").append(str);
                       $("#weixin").append(stra);
                       $("#bestpay").append(strb);

//                $("#twoId").html(str);
                   }, 'json');
       }
       function changec(){
           $("#weixin").removeAttr("onchange");
           $("#ali").removeAttr("onchange");
           $("#jd").removeAttr("onchange");
           $('#ali option').remove();
           $("#weixin option").remove();
           $("#jd option").remove();
           $.post("{{route('xuanzhong')}}", {value:$("#bestpay").val(),_token: "{{csrf_token()}}"},
                   function (data) {
                       var str="<option value='' ></option>";
                       var stra="<option value='' ></option>";
                       var strb="<option value='' ></option>";
                       for(var i=0;i<data.length;i++){
                           if(i==0||i==1||i==2||i==7){
                               if(data[i].value){
                                   str+="<option value='"+data[i].value+"'>"+data[i].name+"</option>"
                               }
                           }
                           if(i==2||i==4||i==8){
                               if(data[i].value){
                                   stra+="<option value='"+data[i].value+"'>"+data[i].name+"</option>"
                               }
                           }
                           if(i==5&&data[i].value){
                               strb+="<option value='"+data[i].value+"'>"+data[i].name+"</option>"
                           }
                       }
                       $("#ali").append(str);
                       $("#weixin").append(stra);
                       $("#jd").append(strb);

//                $("#twoId").html(str);
                   }, 'json');
       }
    </script>
@endsection