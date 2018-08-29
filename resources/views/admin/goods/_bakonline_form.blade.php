
    <div class="col-md-6">
        <div class="form-group avatar-picker">
        </div>

        {{--<div class="form-group">--}}
            {{--<label class="col-sm-3 control-label">商品分类</label>--}}
            {{--<div class="col-sm-9">--}}
                {{--<select class="form-control"  id="cat1" name="cat1"  tabindex="-1" aria-hidden="true" style="width:30%;display:inline;float:left;">--}}
                    {{--<option value="0">请选择</option>--}}
                    {{--@foreach($cats as $k=>$v)--}}
                        {{--<option value="{{$v->id}}" @if(isset($cat1) && $cat1 == $v->id) checked @endif>{{$v->title}}</option>--}}
                    {{--@endforeach--}}
                {{--</select>--}}

                {{--<select class="form-control"  id="cat2" name="cat2"  tabindex="-1" aria-hidden="true" style="width:30%;display:inline;float:left;display:none;">--}}
                    {{--<option value="0" >请选择</option>--}}
                {{--</select>--}}
                {{--<select class="form-control"  id="cat3" name="cat3"  tabindex="-1" aria-hidden="true" style="width:30%;display:inline;float:left;display:none;">--}}
                    {{--<option value="0" >请选择</option>--}}
                {{--</select>--}}
            {{--</div>--}}
        {{--</div>--}}
        <div class="form-group">
            <label class="col-sm-3 control-label">商品名称</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" id="title"  value="{{$title or ''}}" placeholder="分类名称">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">商品别名</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" id="alias"  value="{{$alias or ''}}" placeholder="商品别名">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">商品库存</label>
            <div class="col-sm-9">
                <input  style="width: 150px; text-align: right;"  class="form-control" type="text" id="kucun" name="kucun"  value="{{$kucun or ''}}" placeholder="商品库存">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">商品单位</label>
            <div class="col-sm-9">
                <input  style="width: 150px; text-align: right;"  class="form-control" type="text" id="unit" name="unit"  value="{{$unit or ''}}" placeholder="商品单位">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">商品价格</label>
            <div class="input-group">

                <span class="input-group-addon">￥</span>

                <input style="width: 120px; text-align: right;" id="price" name="price" value="{{$price or ''}}" class="form-control" placeholder="0.00" type="text">


            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">所需红人圈</label>
            <div class="input-group">

                <span class="input-group-addon">￥</span>

                <input style="width: 120px; text-align: right;" id="memprice" name="memprice" value="{{$memprice or ''}}" class="form-control" placeholder="红人圈" type="text">


            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">商品类型</label>
            <div class="col-sm-9">
                <select class="form-control"  id="goods_type" name="goods_type"  tabindex="-1" aria-hidden="true" style="width:30%; ">
                    <option value="1" >实物</option>
                    <option value="0" >虚拟</option>
                </select>
            </div>
        </div>
        <div class="form-group" id="goods_spec">
            <label class="col-sm-3 control-label">商品规格</label>
            <div class="col-sm-9" id="sss">
                @foreach($spec['item'] as $k=>$v)
                    <input  type="checkbox"  name="spec"  value="{{$v->spec_id}}" >{{$v->spec_title}}
                @endforeach
            </div>
        </div>
        <div class="form-group" id="spec_item">
            <label class="col-sm-3 control-label"></label>
            <div class="col-sm-9">
                @foreach($spec['item'] as $ky=>$a)
                    <div style="width:150px;height:100px;border:1px solid green;float:left;display:none;" id="item{{$a->spec_id}}">
                        @foreach($spec['value'] as $k=>$v)
                            @if($a->spec_id==$v->spec_id)
                                <input type="checkbox" class="{{$v->spec_id}}" name="ittt{{$ky}}" id="{{$v->spec_value_id}}" value="{{$v->spec_value}}">{{$v->spec_value}}
                            @endif
                        @endforeach
                    </div>
                @endforeach

            </div>
        </div>
        <div class="form-group" id="sku">
            <label class="col-sm-3 control-label">   SKU管理  </label>
            <input  type="button" class="layui-btn  layui-btn-normal"  name="button" id="ng" value="生成SKU">

        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"></label>
            <div class="col-sm-9">
                <div id="gg">
                    <ul>
                        @if(!empty($products))
                            @foreach($products as $k=>$do)
                                {{--<volist name="products" id="do" key ='k'>--}}
                                <li style="margin:5px;">
                                    <input name="product_id[]" type="hidden"  class="form-control" id="product_id" value="{{$do->product_id}}"  style="width:80px;height:30px;display:inline;"/>
                                    <input name="ys[]" type="text" class="form-control"  id="ys[]" size="12"  value="{{$do->ys}}" placeholder="规格一"  style="width:80px;height:30px;display:inline;"/>
                                    <input name="cm[]" type="text" class="form-control"  id="cm[]" size="12" value="{{$do->cm}}" placeholder="规格二"  style="width:80px;height:30px;display:inline;"/>
                                    <input name="kc[]" type="text" class="form-control"  id="kc[]" size="12" value="{{$do->kc}}" placeholder="库存"  style="width:80px;height:30px;display:inline;"/>
                                    <input name="jg[]" type="text" class="form-control"  id="jg[]" size="12"  value="{{$do->jg}}"  placeholder="红人圈"  style="width:80px;height:30px;display:inline;"/>
                                    <input name="{{$do->product_id}}" type="button" class="layui-btn" @if($do->status) value="删除" @else value="恢复" @endif onclick="deltb(this)"  data_status="{{$do->status}}"  style="cursor:pointer">
                                </li>
                                {{--</volist>--}}
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">   属性管理  </label>
            <input  type="button" class="layui-btn  layui-btn-normal"  name="button" id="ns" value="新增属性">

        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"></label>
            <div class="col-sm-9">
                <div id="ss">
                    <ul>
                        @if(!empty($attr))
                            @foreach($attr as $k=>$do)
                                    <li style="margin:5px;">
                                        属性名：<input name="attr[]" type="text" class="form-control"   size="24"   value="{{$do['attr']}}" placeholder="产地"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                        属性值：<input name="vals[]" type="text" class="form-control"   size="24"  value="{{$do['val']}}"  placeholder="杭州"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="button" class="layui-btn" value="删除" name="null" onclick="deltb(this)"   style="cursor:pointer">
                                    </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">商品图集</label>
            <div class="col-sm-9">

                <div class="layui-upload">
                    <button type="button" class="layui-btn layui-btn-normal" id="testList">选择多文件</button>(主图默认为第一张)
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    <button type="button" class="layui-btn" id="testListAction">开始上传</button>
                    <div class="layui-upload-list">
                        <table class="layui-table">
                            <thead>
                            <tr><th>文件名</th>
                                <th>大小</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr></thead>

                            <tbody id="demoList">
                            @if (!empty($pictures))
                                @foreach ($pictures as $key => $value)
                                    <tr><th>{{ $value }}</th><th>大小</th><th>已上传</th><th><img src="{{ $value }}" style="width:200px;"></th></tr>
                                @endforeach
                            @endif

                            </tbody>

                        </table>
                    </div>
                </div>
                <div id="hidden_pictures">
                    
                </div>
                {{--<div class="layui-upload">--}}
                    {{--<blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">--}}
                        {{--预览图：--}}
                        {{--<div class="layui-upload-list" id="demo2"></div>--}}
                    {{--</blockquote>--}}
                {{--</div>--}}

            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">商品描述文字</label>
            <div class="col-sm-9">
                <textarea  class="form-control" style="height: 150px" name="intro_spec" id="intro_spec" placeholder="商品描述文字" style="height:500px;"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">商品描述图片</label>
            <div class="col-sm-9">
                <textarea  class="form-control" name="intro" id="intro" placeholder="商品描述图片" style="height:500px;">
                </textarea>
            </div>
        </div>

        @include('kindeditor::editor',['editor'=>'intro'])

        <div class="text-center m-t-10">
            <button type="button" class="btn layui-btn" id="add_goods">
                <i class="fa fa-plus-circle"></i>提交
            </button>
            <input type="hidden"  name="goods_sn"   value="{{$goods_sn or ''}}">
                <button type="button"  class="btn layui-btn" onclick="location.href='{{url('admin/goods/index')}}'">
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


    <script>
        layui.use('upload', function(){
            var $ = layui.jquery
                ,upload = layui.upload;

            //多文件列表示例
            var demoListView = $('#demoList')
                ,uploadListIns = upload.render({
                elem: '#testList'
                ,url: '{{url("admin/goods/upload")}}'
                ,field:'pictures'
                ,accept: 'images'
                ,size: 1024*5
                ,multiple: true
                ,auto: false
                ,headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
                ,bindAction: '#testListAction'
                ,choose: function(obj){
                    var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
                    //读取本地文件
                    obj.preview(function(index, file, result){
                        var tr = $(['<tr id="upload-'+ index +'">'
                            ,'<td>'+ file.name +'</td>'
                            ,'<td>'+ (file.size/1014).toFixed(1) +'kb</td>'
                            ,'<td>等待上传</td>'
                            ,'<td>'
                            ,'<button class="layui-btn layui-btn-mini demo-reload layui-hide">重传</button>'
                            ,'<button class="layui-btn layui-btn-mini layui-btn-danger demo-delete">删除</button>'
                            ,'</td>'
                            ,'</tr>'].join(''));

                        //单个重传
                        tr.find('.demo-reload').on('click', function(){
                            obj.upload(index, file);
                        });

                        //删除
                        tr.find('.demo-delete').on('click', function(){
                            delete files[index]; //删除对应的文件
                            tr.remove();
                            uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                        });

                        demoListView.append(tr);
                    });
                }
                ,done: function(res, index, upload){
                    if(res.code == 0){ //上传成功
                        console.log(res.ResultData);
                        // $('#demo2').append('<img src="/upload/image/goods/'+ res.ResultData +'" alt="'+ res.ResultData +'" class="layui-upload-img" style="display:inline;width:100px;heigth:100px;">' +
                        //     '<input type="hidden" name="pictures[]" value="/upload/image/goods/'+ res.ResultData +'">')

                        var tr = demoListView.find('tr#upload-'+ index)
                            ,tds = tr.children();
                        tds.eq(2).html('<span style="color: #5FB878;">上传成功</span>');
                        tds.eq(3).html('<img src="/upload/image/goods/'+ res.ResultData +'" />');
                        //tds.eq(3).html(''); //清空操作
                        $('#hidden_pictures').append("<input type='hidden' name='pictures[]' value='/upload/image/goods/"+  res.ResultData +"' />");
                        return delete this.files[index]; //删除文件队列已经上传成功的文件
                    }
                    this.error(index, upload);
                }
                ,error: function(index, upload){
                    var tr = demoListView.find('tr#upload-'+ index)
                        ,tds = tr.children();
                    tds.eq(2).html('<span style="color: #FF5722;">上传失败</span>');
                    tds.eq(3).find('.demo-reload').removeClass('layui-hide'); //显示重传
                }
            });
        });

    </script>

    <script>
        function dobj(id)
        {
            $("#til li").each(function () {
                $('#til li').css({'background-color': '#F7FAFC', 'color': '#333333'});
            });
            $(id).css({'background-color': '#1B7BB8', 'color': '#ffffff'});
            $("#c").html("");
            $("#c_v").html("");
            $("#m").html($(id).text());
            $("#m_v").html($(id).attr("id"));
            var all = ($(id).attr("id"));
            for (var i = 1; i < 10000; i++)
            {
                $("#t_" + i).hide();
            }
            $("#t_" + all).show();
        }
        function redhov(nm)
        {
            $(nm).hover(function ()
                {
                    $(nm).css('background-color', '#E3EFFB');
                },
                function ()
                {
                    $(nm).css('background-color', '');
                }
            );
            $(nm).click(function ()
                {
                    $("#c").html("/" + $(nm).parent().parent().find("span").html() + "/" + $(nm).html());
                    $("#c_v").html($(nm).parent().parent().find("span").attr("id") + "_" + $(nm).attr("id"));
                }
            );
        }
        function sur()
        {
            if ($("#c_v").text() == "")
            {
                // alert("请选择子栏目！");
            } else
            {
                $("#lan").val($("#m_v").html() + "_" + $("#c_v").html());
                $("#lanmuid").val($("#m").html() + $("#c").html());
                $("#dlg").hide();
            }
        }

        function deltb(aa) {

            var product_id = $(aa).attr("name");
            var status = $(aa).attr("data_status");
            //var statue = $(aa).val();//alert(product_id+'---'+data_status+'==='+status);return;
            if (product_id == 'null') {
                $(aa).parent().remove();
            } else {

                $.ajax({
                    type: 'POST',
                    url: '{{url("admin/goods/skuStatus")}}',
                    data: { product_id : product_id,status : status, _token:"{{csrf_token()}}"},
                    dataType: 'json',
                    success: function(data){
                        alert(333);return ;
                        //验证成功后实现跳转

                    },
                    error: function(xhr, status, error){
                        console.log(xhr);
                        console.log(status);
                        console.log(error);
                    }
                });


            }
        }
        //生成sku 
        $("#ng").click(function () {
            var obj = document.getElementsByName("ittt0");
            var obj1 = document.getElementsByName("ittt1");
            var memprice=$("#memprice").val();
            if(memprice==''){
                memprice=0;
            }
            var kucun=$("#kucun").val();
            if(kucun==''){
                kucun=0;
            }
            for(k in obj){
                for(s in obj1){
                    if(obj1[s].checked==true&&obj[k].checked==true){
                        var spec_id=$(obj[k]).attr('class');
                        var spec_value_id=$(obj[k]).attr('id');
                        var spec_value_name=obj[k].value;
                        var spec_id1=$(obj1[s]).attr('class');
                        var spec_value_id1=$(obj1[s]).attr('id');
                        var spec_value_name1=obj1[s].value;
                        var add = '<li style="margin:5px;">';
                        var add = add + '<input name="product_id[]" type="hidden"    id="product_id" class="form-control"  />';
                        var add = add + '<input name="ys[]" type="text" class="form-control" id="'+spec_id+'|'+spec_value_id+'" name="ys[]" size="12" placeholder="规格一" value="' + spec_value_name + '" style="width:80px;height:30px;display:inline;">&nbsp;';
                        var add = add + '<input name="cm[]" type="text" class="form-control"  id="'+spec_id1+'|'+spec_value_id1+'" name="cm[]" size="12" placeholder="规格二"  value="' + spec_value_name1 + '" style="width:80px;height:30px;display:inline;">&nbsp;';
                        var add = add + '<input name="kc[]" type="text" class="form-control"  id="'+spec_id+'|'+spec_value_id+'" name="kc[]" size="12" placeholder="库存"  value="'+kucun+'" style="width:80px;height:30px;display:inline;">&nbsp;';
                        var add = add + '<input name="jg[]" type="text" class="form-control"  id="'+spec_id+'|'+spec_value_id+'" name="jg[]" size="12"  placeholder="红人圈"  value="'+memprice+'"  style="width:80px;height:30px;display:inline;"/>&nbsp;';
                        var add = add + '<input type="button" class="layui-btn" value="删除" name="null" onclick="deltb(this)"   style="cursor:pointer">';
                        var add = add + ' </li>';
                        $("#gg").append(add);
                    }
                }
            }
        });

        $("#ns").click(function () {

            var add = '<li style="margin:5px;">';
            var add = add + '属性名：<input name="attr[]" type="text" class="form-control"   size="24"    placeholder="产地"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            var add = add + '属性值：<input name="vals[]" type="text" class="form-control"   size="24"    placeholder="杭州"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            var add = add + '<input type="button" class="layui-btn" value="删除" name="null" onclick="deltb(this)"   style="cursor:pointer">';
            var add = add + ' </li>';

            $("#ss").append(add);
        });




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
        /**
         * 商品分类筛选cat2
         */
        $("#cat2").change(function(){
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

                        if( data !=''){
                            $("#cat3").empty();
                            $("#cat3").css('display','inline');
                            $("#cat3").append(data);//给area下拉框添加option
                        }else{
                            $("#cat3").css('display','none');
                        }
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
    //选择实物或者虚拟物品
    $("#goods_type").bind("change",function(){
        var checkval=$(this).val();//-1 未选择 0 为虚拟货品 1 为实物
        if(checkval==0){
            $("#goods_spec").hide();
            var obj = document.getElementsByName("spec");
            obj.disabled=true;
            $("#sku").hide();
        }else if(checkval==1){
            $("#goods_spec").show();
            $("#sku").show();
            var obj = document.getElementsByName("spec");
            obj.disabled=false;
        }
    });
    //选择商品规格项
    $("#sss").click(function(){
        var obj = document.getElementsByName("spec");
        check_val = [];
        for(k in obj){
            if(obj[k].checked){
                 $("#item"+obj[k].value).show();
                check_val.push(obj[k].value);
            }else{
                $("#item"+obj[k].value).hide();
            }
               
        }
    });
    //添加商品  
    $("#add_goods").click(function(){
        var title = $("#title").val();
        var alias = $("#alias").val();
        var kc = $("#kucun").val();
        var unit = $("#unit").val();
        var price = $("#price").val();
        var jg = $("#memprice").val();
        var goods_type = $("#goods_type").val();
        var intro = $("#intro").val();//商品描述图片
        var intro_spec = $("#intro_spec").val();//商品描述文字
        var pictures=document.getElementsByName("pictures[]");
        var check_val=[];//商品图片
        var indexpic=[];//商品主图
        for(var i=0;i<pictures.length;i++){
            check_val.push(pictures[i].value);
            indexpic=pictures[0].value;//商品主图片
        }
        //规格 sku
        var ys=document.getElementsByName("ys[]");
        var cm=document.getElementsByName("cm[]");
        var kucun=document.getElementsByName("kc[]");
        var mprice=document.getElementsByName("jg[]");
        var ys_val=[];
        var cm_val=[];
        var kucun_val=[];
        var mprice_val=[];
        for(var i=0;i<ys.length;i++){
            ys_val.push(ys[i].id);
            cm_val.push(cm[i].id);
            kucun_val.push(kucun[i].value);
            mprice_val.push(mprice[i].value);
        }
        //属性 与值对应参数
        var attr=document.getElementsByName("attr[]");
        var vals=document.getElementsByName("vals[]");
        var attr_val=[];
        var vals_val=[];
        for(var i=0;i<attr.length;i++){
            attr_val.push(attr[i].value);
            vals_val.push(vals[i].value);
        }
        //提交数据库
        $.ajax({
                url:'{{url('admin/goods/add')}}',
                type:'get',
                data:"title="+title+"&alias="+alias+"&kc="+kc+"&jg="+jg+"&unit="+unit+"&price="+price+"&goods_type="+goods_type+"&intro="+intro+"&intro_spec="+intro_spec+"&check_val="+check_val+"&indexpic="+indexpic+"&mprice_val="+mprice_val+"&ys_val="+ys_val+"&cm_val="+cm_val+"&kucun_val="+kucun_val+"&attr_val="+attr_val+"&vals_val="+vals_val,
                success:function(msg){
                    console.log(msg);
                },
                error: function(msg){
                    console.log(msg);
                }
        });
    })


</script>

