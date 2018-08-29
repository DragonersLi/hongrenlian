
    <div class="col-md-6">
        <div class="form-group avatar-picker">
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">商品名称</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="title" id="title"  value="{{$title or ''}}" placeholder="分类名称">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">商品别名</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="alias" id="alias"  value="{{$alias or ''}}" placeholder="商品别名">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">商品库存</label>
            <div class="col-sm-9">
                <input  style="width: 150px; text-align: right;"  class="form-control" type="text" id="store" name="store"  value="{{$store or ''}}" placeholder="商品库存">
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

                <input style="width: 120px; text-align: right;" id="score" name="score" value="{{$score or ''}}" class="form-control" placeholder="红人圈" type="text">


            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">商品类型</label>
            <div class="col-sm-9">
                <select class="form-control"  id="goods_type" name="goods_type"  tabindex="-1" aria-hidden="true" style="width:30%; ">
                    <option value="-1">请选择</option>
                    <option value="0" @if(isset($goods_type) && $goods_type ==0) selected="selected" @endif>虚拟</option>
                    <option value="1"  @if(isset($goods_type) && $goods_type ==1) selected="selected" @endif>实物</option>
                </select>
            </div>
        </div>
        @if(empty($goods_id))
        <div id="sku_index">
        <div class="form-group" id="goods_spec">
            <label class="col-sm-3 control-label">商品规格</label>
            <div class="col-sm-9" id="spec_key">

                @foreach($spec as $k=>$v)
                    <input  type="checkbox" class="spec_key" spec_title="{{$v['spec_title']}}" name="spec[]"  value="{{$v['spec_id']}}" >{{$v['spec_title']}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                @endforeach

            </div>
        </div>
        <div class="form-group" id="spec_value">
            <label class="col-sm-3 control-label">规格值</label>
            <div class="col-sm-9">
                <div id="spec_value_index" style="border:1px solid black;">
                        {{--这里是规格值追加--}}
                </div>
            </div>
        </div>
            @endif
        <div class="form-group" id="sku">
            @if(empty($goods_id))
            <label class="col-sm-3 control-label">   SKU管理  </label>
            <input  type="button" class="layui-btn  layui-btn-normal"   id="generatesSKU" value="生成SKU" style="margin:10px;">
            @endif
            <div class="form-group">
                <label class="col-sm-3 control-label"></label>
                <div class="col-sm-9">
                    <div id="sku_items">
                        {{--这里是sku生成--}}
                        @if(!empty($products))
                            @foreach($products as $k=>$v)
                                <p  style="margin:5px;">
                                @foreach($v['spec_index'] as $k1=>$v1)
                               <input type="text" value="{{$v1['spec_value']}}"  readonly class="form-control"  style="margin:5px;width:80px;height:30px;display:inline;">
                                @endforeach

                                    <input type="text" value="{{$v['product_score']}}"  class="form-control"  style="margin:5px;width:80px;height:30px;display:inline;">
                                    <input type="text" value="{{$v['product_store']}}"  class="form-control"  style="margin:5px;width:80px;height:30px;display:inline;">
                                    <input type="button" class="layui-btn @if(empty($v['status']))  layui-btn-danger @else  @endif" value="@if(empty($v['status']))恢复@else删除@endif"  product_id="{{$v['product_id']}}" onclick="deleteSKU(this)" onclickss='window.location.href="{{url('admin/goods/skuStatus',['product_id'=>$v['product_id']])}}"'   style="cursor:pointer">
                                </p>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>



        <div class="form-group">
            <label class="col-sm-3 control-label">   属性管理  </label>
            <input  type="button" class="layui-btn  layui-btn-normal"  name="button" id="attr_create" value="新增属性">

        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"></label>
            <div class="col-sm-9">
                <div id="attr">
                    <ul>
                        @if(!empty($attr))
                            @foreach($attr as $k=>$v)
                                    <li style="margin:5px;">
                                        属性名：<input name="attr[key][]" type="text" class="form-control"   size="24"   value="{{$v['key']}}" placeholder="属性名"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                        属性值：<input name="attr[val][]" type="text" class="form-control"   size="24"  value="{{$v['val']}}"  placeholder="属性值"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="button" class="layui-btn" value="删除"   onclick="deleteAttr(this)"   style="cursor:pointer">
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
                    {{--商品图集--}}
                    @if (!empty($pictures))
                        @foreach ($pictures as $key => $value)
                            <input name="pictures[]" value="{{ $value }}" type="hidden">
                        @endforeach
                    @endif

                </div>

            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">商品描述文字</label>
            <div class="col-sm-9">
                <textarea  class="form-control" style="height: 150px" name="desc" id="desc" placeholder="商品描述文字" style="height:500px;">{{$desc or ''}}</textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">商品描述图片</label>
            <div class="col-sm-9">
                <textarea  class="form-control" name="intro" id="intro" placeholder="商品描述图片" style="height:500px;">{{$intro or ''}}</textarea>
            </div>
        </div>

        @include('kindeditor::editor',['editor'=>'intro'])

        <div class="text-center m-t-10">
            <button type="submit" class="btn layui-btn">
                <i class="fa fa-plus-circle"></i>提交
            </button>
                <button type="button"  class="btn layui-btn" onclick="location.href='{{url('admin/goods/index')}}'">
                <i class="fa fa-mail-reply-all"></i>返回上级
            </button>

        </div>
    </div>

    <script src="/plugins/jQuery/jQuery-2.2.0.min.js"></script>
    <script src="/plugins/laydate/laydate.js"></script> <!-- lay日期插件 -->
    <script type="text/javascript" src="/layui/layui.js"></script> <!-- lay上传插件 -->
    <link rel="stylesheet" type="text/css" href="/layui/css/layui.css" /> <!-- lay上传插件样式 -->


    <script type="text/javascript">
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

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        function goods_type(){//选择实物或者虚拟物品 //-1 未选择 0 为虚拟货品 1 为实物
            $('#goods_type').val() > 0 ? $("#sku_index").css('display','block') : $("#sku_index").css('display','none');
        }
        function spec_key(spec_id,spec_title){ //选中规格key,展示规格value
                $.ajax({
                    type: "POST",
                    url: "{{url('admin/goods/getSpecValues')}}",
                    data: { spec_id:spec_id ,spec_title:spec_title , _token:"{{csrf_token()}}"},
                    //dataType: "json",
                    success: function(data){
                        $('.spec_value_status_'+ spec_id).remove();
                        $('#spec_value_index').append(data);
                    },
                    error: function(data){
                        console.log(data);
                    }
                });
        }

        $(function(){
            // goods_type();
            // $("#goods_type").bind("click",function(){
            //     goods_type();
            // });
            $("#spec_key :input[type='checkbox']:checked[class='spec_key']").each(function(){

                var spec_id = $(this).val();//规格id
                var spec_title = $(this).attr('spec_title');//规格
                spec_key(spec_id,spec_title);//获取该规格下的所有值
            });

            $(".spec_key").bind("click",function(){//选中规格key,展示规格value
                var spec_id = $(this).val();//规格id
                var spec_title = $(this).attr('spec_title');//规格
                if($(this).is(":checked")){//选中
                    spec_key(spec_id,spec_title);//获取该规格下的所有值
                }else{//取消
                    $('.spec_value_status_'+ spec_id).remove();
                }

            });
            //属性
            $("#attr_create").click(function () {
                var attr = '<p style="margin:5px;">' +
                    '属性名：<input name="attr[key][]" type="text" class="form-control"   size="24"    placeholder="属性名"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                    '属性值：<input name="attr[val][]" type="text" class="form-control"   size="24"    placeholder="属性值"   style="width:150px;height:30px;display:inline;"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                    '<input type="button" class="layui-btn" value="删除"  onclick="deleteAttr(this)"   style="cursor:pointer">' +
                    '</p>';

                $("#attr").append(attr);
            });

            //生成sku
            $("#generatesSKU").bind("click",function(){//sku_items
                $('#sku_items').empty();//每次点击生成sku，先清空旧数据
                array = [];//全局变量
                store = $('#store').val(); //库存
                score = $('#score').val(); //红人圈
                $("#spec_value_index :input[type='checkbox']:checked[class='spec_value_index']").each(function(i,sku_value){//选中的规格值

                    var spec_id = $(this).attr('spec_id');//规格名id
                    var spec_title = $(this).attr('spec_title');//规格名
                    var spec_value_id = $(this).val();//规格值id
                    var spec_value = $(this).attr('spec_value');//规格值
                    var spec_data = $(this).attr('spec_data');//sku规格拼接

                    var spec_data;
                    var spec_data =  {'spec_id':spec_id,'spec_title':spec_title,'spec_value_id':spec_value_id,'spec_value':spec_value,'spec_data':spec_value};
                        array.push(spec_data); // arr.push(sku_value[i]); //  array.push({'spec_data':spec_data});
                });

                $.ajax({
                    type: 'POST',
                    url: '{{url("admin/goods/generateSKU")}}',
                    data: { sku :JSON.stringify(array), _token:"{{csrf_token()}}"},
                    dataType: 'json',
                    success: function(data){
                        var array = data;
                        var results = [];　//创建一个数组
                        var len = array.length;
                        var indexs = {};
                        generateSKU(-1);
                        var html = '';
                        for (var i = 0; i < results.length; i++)
                        {    var html = html + '<p>';
                             var spec_str =  '';
                             var spec_ids = '';
                             var spec_titles = '';
                             var spec_value_ids = '';
                             var spec_values = '';

                            for(var j in results[i]){
                                for(var k in results[i][j]){
                                    if(k == 'spec_id'){
                                        spec_ids = spec_ids +  results[i][j][k] +'@';
                                        spec_str = spec_str + results[i][j][k] +'||';
                                    }
                                    if(k == 'spec_title'){
                                        spec_titles = spec_titles +  results[i][j][k] +'@';
                                        spec_str = spec_str +  results[i][j][k] +'||';
                                    }
                                    if(k == 'spec_value_id'){
                                        spec_value_ids = spec_value_ids +  results[i][j][k] +'@';
                                        spec_str = spec_str + results[i][j][k] +'||';
                                    }
                                    if(k == 'spec_value'){
                                        spec_values = spec_values +  results[i][j][k] +'@';
                                        spec_str = spec_str + results[i][j][k] +'||';
                                    }

                                    if(k == 'spec_data'){
                                        var html = html + '<input  value="'+results[i][j][k]+'"   readonly class="form-control"  style="margin:5px;width:80px;height:30px;display:inline;"/>';
                                    }
                                }
                             }
                            var html = html + '<input name="sku_items[spec_id][]" type="hidden"  value="'+spec_ids+'"   class="form-control"  style="margin:5px;width:80px;height:30px;display:inline;"/>';
                            var html = html + '<input name="sku_items[spec_title][]" type="hidden"  value="'+spec_titles+'"   class="form-control"  style="margin:5px;width:80px;height:30px;display:inline;"/>';
                            var html = html + '<input name="sku_items[spec_value_id][]" type="hidden"  value="'+spec_value_ids+'"   class="form-control"  style="margin:5px;width:80px;height:30px;display:inline;"/>';
                            var html = html + '<input name="sku_items[spec_value][]" type="hidden"  value="'+spec_values+'"   class="form-control"  style="margin:5px;width:80px;height:30px;display:inline;"/>';
                            var html = html + '<input name="sku_items[sku][]" type="hidden"  value="'+spec_str+'"   class="form-control"  style="margin:5px;width:80px;height:30px;display:inline;"/>';
                            var html = html + '<input name="sku_items[store][]" type="text"    value="'+store+'"   class="form-control"  style="margin:5px;width:80px;height:30px;display:inline;"/>'+
                                '<input name="sku_items[score][]" type="text"    value="'+score+'"   class="form-control"  style="margin:5px;width:80px;height:30px;display:inline;"/>'+
                                '<input type="button" class="layui-btn" value="删除" onclick="deleteSKU(this)" product_id="0"  style="margin:5px;cursor:pointer"></p>';

                        }
                        $("#sku_items").append("<div>" + html + "</div>");

                        function generateSKU(start) {
                            start++;
                            if (start > len - 1) {
                                return;
                            }
                            if (!indexs[start]) {
                                indexs[start] = 0;
                            }
                            if (!(array[start] instanceof Array)) {
                                array[start] = [array[start]];
                            }
                            for (indexs[start] = 0; indexs[start] < array[start].length; indexs[start]++) {
                                generateSKU(start);
                                if (start == len - 1) {
                                    var temp = [];
                                    for (var i = len - 1; i >= 0; i--) {
                                        if (!(array[start - i] instanceof Array)) {
                                            array[start - i] = [array[start - i]];
                                        }
                                        temp.push(array[start - i][indexs[start - i]]);
                                    }
                                    results.push(temp);
                                }
                            }

                        }
                    },
                    error: function(xhr, status, error){
                        console.log(xhr);
                        console.log(status);
                        console.log(error);
                    }
                });
            });
        });

        //删除属性
        function deleteAttr(attr) {
                $(attr).parent().remove();
        }

        //删除或恢复sku
        function deleteSKU(sku) {
            var product_id = $(sku).attr("product_id");
            //var sku_status = $(sku).attr("sku_status");
            if (product_id =='0') {
                $(sku).parent().remove();
            }
        }
</script>

