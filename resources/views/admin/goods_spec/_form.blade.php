
    <div class="col-md-6">
        <div class="form-group avatar-picker">
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">规格名称</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="spec_title"  value="{{$spec_title or ''}}" placeholder="规格名称">
            </div>
        </div>



        <div class="form-group">
            <label class="col-sm-3 control-label">规格别名</label>
            <div class="col-sm-9">
                <textarea  class="form-control" name="spec_alias" placeholder="规格别名">{{$spec_alias or ''}}</textarea>
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
                        @if(!empty($spec_values))
                            @foreach($spec_values as $k=>$do)
                                <li style="margin:5px;">
                                    <input type="hidden" name="spec_value_id[]" value="{{$do->spec_value_id}}" >
                                     规格值：<input name="spec_value[]" type="text" class="form-control"   size="18"    value="{{$do->spec_value}}" placeholder="白色"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                     {{--别名：<input name="spec_value_alias[]" type="text" class="form-control"   size="18"     value="{{$do->spec_value_alias}}"    placeholder="white"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;--}}
                                     排序：<input name="spec_value_sort[]" type="text" class="form-control"   size="9"      value="{{$do->spec_value_sort}}"   placeholder="0"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                   @if(!$do->spec_value_id)
                                    <input type="button" class="layui-btn" value="删除" name="null" onclick="deltb(this)"   style="cursor:pointer">
                                    @endif
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>


        <div class="text-center m-t-10">
            <button type="submit" class="btn layui-btn">
                <i class="fa fa-plus-circle"></i>提交
            </button>
            <button type="button"  class="btn layui-btn" onclick="location.href='{{url('admin/goods_spec/index')}}'">
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


        $("#ns").click(function () {

            var add = '<li style="margin:5px;">';
            var add = add + '<input name="spec_value_id[]" type="hidden"    id="spec_value_id" class="form-control"  />';
            var add = add + '规格值：<input name="spec_value[]" type="text" class="form-control"   size="18"    placeholder="白色"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;';
            // var add = add + '别名：<input name="spec_value_alias[]" type="text" class="form-control"   size="18"    placeholder="white"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;';
            var add = add + '排序：<input name="spec_value_sort[]" type="text" class="form-control"   size="9"    placeholder="0"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;';
            var add = add + '<input type="button" class="layui-btn" value="删除" name="null" onclick="deltb(this)"   style="cursor:pointer">';
            var add = add + ' </li>';

            $("#ss").append(add);
        });





    </script>




