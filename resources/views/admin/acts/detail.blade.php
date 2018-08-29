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
                <form action="{{url('admin/acts/detail',['act_id'=>$act_id])}}" method="post">

                    <div class="form-group col-lg-4">

                        @if(Gate::forUser(auth('admin')->user())->check('admin.acts.voteAdd'))
                            <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{url('admin/acts/detailAdd',['act_id'=>$act_id])}}'"><i class="fa fa-plus-circle"></i>添加候选人</button>
                        @endif
                        <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{ url('admin/acts/index') }}'">返回上级</button>

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
                            <th class="hidden-sm">昵称</th>
                            <th class="hidden-sm">展示昵称</th>
                            {{--<th class="hidden-sm">头像</th>--}}
                            {{--<th class="hidden-md">手机号</th>--}}
                            <th class="hidden-md">风采展示</th>
                            <th class="hidden-md">投票宣言</th>
                            <th class="hidden-md">已投人数</th>
                            <th class="hidden-md">已投红人圈</th>
                            {{--<th class="hidden-md">状态</th>--}}
                            <th data-sortable="false">操作</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($data as $k=>$v)
                            <tr>
                                <th data-sortable="false" class="hidden-sm">{{$v->id}}</th>
                                <th class="hidden-sm">{{$v->username}}</th>
                                <th class="hidden-sm">{{$v->user_name}}</th>
                                {{--<th class="hidden-sm"><img src="{{$v->avatar}}" style="width:100px;height:100px;"></th>--}}
                                {{--<th class="hidden-md">{{$v->mobile}}</th>--}}
                                <th class="hidden-md"><img src="{{$v->user_img}}" style="width:100px;height:100px;"></th>
                                <th class="hidden-md">{{$v->user_desc}}</th>
                                <th class="hidden-md">{{$v->count_user}}</th>
                                <th class="hidden-md">{{$v->count_score}}</th>
                                <th class="hidden-md">{{$v->status ? "正常":"冻结"}}</th>

                                <th data-sortable="false">
                                    @if(Gate::forUser(auth('admin')->user())->check('admin.acts.detailEdit'))
                                        <a href="{{url('admin/acts/detailEdit',['act_id'=>$act_id,'detail_id'=>$v->id])}}" class="btn btn-success">
                                            <i class="fa"></i>编辑
                                        </a>
                                    @endif
                                    @if(Gate::forUser(auth('admin')->user())->check('admin.acts.records'))
                                        <a href="{{url('admin/acts/records',['act_id'=>$act_id,'vote_id'=>$v->id])}}" class="btn btn-success">
                                            <i class="fa"></i>查看
                                        </a>
                                    @endif
                                    {{--@if(Gate::forUser(auth('admin')->user())->check('admin.acts.changeVoteStatus'))--}}
                                        {{--@if($v->status)--}}
                                            {{--<a href="{{url('admin/acts/changeVoteStatus',['vote_id'=>$v->id,'status'=>$v->status])}}" class="btn btn-success">--}}
                                                {{--<i class="fa"></i>冻结--}}
                                            {{--</a>--}}
                                        {{--@else--}}
                                            {{--<a href="{{url('admin/acts/changeVoteStatus',['vote_id'=>$v->id,'status'=>$v->status])}}" class="btn btn-success">--}}
                                                {{--<i class="fa"></i>解冻--}}
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

    //单个日期
    laydate.render({
        elem: '#birthday' //指定元素
        ,theme: '#393D49'//指定样式
        ,range: false //日期区间设置
        ,format: 'yyyy-MM-dd' //显示格式
        // ,range: '~'//两日期分隔符
    });

</script>