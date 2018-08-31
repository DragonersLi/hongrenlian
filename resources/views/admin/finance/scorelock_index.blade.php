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
                <form action="{{url('admin/finance/ScorelockIndex')}}" method="post">
                    <div class="col-lg-2">
                        <input type="text"  name="keywords"  value="{{$page['keywords'] or ''}}"  class="form-control" placeholder="请输入用户名搜索" />
                    </div>
                    <div class="col-lg-2" style="width: 10%;" >
                        <select class="form-control"  id="status" name="status"  tabindex="-1" aria-hidden="true">
                            @if(Gate::forUser(auth('admin')->user())->check('admin.finance.ScorelockUp'))
                            <option value="1"  @if(isset($page['status']) && $page['status'] == 1) selected="selected" @endif >初审</option>
                            <option value="0">未审核</option>
                            @else
                            <option value="0">未审核</option>
                            <option value="1"  @if(isset($page['status']) && $page['status'] == 1) selected="selected" @endif >初审</option>
                            @endif
                            <option value="2"  @if(isset($page['status']) && $page['status'] == 2) selected="selected" @endif >终审</option>
                            <option value="-1"  @if(isset($page['status']) && $page['status'] == -1) selected="selected" @endif >初核未通过</option>
                            <option value="-2"  @if(isset($page['status']) && $page['status'] == -2) selected="selected" @endif >终审未通过</option>
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
                        <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{ url('admin/finance/ScorelockIndex') }}'">重置</button>
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
                            <th class="hidden-md">红人圈数量</th>
                            <th class="hidden-md">备注描述</th>
                            <th class="hidden-md">审核状态</th>
                            <th data-sortable="false">操作</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($data as $k=>$v)
                            <tr>
                                <th data-sortable="false" class="hidden-sm">{{$v->id}}</th>
                                <th class="hidden-md">{{$v->username}}</th>
                                <th class="hidden-md">{{$v->mobile}}</th>
                                <th class="hidden-sm"><img src="{{$v->avatar}}" style="width:100px;height:100px;"/></th>
                                <th class="hidden-sm">{{date('Y-m-d H:i:s',$v->create_time)}}</th>
                                <th class="hidden-md">{{$v->total_score}}</th>
                                <th class="hidden-md">{{$v->title}}</th>
                                <th class="hidden-md">
                                    @if($v->status == 0 )
                                        <button class="layui-btn layui-btn-sm layui-btn-radius">未审核</button>
                                    @elseif($v->status == 1 )
                                        <button class="layui-btn layui-btn-sm layui-btn-normal layui-btn-radius">初审通过</button>
                                    @elseif($v->status == 2 )
                                        <button class="layui-btn layui-btn-sm layui-btn-warm layui-btn-radius">终审通过</button>
                                    @elseif($v->status == -1 )
                                        <button class="layui-btn layui-btn-sm layui-btn-danger layui-btn-radius">初核未通过</button>
                                    @elseif($v->status == -2 )
                                        <button class="layui-btn layui-btn-sm layui-btn-danger layui-btn-radius">终审未通过</button>
                                    @endif
                                </th>
                                <th data-sortable="false">
                                    @if(Gate::forUser(auth('admin')->user())->check('admin.finance.ScorelockUpdate') && $v->status == 0 )
                                        <a href="{{url('admin/finance/ScorelockEdit',['id'=>$v->id])}}" class="btn btn-success">
                                            <i class="fa"></i> 去审核
                                        </a>
                                    @endif
                                    @if(Gate::forUser(auth('admin')->user())->check('admin.finance.ScorelockUp') && $v->status == 1 )
                                            <a href="{{url('admin/finance/ScorelockEdit',['id'=>$v->id])}}" class="layui-btn layui-btn-normal">
                                                <i class="fa"></i> 初审通过
                                            </a>
                                    @endif
                                    @if(Gate::forUser(auth('admin')->user())->check('admin.finance.ScorelockUp') && $v->status == 2 )
                                            <button class="btn" disabled>
                                                <i class="fa"></i> 终审通过
                                            </button>
                                    @endif
                                    @if(Gate::forUser(auth('admin')->user())->check('admin.finance.ScorelockUpdate') && $v->status == -1 )
                                            <button class="btn btn-danger" disabled>
                                                <i class="fa"></i> 初核未通过
                                            </button>
                                    @endif
                                    @if(Gate::forUser(auth('admin')->user())->check('admin.finance.ScorelockUp') && $v->status == -2 )
                                            <button class="btn btn-danger" disabled>
                                                <i class="fa"></i> 终审未通过
                                            </button>
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