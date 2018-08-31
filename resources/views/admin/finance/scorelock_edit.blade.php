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
                            <h3 class="panel-title">添加红人圈</h3>
                        </div>
                        <div class="panel-body">

                            @include('admin.partials.errors')
                            @include('admin.partials.success')
                            @if(Gate::forUser(auth('admin')->user())->check('admin.finance.ScorelockUpdate') && $data->status == 0 )
                            <form class="form-horizontal"   method="POST" action="{{url('admin/finance/ScorelockUpdate')}}">
                            @endif
                            @if(Gate::forUser(auth('admin')->user())->check('admin.finance.ScorelockUp') && $data->status == 1 )
                            <form class="form-horizontal"   method="POST" action="{{url('admin/finance/ScorelockUp')}}">
                            @endif
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="scorelock_id" value="{{ $data->id }}">
                                <input type="hidden" name="user_id" value="{{ $data->user_id }}">
                                <input type="hidden" name="create_time" value="{{ $data->create_time }}">
                                <div class="col-md-6">
                                    <div class="form-group avatar-picker">
                                    </div>
                                    <!--用户名-->
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">用户名</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" style="margin-left:-3%;" type="text" name="username" id="username"  value="{{$data->username}}" placeholder="用户名" disabled>
                                        </div>
                                    </div>
                                    <!--用户名-->
                                    <!--手机号-->
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">手机号</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" style="margin-left:-3%;" type="text" name="mobile" id="mobile"  value="{{$data->mobile}}" placeholder="手机号" disabled>
                                        </div>
                                    </div>
                                    <!--手机号-->

                                    <!--头像-->
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">头像</label>
                                        <div class="col-sm-9">
                                            <div class="layui-form-item">
                                                <div class="layui-upload">
                                                    {{--<button type="button" name="img_upload" class="layui-btn btn_upload_img">--}}
                                                    {{--<i class="layui-icon">&#xe67c;</i>上传图片--}}
                                                    {{--</button>--}}
                                                    <input type="hidden" name="avatar" id="avatar"  value="{{$data->avatar}}">
                                                    <input type="hidden" name="id" id="id"  value="{{$data->id}}">
                                                    <input type="hidden" name="_token" class="tag_token" value="{{csrf_token()}}">
                                                    <img class="btn_upload_img layui-upload-img img-upload-view" src="{{$data->avatar or ''}}">
                                                    <p id="demoText"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--头像-->

                                    <!--所需红人圈-->
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">状态</label>
                                        <div class="col-sm-4">
                                                <select class="form-control"  style="margin-left:-6%;" id="status" name="status"  tabindex="-1" aria-hidden="true">
                                                    @if(Gate::forUser(auth('admin')->user())->check('admin.finance.ScorelockEdit') && $data->status == 0 )
                                                        <option value="1"  @if(isset($data->status) && $data->status == 1) selected="selected" @endif >初审通过</option>
                                                        <option value="-1"  @if(isset($data->status) && $data->status == -1) selected="selected" @endif >初审未通过</option>
                                                    @endif
                                                    @if(Gate::forUser(auth('admin')->user())->check('admin.finance.ScorelockEdit') && $data->status == 1 )
                                                        <option value="2"  @if(isset($data->status) && $data->status == 2) selected="selected" @endif >终审通过</option>
                                                        <option value="-2"  @if(isset($data->status) && $data->status == -2) selected="selected" @endif >终审未通过</option>
                                                    @endif
                                                </select>
                                        </div>
                                    </div>
                                    <!--所需红人圈-->

                                    <!--所需红人圈-->
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">所需红人圈</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">￥</span>
                                            <input style="width: 25%; text-align: right;" id="score" name="total_score" value="{{$data->total_score}}" class="form-control" placeholder="红人圈" type="text" @if($data->total_score) readonly = "readonly" @endif>
                                        </div>
                                    </div>
                                    <!--所需红人圈-->

                                    <!--属性管理-->
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">   分配红人圈  </label>
                                        <input  type="button" class="layui-btn  layui-btn-normal"  name="button" id="attr_create" value="新增属性" @if($data->total_score) Disabled = "Disabled" @endif>

                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"></label>
                                        <div class="col-sm-9">
                                            <div id="attr">
                                                <ul>
                                                    @if(!empty($attr))
                                                        @foreach($attr as $k=>$v)
                                                            <li style="margin:5px;">
                                                                红人圈：<input name="attr[keys][]" type="text" class="form-control"   size="24"   value="{{$v->score}}" placeholder="红人圈"   style="width:150px;height:30px;display:inline;" @if($data->total_score) readonly = "readonly" @endif />&nbsp;&nbsp;&nbsp;&nbsp;
                                                                冻结天数：<input name="attr[days][]" type="text" class="form-control"   size="24"  value="{{$v->days}}"  placeholder="冻结天数"   style="width:150px;height:30px;display:inline;" @if($data->total_score) readonly = "readonly" @endif />&nbsp;&nbsp;&nbsp;&nbsp;
                                                                <input type="hidden" name="attr[create_time][]" value="{{ $v->create_time }}">
                                                                <input type="button" class="layui-btn" value="删除"   onclick="deleteAttr(this)"   style="cursor:pointer" @if($data->total_score) Disabled = "Disabled" @endif>
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <!--属性管理-->
                                    <!--红人圈描述-->
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">备注描述</label>
                                        <div class="col-sm-9">
                                            <textarea  class="form-control" style="height: 20%; margin-left:-3%;text-align:left;" name="title" id="title" placeholder="备注描述" >
                                                @if($data->title)
                                                    {{$data->title}}
                                                 @endif
                                            </textarea>
                                        </div>
                                    </div>
                                    <!--红人圈描述-->
                                    <!--提交按钮-->
                                    <div class="text-center m-t-10">
                                        <button type="submit" class="btn layui-btn">
                                            <i class="fa fa-plus-circle"></i>提交
                                        </button>
                                        <button type="button"  class="btn layui-btn" onclick="location.href='{{url('admin/finance/ScorelockIndex')}}'">
                                            <i class="fa fa-mail-reply-all"></i>返回上级
                                        </button>
                                    </div>
                                    <!--提交按钮-->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
<script src="/plugins/jQuery/jQuery-2.2.0.min.js"></script>
<script src="/plugins/laydate/laydate.js"></script> <!-- lay日期插件 -->
<script type="text/javascript" src="/layui/layui.js"></script> <!-- lay上传插件 -->
<link rel="stylesheet" type="text/css" href="/layui/css/layui.css" /> <!-- lay上传插件样式 -->
<style>
    .layui-upload img{
        display: block;
        margin:0 auto;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        -webkit-border-radius: 50%;
        border: 2px solid #44576B;
    }
</style>
<script type="text/javascript">
    $(function(){
        //属性
        $("#attr_create").click(function () {
            var attr = '<p style="margin:5px;">' +
                    '红人圈：<input name="attr[keys][]" type="text" class="form-control"   size="24"    placeholder="红人圈"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                    '冻结天数：<input name="attr[days][]" type="text" class="form-control"   size="24"    placeholder="冻结天数"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                    '<input type="button" class="layui-btn" value="删除"  onclick="deleteAttr(this)"   style="cursor:pointer">' +
                    '</p>';

            $("#attr").append(attr);
        });
    });
    //删除属性
    function deleteAttr(attr) {
        $(attr).parent().remove();
    }
</script>