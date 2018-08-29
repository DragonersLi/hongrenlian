
    <div class="col-md-6">
        <div class="form-group avatar-picker">
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">活动名称</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="title"  value="{{$title or ''}}" placeholder="活动名称">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">头部标题</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="header"  value="{{$header or ''}}" placeholder="头部标题">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">活动主图</label>
            <div class="col-sm-9">
                <div class="layui-form-item">
                    <div class="layui-upload">
                        {{--<button type="button" name="img_upload" class="layui-btn btn_upload_img">--}}
                            {{--<i class="layui-icon">&#xe67c;</i>上传图片--}}
                        {{--</button>--}}
                        <input type="hidden" name="index_img" id="index_img"  value="{{$index_img or ''}}">
                        <input type="hidden" name="_token" class="tag_token" value="{{csrf_token()}}">
                        <img class="btn_upload_img layui-upload-img img-upload-view"  style="display:;" src="{{$index_img or ''}}"  />
                        <p id="demoText"></p>
                    </div>
                </div>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-3 control-label">轮播图片</label>
            <div class="col-sm-9">
                <div class="layui-form-item">
                    <div class="layui-upload">
                          <?php
                        if(isset($lunbo_img)){
                            $lunbo_img = is_array($lunbo_img) ? $lunbo_img : unserialize($lunbo_img);
                        }

                          ?>
                        <input type="hidden" name="lunbo_img[]" id="lunbo_img1"  value="{{$lunbo_img[0] or ''}}">
                        <input type="hidden" name="lunbo_img[]" id="lunbo_img2"  value="{{$lunbo_img[1] or ''}}">
                        <input type="hidden" name="lunbo_img[]" id="lunbo_img3"  value="{{$lunbo_img[2] or ''}}">
                        <span style="float:left;margin:10px;"><img class="btn_upload_img1 layui-upload-img img-upload-view1"  style="display:;"  src="{{$lunbo_img[0] or ''}}" /></span>
                        <span style="float:left;margin:10px;"><img class="btn_upload_img2 layui-upload-img img-upload-view2"  style="display:;"  src="{{$lunbo_img[1] or ''}}" /></span>
                        <span style="float:left;margin:10px;"><img class="btn_upload_img3 layui-upload-img img-upload-view3"  style="display:;"  src="{{$lunbo_img[2] or ''}}" /></span>

                    </div>
                </div>
            </div>
        </div>




        <div class="form-group">
            <label class="col-sm-3 control-label">活动时间</label>
            <div class="col-sm-9">
                <input class="form-control"  type="text" name="rangedatetime" id="rangedatetime"  value="{{$rangedatetime or ''}}" placeholder="活动有效时间">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">投票用户奖励标题</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="reward_title"  value="{{$reward_title or ''}}" placeholder="活动投票用户奖励标题">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">投票用户奖励描述</label>
            <div class="col-sm-9">
                <textarea  class="form-control" name="reward_desc"  placeholder="活动投票用户奖励描述">{{$reward_desc or ''}}</textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">被投票奖励标题</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="voting_title"  value="{{$voting_title or ''}}" placeholder="活动被投票奖励标题">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">被投票奖励描述</label>
            <div class="col-sm-9">
                <textarea  class="form-control" name="voting_desc"  placeholder="活动被投票奖励描述">{{$voting_desc or ''}}</textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">奖励规则描述</label>
            <div class="col-sm-9">
                <textarea  class="form-control" name="rule_desc"  placeholder="奖励规则描述">{{$rule_desc or ''}}</textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">活动描述</label>
            <div class="col-sm-9">
                <textarea  class="form-control" name="act_desc"  placeholder="活动描述">{{$act_desc or ''}}</textarea>
            </div>
        </div>



        <div class="form-group">
            <label class="col-sm-3 control-label">设为模板</label>
            <div class="col-sm-9">
                <label for="radio_female">否</label>
                <input type="radio" name="is_template" value="0" @if(isset($is_template) && $is_template == 0) checked @endif>
                <label for="radio_male">是</label>
                <input type="radio" name="is_template" value="1" @if(isset($is_template) && $is_template == 1) checked @endif>
            </div>
        </div>

        <div class="text-center m-t-10">
            <button type="submit" class="btn layui-btn">
                <i class="fa fa-plus-circle"></i>提交
            </button>
                <button type="button"  class="btn layui-btn" onclick="location.href='{{url('admin/acts/index')}}'">
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
        //日期区间
        laydate.render({
            elem: '#rangedatetime' //指定元素
            ,theme: '#393D49'//指定样式
            ,range: true //日期区间设置
            //,format: 'yyyy-MM-dd H:i:s' //显示格式
            ,range: '~'//两日期分隔符
            ,type: 'datetime' //日期时间
        });


        //上传插件
        layui.use('upload', function(){
            var upload = layui.upload;
            var tag_token = $(".tag_token").val();

            //轮播主图上传
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
                        $('.img-upload-view').css('display', 'block'); //图片显示
                    });
                }
                ,done: function(res){
                    //如果上传失败
                    if(res.status == 1){
                        $("#index_img").val(res.message);
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
            //轮播一上传
            var uploadInst = upload.render({
                elem: '.btn_upload_img1'
                ,type : 'images'
                ,exts: 'jpg|png|gif|jpeg' //设置一些后缀，用于演示前端验证和后端的验证
                //,auto:false //选择图片后是否直接上传
                //,accept:'images' //上传文件类型
                ,url: '{{url("admin/acts/upload")}}'
                ,data:{'_token':tag_token}
                ,before: function(obj){
                    //预读本地文件示例，不支持ie8
                    obj.preview(function(index, file, result){
                        $('.img-upload-view1').attr('src', result); //图片链接（base64）
                        $('.img-upload-view1').css('display', 'block'); //图片显示
                    });
                }
                ,done: function(res){
                    //如果上传失败
                    if(res.status == 1){
                        $("#lunbo_img1").val(res.message);
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
            //轮播二上传
            var uploadInst = upload.render({
                elem: '.btn_upload_img2'
                ,type : 'images'
                ,exts: 'jpg|png|gif|jpeg' //设置一些后缀，用于演示前端验证和后端的验证
                //,auto:false //选择图片后是否直接上传
                //,accept:'images' //上传文件类型
                ,url: '{{url("admin/acts/upload")}}'
                ,data:{'_token':tag_token}
                ,before: function(obj){
                    //预读本地文件示例，不支持ie8
                    obj.preview(function(index, file, result){
                        $('.img-upload-view2').attr('src', result); //图片链接（base64）
                        $('.img-upload-view2').css('display', 'block'); //图片显示
                    });
                }
                ,done: function(res){
                    //如果上传失败
                    if(res.status == 1){
                        $("#lunbo_img2").val(res.message);
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
            //轮播三上传
            var uploadInst = upload.render({
                elem: '.btn_upload_img3'
                ,type : 'images'
                ,exts: 'jpg|png|gif|jpeg' //设置一些后缀，用于演示前端验证和后端的验证
                //,auto:false //选择图片后是否直接上传
                //,accept:'images' //上传文件类型
                ,url: '{{url("admin/acts/upload")}}'
                ,data:{'_token':tag_token}
                ,before: function(obj){
                    //预读本地文件示例，不支持ie8
                    obj.preview(function(index, file, result){
                        $('.img-upload-view3').attr('src', result); //图片链接（base64）
                        $('.img-upload-view3').css('display', 'block'); //图片显示
                    });
                }
                ,done: function(res){
                    //如果上传失败
                    if(res.status == 1){
                        $("#lunbo_img3").val(res.message);
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
