
    <div class="col-md-6">
        <div class="form-group avatar-picker">
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">昵称</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="name"  value="{{$name}}" placeholder="昵称">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">头像</label>
            <div class="col-sm-9">
                <div class="layui-form-item">
                    <div class="layui-upload">
                        {{--<button type="button" name="img_upload" class="layui-btn btn_upload_img">--}}
                        {{--<i class="layui-icon">&#xe67c;</i>上传图片--}}
                        {{--</button>--}}
                        <input type="hidden" name="avatar" id="avatar"  value="{{$avatar}}">
                        <input type="hidden" name="_token" class="tag_token" value="{{csrf_token()}}">
                        <img class="btn_upload_img layui-upload-img img-upload-view" src="{{$avatar or ''}}">
                        <p id="demoText"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">真实姓名</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="truename"  value="{{$truename}}" placeholder="真实姓名">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">手机号</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="mobile"  value="{{$mobile}}" placeholder="手机号">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">E-mail</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="email"  value="{{$email}}" placeholder="邮箱">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">QQ</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="qq"  value="{{$qq}}" placeholder="QQ">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">性别</label>
            <div class="col-sm-9">
                <label for="radio_male">男</label>
                <input type="radio" name="sex" value="1" @if($sex == 1) checked @endif>
                <label for="radio_female">女</label>
                <input type="radio" name="sex" value="2" @if($sex == 2) checked @endif>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">出生日期</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="birthday"  value="{{$birthday}}" id="birthday" placeholder="出生日期">

            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">紧急联系人</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="contacts_name"  value="{{$contacts_name}}" placeholder="紧急联系人姓名">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">紧急联系人电话</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="contacts_mobile"  value="{{$contacts_mobile}}" placeholder="紧急联系人电话">
            </div>
        </div>


        <div class="text-center m-t-10">
            <button type="submit" class="btn layui-btn">
                <i class="fa fa-plus-circle"></i>提交
            </button>
            <button type="button"  class="btn layui-btn" onclick="location.href='{{url('admin/fans/index')}}'">
                <i class="fa fa-mail-reply-all"></i>返回上级
            </button>

        </div>
    </div>
    <div class="form-group">
        <div class="col-md-7 col-md-offset-3">

        </div>
    </div>

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
    <script>
        lay('#version').html('-v'+ laydate.v);
        //日期插件
        laydate.render({
            elem: '#birthday' //指定元素
            ,theme: '#393D49'//指定样式
            ,range: false //日期区间设置
            ,format: 'yyyy-MM-dd' //显示格式
            // ,range: '~'//两日期分隔符
        });
        //上传插件
        layui.use('upload', function(){
            var upload = layui.upload;
            var tag_token = $(".tag_token").val();
            //普通图片上传
            var uploadInst = upload.render({
                elem: '.btn_upload_img'
                ,type : 'images'
                ,exts: 'jpg|png|gif|jpeg' //设置一些后缀，用于演示前端验证和后端的验证
                //,auto:false //选择图片后是否直接上传
                //,accept:'images' //上传文件类型
                ,url: '{{url("admin/fans/upload")}}'
                ,data:{'_token':tag_token}
                ,before: function(obj){
                    //预读本地文件示例，不支持ie8
                    obj.preview(function(index, file, result){
                        //$("#demoText").html(' <img class="layui-upload-img img-upload-view" src="'+result+'">');
                         $('.img-upload-view').attr('src', result); //图片链接（base64）
                    });
                }
                ,done: function(res){
                    //如果上传失败
                    if(res.status == 1){
                        $("#avatar").val(res.message);
                        return layer.msg('上传成功');
                    }else{//上传成功
                        layer.msg(res.message);
                    }
                }
                ,error: function(){
                    //演示失败状态，并实现重传
                    return layer.msg('上传失败,请重新上传');
                }
            });
        });
    </script>




