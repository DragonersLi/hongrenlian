
    <div class="col-md-6">
        <div class="form-group avatar-picker">
        </div>


        <div class="form-group">
            <label class="col-sm-3 control-label">众筹名称</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="title"  value="{{$title or ''}}" placeholder="众筹名称">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">众筹主图</label>
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
            <label class="col-sm-3 control-label">总目标红人圈数量</label>
            <div class="input-group">


                <input style="width: 120px; text-align: right;" id="count_score" name="count_score" value="{{$count_score or ''}}" class="form-control" placeholder="0.00" type="text">

            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">当前认筹红人圈数量</label>
            <div class="input-group">


                <input style="width: 120px; text-align: right;" id="current_score" name="current_score" value="{{$current_score or ''}}" class="form-control" placeholder="0.00" type="text">

            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">认筹比例</label>
            <div class="input-group">


                <input style="width: 120px; text-align: right;" id="scale" name="scale" value="{{$scale or ''}}" class="form-control" placeholder="" type="text">

            </div>
        </div>



        <div class="form-group">
            <label class="col-sm-3 control-label">众筹时间</label>
            <div class="col-sm-9">
                <input class="form-control"  type="text" name="rangedatetime" id="rangedatetime"  value="{{$rangedatetime or ''}}" placeholder="众筹有效时间">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">获得礼品</label>
            <div class="col-sm-9">
                <textarea class="form-control" name="gift"   placeholder="获得礼品">{{$gift or ''}}</textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">众筹详情</label>
            <div class="col-sm-9">
                <textarea  class="form-control" name="intro" id="intro" placeholder="众筹详情" style="height:500px;">{{$intro or ''}}</textarea>
            </div>
        </div>

        @include('kindeditor::editor',['editor'=>'intro'])


        <div class="text-center m-t-10">
            <button type="submit" class="btn layui-btn">
                <i class="fa fa-plus-circle"></i>提交
            </button>
                <button type="button"  class="btn layui-btn" onclick="location.href='{{url('admin/crowdfunding/index')}}'">
                    <i class="fa  fa-mail-reply-all"></i>返回上级
                </button>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-7 col-md-offset-3">

        </div>
    </div>

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
                ,url: '{{url("admin/crowdfunding/upload")}}'
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

        });

    </script>
