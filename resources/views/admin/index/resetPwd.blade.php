    {{--<img src="/imgs/qrcodes/qrcode.png" alt="" width="180px">--}}
    {{--<img src="/imgs/qrcodes/qrcode2.png" alt="" width="180px">--}}
    {{--<img src="/imgs/qrcodes/qrcode3.png" alt="" width="180px">--}}
    @extends('admin.layouts.base')

    @section('title','控制面板')

    @section('pageHeader','控制面板')

    @section('pageDesc','DashBoard')

    @section('content')
        <div class="main animsition">
            <div class="container-fluid">

                <div class="row">
                    <div class="">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">重置密码</h3>
                            </div>
                            <div class="panel-body">

                                @include('admin.partials.errors')
                                @include('admin.partials.success')

                                <form class="form-horizontal"   method="POST" action="{{url('admin/resetPwd')}}">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">


                                    <div class="col-md-6">
                                        <div class="form-group avatar-picker">
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">用户名</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" type="text" name="name"  value="{{auth('admin')->user()->name}}" placeholder="用户名" readOnly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">邮箱</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" type="text" name="email"  value="{{auth('admin')->user()->email}}" placeholder="邮箱" readOnly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">旧密码</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" type="password" name="oldPwd"  value="" placeholder="旧密码" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">新密码</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" type="password" name="newPwd"  value="" placeholder="新密码" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">确认密码</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" type="password" name="newPwd_confirmation"  value="" placeholder="确认密码" required>
                                            </div>
                                        </div>



                                        <div class="text-center m-t-10">
                                            <input class="form-control" type="hidden" name="id"  value="{{auth('admin')->user()->id}}" placeholder="ID" readOnly>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fa fa-plus-circle">提交</i>
                                            </button>
                                            <button type="button" class="btn btn-success" onclick="location.href='{{$pre_url}}'">
                                                <i class="fa  fa-mail-reply-all">返回上级</i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-7 col-md-offset-3">

                                        </div>
                                    </div>




                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @stop