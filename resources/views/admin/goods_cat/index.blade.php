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
                <form action="{{url('admin/goods_cat/index')}}" method="post">
                    <div class="col-lg-2">
                        <input type="text"  name="keywords"  value="{{$page['keywords'] or ''}}"  class="form-control" placeholder="请输入关键词搜索" />
                    </div>
                    {{--<div class="col-lg-2" style="width: 10%;" >--}}
                        {{--<select class="form-control"  id="sex" name="sex"  tabindex="-1" aria-hidden="true">--}}
                            {{--<option value="-1" @if(!isset($page['sex']) || $page['sex'] == -1) selected="selected" @endif>适用性别</option>--}}
                            {{--<option value="0"  @if(isset($page['sex']) && $page['sex'] == 0) selected="selected" @endif >通用</option>--}}
                            {{--<option value="1"  @if(isset($page['sex']) && $page['sex'] == 1) selected="selected" @endif >男性</option>--}}
                            {{--<option value="2"  @if(isset($page['sex']) && $page['sex'] == 2) selected="selected" @endif >女性</option>--}}
                        {{--</select>--}}
                    {{--</div>--}}
                    <div class="col-lg-2" style="width: 10%;" >
                        <select class="form-control"  id="is_hot" name="is_hot"  tabindex="-1" aria-hidden="true">
                            <option value="-1" @if(!isset($page['is_hot']) || $page['is_hot'] == -1) selected="selected" @endif>是否热门</option>
                            <option value="0"  @if(isset($page['is_hot']) && $page['is_hot'] == 0) selected="selected" @endif >否</option>
                            <option value="1"  @if(isset($page['is_hot']) && $page['is_hot'] == 1) selected="selected" @endif >是</option>
                        </select>
                    </div>

                    {{--<div class="form-group col-lg-4" style="width:20%;">--}}
                        {{--<div class="input-group">--}}
                            {{--<div class="input-group-addon">出生日期</div>--}}
                            {{--<input  type="text" name="birthday" value="{{$page['birthday'] or ''}}" class="form-control demo-input" id="birthday" placeholder="请选择出生日期" />--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="form-group col-lg-4"style="width:30%;">--}}
                        {{--<div class="input-group">--}}
                            {{--<div class="input-group-addon">注册日期</div>--}}
                            {{--<input  type="text" name="rangedate" value="{{$page['rangedate'] or ''}}" class="form-control demo-input" id="rangedate" placeholder="请选择注册日期" />--}}
                            {{--<input type="text" class="form-control datepicker" name="startTime" id="startTime" value=" " >--}}
                            {{--<div class="input-group-addon">至</div>--}}
                            {{--<input type="text" class="form-control datepicker" name="endTime" id="endTime" value=" ">--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    <div class="col-lg-2">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-default">搜索</button>
                        <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{ url('admin/goods_cat/index') }}'">重置</button>
                        @if(Gate::forUser(auth('admin')->user())->check('admin.goods_cat.add'))
                            <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{url('admin/goods_cat/add')}}'"><i class="fa fa-plus-circle"></i>添加分类</button>
                        @endif
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
                            <th class="hidden-sm">分类名称</th>
                            <th class="hidden-sm">分类描述</th>
                            <th class="hidden-sm">分类图标</th>
                            {{--<th class="hidden-sm">适用性别</th>--}}
                            <th class="hidden-sm">是否热门</th>
                            <th class="hidden-md">状态</th>
                            <th data-sortable="false">操作</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($data as $k=>$v)
                        <tr>
                            <th data-sortable="false" class="hidden-sm">{{$v->id}}</th>
                            <th class="hidden-sm">{{$v->title}}</th>
                            <th class="hidden-sm">{{$v->desc}}</th>
                            <th class="hidden-sm"><img src="{{$v->icon}}" style="width:100px;height:100px;"/></th>
                            {{--<th class="hidden-sm">@if($v->sex == 2) 女性@elseif($v->sex ==1)男性@else通用@endif</th>--}}
                            <th class="hidden-sm">@if($v->is_hot)是@else否@endif</th>
                            <th class="hidden-md">{{$v->disabled ? "禁用":"正常"}}</th>
                            <th data-sortable="false">
                                @if(Gate::forUser(auth('admin')->user())->check('admin.goods_cat.edit'))
                                    <a href="{{url('admin/goods_cat/edit',['id'=>$v->id])}}" class="btn btn-success">
                                        <i class="fa"></i> 编辑
                                    </a>
                                @endif
                                    @if(Gate::forUser(auth('admin')->user())->check('admin.goods_cat.changeStatus'))
                                        @if($v->disabled)
                                        <a href="{{url('admin/goods_cat/changeStatus',['id'=>$v->id,'status'=>$v->disabled])}}" class="btn btn-success">
                                            <i class="fa"></i>正常
                                        </a>
                                            @else
                                            <a href="{{url('admin/goods_cat/changeStatus',['id'=>$v->id,'status'=>$v->disabled])}}" class="btn btn-success">
                                                <i class="fa"></i>禁用
                                            </a>
                                        @endif
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

<script src="/plugins/laydate/laydate.js"></script> <!-- 改成你的路径 -->
<script>
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