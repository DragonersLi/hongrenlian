
    <div class="col-md-6">
        <div class="form-group avatar-picker">
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">上级分类</label>
            <div class="col-sm-9">
            <select class="form-control"  id="cat1" name="cat1"  tabindex="-1" aria-hidden="true" style="width:30%;display:inline;float:left;">
                <option value="0">请选择</option>
                @foreach($cats as $k=>$v)
                    <option value="{{$v->id}}" @if(isset($cat1) && $cat1 == $v->id) checked @endif>{{$v->title}}</option>
                    @endforeach
            </select>

                <select class="form-control"  id="cat2" name="cat2"  tabindex="-1" aria-hidden="true" style="width:30%;display:inline;float:left;display:none;">
                    <option value="0" >请选择</option>

                </select>

                {{--<select class="form-control"  id="cat3" name="cat3"  tabindex="-1" aria-hidden="true" style="width:30%;display:inline;float:left;display:none;">--}}
                    {{--<option value="0" >请选择</option>--}}

                {{--</select>--}}



            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">分类名称</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="title"  value="{{$title or ''}}" placeholder="分类名称">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">分类图标</label>
            <div class="col-sm-9">
                <div class="layui-form-item">
                    <div class="layui-upload">
                        {{--<button type="button" name="img_upload" class="layui-btn btn_upload_img">--}}
                        {{--<i class="layui-icon">&#xe67c;</i>上传图片--}}
                        {{--</button>--}}
                        <input type="hidden" name="icon" id="icon"  value="{{$icon or ''}}">
                        <input type="hidden" name="_token" class="tag_token" value="{{csrf_token()}}">
                        <img class="btn_upload_img layui-upload-img img-upload-view" src="{{$icon or ''}}">
                        <p id="demoText"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">是否热门</label>
            <div class="col-sm-9">
                <label for="radio_male">是</label>
                <input type="radio" name="is_hot" value="1" @if(isset($is_hot) && $is_hot == 1) checked @endif>
                <label for="radio_female">否</label>
                <input type="radio" name="is_hot" value="0" @if(!isset($is_hot) || $is_hot == 0) checked @endif>
            </div>
        </div>
        {{--<div class="form-group">--}}
            {{--<label class="col-sm-3 control-label">适用性别</label>--}}
            {{--<div class="col-sm-9">--}}
                {{--<label for="radio_male">通用</label>--}}
                {{--<input type="radio" name="sex" value="0" @if(!isset($sex) || $sex == 0) checked @endif>--}}
                {{--<label for="radio_male">男性</label>--}}
                {{--<input type="radio" name="sex" value="1" @if($sex == 1) checked @endif>--}}
                {{--<label for="radio_female">女性</label>--}}
                {{--<input type="radio" name="sex" value="2" @if($sex == 2) checked @endif>--}}
            {{--</div>--}}
        {{--</div>--}}

        <div class="form-group">
            <label class="col-sm-3 control-label">分类描述</label>
            <div class="col-sm-9">
                <textarea  class="form-control" name="desc" placeholder="分类描述">{{$desc or ''}}</textarea>
            </div>
        </div>



        <div class="text-center m-t-10">
            <button type="submit" class="btn layui-btn">
                <i class="fa fa-plus-circle"></i>提交
            </button>
            <button type="button"  class="btn layui-btn" onclick="location.href='{{url('admin/goods_cat/index')}}'">
                <i class="fa fa-mail-reply-all"></i>返回上级
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
                ,url: '{{url("admin/goods_cat/upload")}}'
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
                        $("#icon").val(res.message);
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

function getCat(){

}

    $(function(){
   // alert($("#cat1").val());
    /**
    * 商品分类筛选cat1
    */
    $("#cat1").change(function(){
        var cat_id = $(this).val();
        if(cat_id != ''){
            $.ajax({
                type: "post",
                url: "{{url('admin/goods_cat/getChildCat')}}",
                data:"id="+cat_id+"&_token={{csrf_token()}}",

                cache:false,
                beforeSend: function(XMLHttpRequest){
                },
                success: function(data, textStatus){

                    if( data !='' ){

                        $("#cat2").empty();
                        $("#cat3").empty();
                        $("#cat2").css('display','inline');
                        $("#cat2").append(data);//给area下拉框添加option

                    }else{
                        $("#cat2").css('display','none');

                    }
                    $("#cat3").css('display','none');

                },
                complete: function(XMLHttpRequest, textStatus){
                },
                error: function(){
                    //请求出错处理
                }
            });
        }

    });


    })
    </script>


