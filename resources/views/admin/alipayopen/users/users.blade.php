@extends('layouts.public')
@section('css')
    <link rel="stylesheet" href="{{asset('css/plugins/jsTree/style.min.css')}}">
@endsection
@section('content')
    <script type="text/javascript" src="{{asset('js/plugins/jsTree/jstree.min.js')}}"></script>
    <div class="col-sm-12">
        @permission("addUser")
        <a  href="{{url('/register')}}">
            <button type="button" class="btn btn-w-m btn-success">添加@if(Auth::user()->level==1)代理商@else业务员@endif</button>
        </a>
        @endpermission
        @permission("changeShopOwner")
        <a href="{{route("changeShopOwner")}}">
            <button type="button" class="btn btn-primary  btn-w-m">员工店铺转移</button>
        </a>
        @endpermission
        @permission('role')
            <a class="J_menuItem" href="{{url('admin/alipayopen/role')}}"><button type="button" class="btn btn-w-m btn-success">角色管理</button></a>
        @endpermission
        @permission('permission')
            {{--<a class="J_menuItem" href="{{url('admin/alipayopen/permission')}}"><button type="button" class="btn btn-primary  btn-w-m">权限管理</button></a>--}}
        @endpermission
        @permission('setrate')
        <a class="J_menuItem" href="{{url('admin/alipayopen/setrate')}}"><button type="button" class="btn btn-primary  btn-w-m">设置费率</button></a>
        @endpermission
        @permission('dropUser')
        <a class="J_menuItem" href="{{url('admin/alipayopen/deluserlist')}}"><button type="button" class="btn btn-danger  btn-w-m">离职员工管理</button></a>
        @endpermission
        {{--遮罩层--}}
        <div id="mask" class="mask"></div>
        <!--代理商管理-->
        <div style="margin-left: 70px;margin-top: 50px;" id="usersInfo">
            <div class="tree-map">
                <!--树形图-->
                <div id="using_json"></div>
            </div>
        </div>
        <div id="getUser_box" class="col-sm-12 connect_box" style="display: none">
            <div class="ibox float-e-margins pop-up">
                <div class="ibox-title">
                    <h5>代理商信息</h5>
                    <h5 style="float: right;" class="close" onclick="hide_getuser()">关闭</h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal m-t" id="signupForm" action="" method="post">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">代理商ID：</label>
                            <div class="col-sm-3">
                                <input id="id" readonly name="id" value="" class="form-control" type="text" aria-required="true"
                                       aria-invalid="true" class="error">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">代理商姓名：</label>
                            <div class="col-sm-3">
                                <input id="getname" name="name" value="" class="form-control" type="text" aria-required="true"
                                       aria-invalid="true" class="error">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">联系电话：</label>
                            <div class="col-sm-3">
                                <input id="getphone" name="phone" value="" class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">登录邮箱：</label>
                            <div class="col-sm-3">
                                <input id="getemail" name="" readonly  class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">级别：</label>
                            <div class="col-sm-3">
                                <input id="getlevel" name="level" readonly value="" class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group" id="up_level" >
                            <label class="col-sm-3 control-label">上级：</label>
                            <div class="col-sm-3">
                                <input id="level_upone" name="level_upone" class="form-control"  readonly value="sfafasf" class="form-control" type="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">结算费率：</label>
                            <div class="col-sm-3">
                                <input id="getRate" name="rate" class="form-control" type="" value="">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-6 col-sm-offset-3">
                                <input type="hidden" id="peopleid" name="peopleid">
                                <button class="btn btn-primary" type="button" id="amend_sure" onclick="edituser()">修改信息
                                </button>
                                <button class="btn btn-primary" type="button" id="amendUser_p" onclick="show_eq()">修改密码</button>
                                <button class="btn btn-danger" type="button" id="amendUser_h" onclick="deleteu()">离职</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="eq_box" class="col-sm-12 connect_box" style="display: none">
            <div class="ibox float-e-margins pop-up">
                <div class="ibox-title">
                    <h5>修改密码</h5>
                    <h5 style="float: right;" class="close" onclick="hide_eq()">关闭</h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal m-t" id="signupForm" action="" method="post">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">新密码：</label>
                            <div class="col-sm-3">
                                <input id="amend_password" name="amend_password" class="form-control" type="password" value=""
                                       placeholder="请输入新密码">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">确认密码：</label>
                            <div class="col-sm-3">
                                <input id="confirm_apassword" name="confirm_apassword" value='' class="form-control"
                                       type="password" placeholder="请再次输入密码">
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 请再次输入您的密码</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-6 col-sm-offset-3">
                                <button class="btn btn-primary" type="button" id="amendS_password">确定修改</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection
