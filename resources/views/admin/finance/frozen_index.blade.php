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
                <form action="{{url('admin/finance/frozenIndex')}}" method="post">
                    <div class="col-lg-2">
                        <input type="text"  name="keywords"  value="{{$page['keywords'] or ''}}"  class="form-control" placeholder="请输入用户名搜索" />
                    </div>
                    <div class="col-lg-2" style="width: 10%;" >
                        <select class="form-control"  id="status" name="status"  >
                            <option value="">请选择</option>
                            <option value="0" @if(isset($page['status']) && $page['status'] == 0) selected="selected" @endif  >冻结中</option>
                            <option value="1" @if(isset($page['status']) && $page['status'] == 1) selected="selected" @endif  >已解冻</option>
                        </select>
                    </div>
                    <div class="col-lg-2" style="width: 10%;" >
                        <select class="form-control"  id="type" name="type" >
                            <option value="">请选择</option>
                            <option value="1" @if(isset($page['type']) && $page['type'] == 1) selected="selected" @endif  >点赞</option>
                            <option value="2" @if(isset($page['type']) && $page['type'] == 2) selected="selected" @endif  >推荐红人</option>
                            <option value="3" @if(isset($page['type']) && $page['type'] == 3) selected="selected" @endif  >粉丝投票</option>
                            <option value="4" @if(isset($page['type']) && $page['type'] == 4) selected="selected" @endif  >购买</option>
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
                        <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{ url('admin/finance/FrozenIndex') }}'">重置</button>
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
                            <th class="hidden-md">状态</th>
                            <th class="hidden-md">消费金额</th>
                            <th class="hidden-md">解冻时间</th>
                            <th data-sortable="false">操作</th>
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
                                    @if($v->type)
                                        {{$type[$v->type]}}
                                    @endif
                                </th>
                                <th class="hidden-md">
                                    @if($v->status == 0 )
                                        <button class="layui-btn layui-btn-sm layui-btn-danger layui-btn-radius">冻结中</button>
                                    @elseif($v->status == 1 )
                                        <button class="layui-btn layui-btn-sm layui-btn-normal layui-btn-radius">已解冻</button>
                                    @endif
                                </th>
                                <th class="hidden-md">{{$v->number}}</th>
                                <th class="hidden-md">{{date('Y-m-d H:i:s', $v->thaw_time )}}</th>
                                <th data-sortable="false">
                                    @if( $v->thaw_time < time() )
                                        @if(Gate::forUser(auth('admin')->user())->check('admin.finance.frozenIndex') && $v->status == 0 )
                                            <a href="{{url('admin/finance/frozenUp',['id'=>$v->id, 'mobile'=>$v->mobile, 'user_id'=>$v->user_id, 'number'=>$v->number])}}" class="btn btn-success">
                                                <i class="fa"></i> 去解冻
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
<link rel="stylesheet" type="text/css" href="/layui/css/layui.css" /> <!-- lay上传插件样式 -->
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
