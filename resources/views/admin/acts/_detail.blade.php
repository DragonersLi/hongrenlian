
<div class="col-md-6">
    <div class="form-group avatar-picker">
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">选择用户</label>
        <div class="col-sm-9">
            <div class="layui-form-item">
                <select class="form-control"  id="uid" name="uid"  tabindex="-1" aria-hidden="true">
                    <option value="0">请选择</option>
                    @foreach($users as $k=>$v)
                        <option value="{{$v['id']}}"  @if(isset($uid) && $uid == $v['id']) selected="selected" @endif >{{$v['username']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">昵称展示</label>
        <div class="col-sm-9">
            <div class="layui-form-item">
                <div class="layui-upload">
                    <input type="text" class="form-control" name="user_name" id="user_name"  value="{{$user_name or ''}}" placeholder="昵称展示" >
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">风采展示</label>
        <div class="col-sm-9">
            <div class="layui-form-item">
                <div class="layui-upload">
                    <input type="hidden" name="user_img" id="user_img"  value="{{$user_img or ''}}">
                    <input type="hidden" name="_token" class="tag_token" value="{{csrf_token()}}">
                    <img class="btn_upload_img layui-upload-img img-upload-view" src="{{$user_img or ''}}">
                    <p id="demoText"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">投票宣言</label>
        <div class="col-sm-9">
            <textarea class="form-control" name="user_desc"   placeholder="投票宣言" style="height:180px;">{{$user_desc or ''}}</textarea>
        </div>
    </div>


    <div class="text-center m-t-10">
        <input class="form-control" type="hidden" name="act_id"  value="{{$act_id}}" id="act_id" placeholder="参加的活动ID">
        <button type="submit" class="btn layui-btn">
            <i class="fa fa-plus-circle"></i>提交
        </button>
        <button type="button"  class="btn layui-btn" onclick="location.href='{{url('admin/acts/detail',['act_id'=>$act_id])}}'">
            <i class="fa  fa-mail-reply-all"></i>返回上级
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
            ,url: '{{url("admin/acts/upload")}}'
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
                    $("#user_img").val(res.message);
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



