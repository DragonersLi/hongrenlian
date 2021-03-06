@extends('admin.layouts.base')

@section('title','控制面板')

@section('pageHeader','控制面板')

@section('pageDesc','DashBoard')

@section('content')
    <div class="box box-info">
        <div class="box-header width-border">
            <h3 class="box-title">搜索</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <form action="{{url('admin/finance/index')}}" method="post">
                    <div class="col-lg-2">
                        <input type="text"  name="keywords"  value="{{$page['keywords'] or ''}}"  class="form-control" placeholder="请输入用户名搜索" />
                    </div>
                    <div class="col-lg-2" style="width: 10%;" >
                        <select class="form-control"  id="action" name="action"  >
                                <option value="-1">请选择</option>
                            <option value="0" @if(isset($page['action']) && $page['action'] == 0) selected="selected" @endif  >收入</option>
                            <option value="1" @if(isset($page['action']) && $page['action'] == 1) selected="selected" @endif  >支出</option>
                        </select>
                    </div>
                    <div class="col-lg-2" style="width: 10%;" >
                        <select class="form-control"  id="type" name="type" >
                            @if(!empty($page['type']))
                                @foreach($income as $k=>$v)
                                    <option value="{{$k}}" @if(isset($page['type']) && $page['type'] == $k ) selected="selected" @endif>{{$income[$k]}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-lg-4"style="width:30%;">
                        <div class="input-group">
                            <div class="input-group-addon">创建日期</div>
                            <input  type="text" name="rangedate" value="{{$page['rangedate'] or ''}}" class="form-control demo-input" id="rangedate" placeholder="请选择创建日期" />
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-default">搜索</button>
                        <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{ url('admin/finance/index') }}'">重置</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">

                @include('admin.partials.errors')
                @include('admin.partials.success')

                <div class="box-body">
                    <table id="tags-table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm">ID</th>
                            <th class="hidden-sm">用户名</th>
                            <th class="hidden-sm">手机号</th>
                            <th class="hidden-sm">头像</th>
                            <th class="hidden-md">注册时间</th>
                            <th class="hidden-md">类型</th>
                            <th class="hidden-md">备注描述</th>
                            <th class="hidden-md">消费金额</th>
                            <th data-sortable="false">状态</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($data as $k=>$v)
                            <tr>
                                <th data-sortable="false" class="hidden-sm">{{$v->id}}</th>
                                <th class="hidden-md">{{$v->username}}</th>
                                <th class="hidden-md">{{$v->mobile}}</th>
                                <th class="hidden-sm"><img src="{{$v->avatar or ''}}" style="width:100px;height:100px;"/></th>
                                <th class="hidden-sm">{{date('Y-m-d H:i:s',$v->create_time)}}</th>
                                <th class="hidden-md">
                                    @if($v->action == 0 )
                                        {{$incomes[$v->type]}}
                                    @elseif($v->action == 1 )
                                        {{$expend[$v->type]}}
                                    @endif
                                </th>
                                <th class="hidden-md">{{$v->note}}</th>
                                <th class="hidden-md">{{$v->number}}</th>
                                <th data-sortable="false">
                                    @if($v->action == 0 )
                                        <button class="layui-btn layui-btn-sm layui-btn-danger layui-btn-radius">收入</button>
                                    @elseif($v->action == 1 )
                                        <button class="layui-btn layui-btn-sm layui-btn-normal layui-btn-radius">支出</button>
                                    @endif
                                </th>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
                <div class="layui-inline">
                </div>

                <div class="row">
                    <div class="col-sm-5">
                        <div class="dataTables_info" id="tags-table_info" role="status" aria-live="polite">共 <span style='color:red;'> {{$data->total()}} </span> 条记录！</div>
                    </div>
                    <div class="col-sm-7"><div class="dataTables_paginate paging_simple_numbers" id="tags-table_paginate">
                            {{$data->render()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
@stop
<link rel="stylesheet" type="text/css" href="/layui/css/layui.css" /> <!-- lay上传插件样式 -->
<script src="/plugins/laydate/laydate.js"></script> <!-- 改成你的路径 -->
<script type="application/javascript">
    lay('#version').html('-v'+ laydate.v);

    //日期区间
    laydate.render({
        elem: '#rangedate' //指定元素
        ,theme: '#393D49'//指定样式
        ,range: true //日期区间设置
        ,format: 'yyyy-MM-dd' //显示格式
        ,range: '~'//两日期分隔符
    });
    //单个日期
    laydate.render({
        elem: '#birthday' //指定元素
        ,theme: '#393D49'//指定样式
        ,range: false //日期区间设置
        ,format: 'yyyy-MM-dd' //显示格式
        // ,range: '~'//两日期分隔符
    });
</script>
<!-- jQuery 2.2.0 -->
<script src="/js/jquery.min.js"></script>
<script type="text/javascript">
    $(function(){
        //点击事件 select使用change on
        $('#action').change(function(){
            var action = $('#action').val();
            //alert(action);
            $.ajax({
                url: "../finance/incomeType",
                type: "POST",
                dataType: "json",
                data: { "type":action, "_token":"{{ csrf_token() }}"},
                success: function(data){
                    if(data.code == 0){
                        var html = '';
                        var len = data.length;
                        for(var i = 1 ; i <= len;i++){
                            html = html + "<option value="+ i +">"+ data.data[i] +"</option>";
                        }
                        $('#type').html('').append(html);
                    }else{
                        $('#type').html('').append("<option value=''>"+ "请选择" +"</option>");
                    }
                }
            })
        });
    })
</script>
