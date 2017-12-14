@extends('layouts.amaze1')
@section('title','设置通道')
@section('content')
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                @can("changeShopOwner")
                <div class="widget-head am-cf">
                    <div class="widget-title am-fl">店铺归属转移</div>
                </div>
                @endcan
                <span style="color:red">{{session("warnning")}}</span>
                <div class="widget-body am-fr">
                    <form class="am-form tpl-form-line-form" action="{{route('changeOwner')}}" method="post">
                                <label for="user-phone" class=" am-form-label">转出方</label>
                                <select data-am-selected="{searchBox: 1,maxHeight: 100,maxWidth:100,btnWidth: '200', btnSize: 'sm', btnStyle: 'secondary'}" id="from" style="display: none;" name="from" onchange="change()">
                                    <option class="am-u-sm-2" value="" ></option>
                                    @foreach($users as $v)
                                    <option class="am-u-sm-2" value="{{$v->id}}" >{{$v->name}}</option>
                                    @endforeach
                                </select>
                                <label for="user-phone" class="am-form-label">转入方</label>
                                <select data-am-selected="{searchBox: 1,maxHeight: 100,maxWidth:100,btnWidth: '200', btnSize: 'sm', btnStyle: 'secondary'}" id="to" style="display: none;" name="to">
                                    <option class="am-u-sm-2" value="" ></option>
                                    @foreach($users as $v)
                                        <option class="am-u-sm-2" value="{{$v->id}}" >{{$v->name}}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">确认转移
                                </button>
                        {{csrf_field()}}

                        <table class="am-table am-table-bordered " id="table">

                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
        <script type="text/JavaScript">
                function change(){
                    $('#table tr').remove();
                    $.post("{{route('changeTo')}}", {id:$("#from").val(),_token: "{{csrf_token()}}"},
                            function (data) {
                               //alert(data[1].name);
                                var str="<tr><th></th> <th>店铺名称</th> <th>店铺id</th> </tr>";
                                for(var i=0;i<data.length;i++){
                                    $('#table tr').remove();
                                    str+="<tr><td><label class='am-checkbox am-success'><input value='"+data[i].external_id+"' data-am-ucheck='' class='am-ucheck-checkbox' type='checkbox' name='su[]'><span class='am-ucheck-icons'><i class='am-icon-unchecked'></i><i class='am-icon-checked'></i></span></label> </td><td>"+data[i].alias_name+"</td><td>"+data[i].external_id+"</td></tr>"
                                }
                                $("#table").append(str);
              //  $("#twoId").html(str);
                            }, 'json');
                }
    </script>

@endsection