@section('js')
    <script>
        ulevel="{{Auth::user()->level}}";
        $.ajax({
            type: "get",
            url: "{{route('ajaxusers')}}",
            async: false,
            data: {
                //查询所需要的参数,
//                token: token,
            },
            dataType: "json",
            success: function (res) {
                jsonData = res;
//                      console.log(res.name)
                var level1Arr = [];
                var level2Arr = [];
                var level3Arr = [];
                //jq的数组对象遍历
                //然后开始去拼插件所需要的数据
                var needData = [];
                if(ulevel==1){
                    $.each(jsonData, function (index, value) {
                        if (value.level == 1) {
                            level1Arr.push(value)
                        } else if (value.level == 2) {
                            level2Arr.push(value)
                        } else {
                            level3Arr.push(value)
                        }
                    });

                    $.each(level1Arr, function (i, v) {
                        var obj = {};
                        obj.id = v.id;
                        obj.text = v.name+"(业务商)";
                        obj.children = [];
                        needData.push(obj);
                        $.each(level2Arr, function (i2, v2) {
                            var obj2 = {};
                            obj2.id = v2.id;
                            obj2.text = v2.name+"(代理商)";
                            obj2.children = [];
                            if (v.id == v2.pid) {
                                obj.children.push(obj2)
                            }
                            $.each(level3Arr, function (i3, v3) {
                                var obj3 = {};
                                obj3.id = v3.id;
                                obj3.text = v3.name+"(业务员)" ;
                                if (v2.id == v3.pid) {
                                    obj2.children.push(obj3)
                                }

                            });

                        });
                    });
                }else if(ulevel==2){
                    $.each(jsonData, function (index, value) {
                        if (value.level == 2) {
                            level1Arr.push(value)
                        } else  {
                            level2Arr.push(value)
                        }
                    });
                    $.each(level1Arr, function (i, v) {
                        var obj = {};
                        obj.id = v.id;
                        obj.text = v.name+"(代理商)";
                        obj.children = [];
                        needData.push(obj);
                        $.each(level2Arr, function (i2, v2) {
                            var obj2 = {};
                            obj2.id = v2.id;
                            obj2.text =v2.name+"(业务员)";
                            obj2.children = [];
                            if (v.id == v2.pid) {
                                obj.children.push(obj2)
                            }
                        });
                    });
                }

                $("#using_json").jstree({
                    'core': {
                        "multiple": false,
                        'data': needData,
                        'dblclick_toggle': false          //禁用tree的双击展开
                    }
                });

            },
            error: function (err) {
                console.log(err)
            }
        });
        //获取用户信息
        $('#using_json').on("changed.jstree", function (e, data) {
            $('#using_json').jstree(true).toggle_node(data.selected);
            var people_id = data.node.original.id;
            peope_ids="{{Auth::user()->id}}";
            $('#peopleid').val(people_id);
            if(people_id==peope_ids){
                $('#amendUser_h').hide();
            }else{
                $('#amendUser_h').show();
            }
            //获取信息
            $.ajax({
                type: "post",
                url: "{{route('edituser')}}",
                async: true,
                dataType: 'json',
                data: {
                    _token: '{{csrf_token()}}',
                    id: people_id

                },
                success: function (res) {
                    console.log(res[0]);
                    //级别
                    var pid = res[0].pid;
                    var level = res[0].level;
                    if(pid!=0){
                        level_one=res[1].name;
                    }else{
                        level_one='';
                    }
                    $('#up_level').show();
                    if (level == 1) {
                        level = "业务商";
                        $('#up_level').hide();

                    } else if (level == 2) {
                        level = "代理商";
//                        level_one = "业务商";
                    } else {
                        level = "业务员";
//                        level_one = "代理商";
                    }
                    $('#id').val(res[0].id);
                    $('#getname').val(res[0].name);
                    $('#getphone').val(res[0].phone);
                    $('#getemail').val(res[0].email);
                    $('#getlevel').val(level);
                    $('#level_upone').val(level_one);
                    $('#getRate').val(res[0].rate);

                    //修改页面
                    $('#amend_name').val(res[0].name);
                    $('#amend_phone').val(res[0].phone);
                    $('#amend_email').val(res[0].email);
                    $('#get_level').text(level);
                    $('#amendL_rate').val(res[0].rate);

                },
                error: function (err) {
                    console.log(err)
                }
            });

            //修改;密码
            $('#amendS_password').click(function () {
                people_id=$('#peopleid').val();
                $.ajax({
                    type: "post",
                    url: "{{route('ajaxpasswd')}}",
                    async: true,
                    dataType: 'json',
                    data: {
                        _token: '{{csrf_token()}}',
                        id: people_id,
                        password:$('#amend_password').val(),
                        confirm_password:$('#confirm_apassword').val()

                    },
                    success: function (res) {
                        if(res.code==1){
                            layer.alert(res.msg);
                            window.location.reload()
                        }else{
                            layer.alert(res.msg);
                        }
                    },
                    error: function (err) {
                        console.log(err)
                    }
                });
            });

        });
        function edituser() {
            var people_id=$('#peopleid').val();
            $.ajax({
                type: "post",
                url: "{{route('doedituser')}}",
                async: true,
                dataType: 'json',
                data: {
                    _token: "{{csrf_token()}}",
                    id: people_id,
                    'name': $('#getname').val(),
                    'rate':  $('#getRate').val(),
                    'phone': $('#getphone').val(),
//                        'email': $('#amend_email').val()
                },
                success: function (res) {
                    if(res.code==1){
                        window.location.reload()
                    }else{
                        layer.alert(res.msg);
                    }
                },
                error: function (err) {
                    console.log(err)
                }
            });
//                window.location.reload()
        }
        function editpasswd() {
            var people_id=$('#peopleid').val();
            $.ajax({
                type: "post",
                url: "{{route('ajaxpasswd')}}",
                async: true,
                dataType: 'json',
                data: {
                    _token: '{{csrf_token()}}',
                    id: people_id,
                    password:$('#amend_password').val(),
                    confirm_password:$('#confirm_apassword').val()

                },
                success: function (res) {
                    if(res.code==1){
                        layer.alert(res.msg);
                        window.location.reload()
                    }else{
                        layer.alert(res.msg);
                    }
                },
                error: function (err) {
                    console.log(err)
                }
            });
        }
    </script>
    <script>
        function ShowDiv(show_div,bg_div){
            document.getElementById(show_div).style.display='block';
            document.getElementById(bg_div).style.display='block' ;
            var bgdiv = document.getElementById(bg_div);
            bgdiv.style.width = document.body.scrollWidth;
            $("#"+bg_div).height($(document).height());
            $("#usersInfo").hide();

        }
        //关闭弹出层
        function CloseDiv(show_div,bg_div){
            document.getElementById(show_div).style.display='none';
            document.getElementById(bg_div).style.display='none';
        }

        //    function show_adduser() {
        //        $("#mask").css("height", $(document).height());
        //        $("#mask").css("width", $(document).width());
        //        $("#mask").show();
        //        $("#usersInfo").hide();
        //        $("#addUser_box").show();
        //    }
        //隐藏遮罩层
        function hide_adduser() {
            $("#mask").hide();
            $("#usersInfo").show();
            $("#addUser_box").hide()
        }

        {{--获取用户信息 修改用户--}}
        //    编辑 修改用户
        function hide_getuser() {
            $("#mask").hide();
            $("#usersInfo").show();
            $("#getUser_box").hide()
        }

        $('#using_json').on("changed.jstree", function (e, data) {
            $('#using_json').jstree(true).toggle_node(data.selected);
            $("#mask").css("height", $(document).height());
            $("#mask").css("width", $(document).width());
            $("#mask").hide();
            $("#usersInfo").hide();
            $("#getUser_box").show();
        });
        {{--修改用户信息                        --}}
        function show_amendUser() {
            $("#getUser_box").hide();
            $("#amendUser_box").show();
        }
        function hide_amendUser() {
            $("#mask").hide();
            $("#usersInfo").show();
            $("#getUser_box").hide();
            $("#amendUser_box").hide()

        }

        {{--修改用户密码--}}
        function show_eq() {
            $("#getUser_box").hide();
            $("#eq_box").show();
        }
        function hide_eq() {
            $("#mask").hide();
            $("#usersInfo").show();
            $("#getUser_box").hide();
            $("#eq_box").hide()
        }


    </script>
    <script>
        function updateu(id) {
            window.location.href = "/admin/alipayopen/updateu?id=" + id;
        }

        function deleteu() {
            id=$('#peopleid').val();
            layer.confirm('数据价值很重要！确定要删除用户信息？', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post("{{route('deleteu')}}", {id: id, _token: "{{csrf_token()}}"}, function (result) {
                    if (result.success==1) {
                        window.location.href = "{{route('users')}}";
                    }else{
                        layer.msg(result.msg);
                    }
                },"json");
            }, function () {

            });
        }
    </script></div></div>
@endsection