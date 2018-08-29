
<div class="col-md-6">
    <div class="form-group avatar-picker">
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">用户名</label>
        <div class="col-sm-9">
            <input class="form-control" type="text" name="name"  value="{{$name}}" autofocus placeholder="用户名">
        </div>
    </div> 
    <div class="form-group">
        <label class="col-sm-3 control-label">邮箱</label>
        <div class="col-sm-9">
            <input class="form-control" type="text" name="email"  value="{{$email}}" autofocus  placeholder="邮箱">
        </div>
    </div>

    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">密码</label>
        <div class="col-md-5">
            <input type="password" class="form-control" name="password" id="tag" value="" autofocus placeholder="密码">
        </div>
    </div>

    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">密码确认</label>
        <div class="col-md-5">
            <input type="password" class="form-control" name="password_confirmation" id="tag" value="" autofocus placeholder="密码确认">
        </div>
    </div>
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">角色列表</label>
        @if(isset($id)&&$id==1)
            <div class="col-md-4" style="float:left;padding-left:20px;margin-top:8px;"><h2>超级管理员</h2></div>
        @else
            <div class="col-md-6">
                @foreach($rolesAll as $v)
                    <div class="col-md-4" style="float:left;padding-left:20px;margin-top:8px;">
            <span class="checkbox-custom checkbox-default">
                <i class="fa"></i>
                    <input class="form-actions"
                           @if(in_array($v['id'],$roles))
                           checked
                           @endif
                           id="inputChekbox{{$v['id']}}" type="Checkbox" value="{{$v['id']}}"
                           name="roles[]"> <label for="inputChekbox{{$v['id']}}">
                    {{$v['name']}}
                </label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
                    </div>
                @endforeach
            </div>
        @endif

    </div>



    <div class="text-center m-t-10">

        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button type="submit" class="btn btn-success btn-md">
            <i class="fa fa-plus-circle"></i>提交
        </button>
        <button type="button"  class="btn btn-success btn-md" onclick="location.href='{{url('admin/user/index')}}'">
            <i class="fa layui-icon"></i>返回上级
        </button>
    </div>
</div>
<div class="form-group">
    <div class="col-md-7 col-md-offset-3">

    </div>
</div>
