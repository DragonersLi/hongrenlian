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
                <form action="{{url('admin/acts/index')}}" method="post">
                    <div class="col-lg-2">
                        <input type="text"  name="keywords"  value="{{$page['keywords'] or ''}}"  class="form-control" placeholder="请输入关键词搜索" />
                    </div>
                    <div class="col-lg-2" style="width: 10%;" >
                        <select class="form-control"  id="is_template" name="is_template"  tabindex="-1" aria-hidden="true">
                            <option value="-1">模板</option>
                            <option value="1"  @if(isset($page['is_template']) && $page['is_template'] == 1) selected="selected" @endif >是</option>
                            <option value="0"  @if(isset($page['is_template']) && $page['is_template'] == 0) selected="selected" @endif >否</option>
                        </select>
                    </div>
                    <div class="col-lg-2" style="width: 10%;" >
                        <select class="form-control"  id="status_type" name="status_type"  tabindex="-1" aria-hidden="true">
                            <option value="-1">请选择</option>
                            @foreach($status_type as $k=>$v)
                                <option value="{{$k}}"  @if(isset($page['status_type']) && $page['status_type'] ==$k) selected="selected" @endif >{{$v}}</option>
                            @endforeach
                        </select>
                    </div>



                    <div class="col-lg-2">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-default">搜索</button>
                        <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{ url('admin/acts/index') }}'">重置</button>
                        @if(Gate::forUser(auth('admin')->user())->check('admin.acts.add'))
                            <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{url('admin/acts/add')}}'"><i class="fa fa-plus-circle"></i>添加活动</button>
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
                            <th class="hidden-sm">活动名称</th>
                            <th class="hidden-sm">活动主图</th>
                            <th class="hidden-md">活动时间</th>
                            <th class="hidden-md">被投票用户奖励标题</th>
                            <th class="hidden-md">投票用户奖励标题</th>
                            <th class="hidden-md">是否模板</th>
                            <th class="hidden-md">排序</th>
                            <th class="hidden-md">状态</th>
                            <th data-sortable="false">操作</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($data as $k=>$v)
                            <tr>
                                <th data-sortable="false" class="hidden-sm">{{$v->id}}</th>
                                <th class="hidden-sm">{{$v->title}}</th>
                                <th class="hidden-sm"><img src="{{$v->index_img}}" style="width:100px;height:100px;"></th>
                                <th class="hidden-md">{{$v->start_time}}~{{$v->end_time}}</th>
                                <th class="hidden-md">{{$v->reward_title}}</th>
                                <th class="hidden-md">{{$v->voting_title}}</th>
                                <th class="hidden-md">{{$v->is_template ? '是':'否'}}</th>
                                <th class="hidden-md">{{$v->sort}}</th>
                                <th class="hidden-md">
                                    @if($v->status == 9){{$status_type[4]}}
                                    @elseif($v->start_time > date('Y-m-d H:i:s')){{$status_type[1]}}
                                    @elseif($v->end_time > date('Y-m-d H:i:s') && date('Y-m-d H:i:s') > $v->start_time){{$status_type[2]}}
                                    @elseif( date('Y-m-d H:i:s') > $v->end_time){{$status_type[3]}}
                                    @endif
                                 </th>
                                 <th data-sortable="false">

                                    <a href="{{url('admin/acts/detail',['act_id'=>$v->id])}}" class="btn btn-success">
                                        <i class="fa"></i>查看
                                    </a>
                                    @if(Gate::forUser(auth('admin')->user())->check('admin.acts.edit'))
                                        <a href="{{url('admin/acts/edit',['id'=>$v->id])}}" class="btn btn-success">
                                            <i class="fa"></i>编辑
                                        </a>
                                    @endif
                                    {{--@if(Gate::forUser(auth('admin')->user())->check('admin.acts.changeActStatus'))--}}
                                        {{--@if($v->status != 9)--}}
                                            {{--<a href="{{url('admin/acts/changeActStatus',['id'=>$v->id,'status'=>$v->status])}}" class="btn btn-success">--}}
                                                {{--<i class="fa"></i>关闭--}}
                                            {{--</a>--}}
                                        {{--@else--}}
                                            {{--<a href="{{url('admin/acts/changeActStatus',['id'=>$v->id,'status'=>$v->status])}}" class="btn btn-success">--}}
                                                {{--<i class="fa"></i>开启--}}
                                            {{--</a>--}}
                                        {{--@endif--}}
                                    {{--@endif--}}
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
        //,format: 'yyyy-MM-dd H:i:s' //显示格式
        ,range: '~'//两日期分隔符
        ,type: 'datetime' //日期时间
    });

</script>