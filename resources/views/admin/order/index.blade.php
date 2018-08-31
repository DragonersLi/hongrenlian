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
                <form action="{{url('admin/order/index')}}" method="post">
                    <div class="col-lg-2">
                        <input type="text"  name="keywords"  value="{{$page['keywords'] or ''}}"  class="form-control" placeholder="请输入关键词搜索" />
                    </div>
                    <div class="col-lg-2" style="width: 10%;" >
                        <select class="form-control"  id="order_status" name="order_status"  tabindex="-1" aria-hidden="true">
                            <option value="-1" @if(!isset($page['order_status']) || $page['order_status'] == -1) selected="selected" @endif>订单状态</option>
                            @foreach($orderStatus as $k=>$v)
                            <option value="{{$k}}"  @if(isset($page['order_status']) && $page['order_status'] == $k) selected="selected" @endif >{{$v}}</option>
                           @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-default">搜索</button>
                        <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{ url('admin/order/index') }}'">重置</button>

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
                            <th class="hidden-sm">订单编号</th>
                            <th class="hidden-sm">用户名</th>
                            <th class="hidden-sm">订单地址</th>
                            <th class="hidden-sm">订单状态</th>
                            <th class="hidden-sm">订单备注</th>
                            <th class="hidden-sm">下单时间</th>
                            <th data-sortable="false">操作</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($data as $k=>$v)
                            <tr>
                                <th data-sortable="false" class="hidden-sm">{{$k+1}}</th>
                                <th class="hidden-sm">{{$v->order_id}}</th>
                                <th class="hidden-sm">{{$v->username}}</th>
                                <th class="hidden-sm">{{$v->receive_address}}</th>
                                <th class="hidden-sm">
                                    {{$orderStatus[$v->order_status]}}
                                </th>
                                <th class="hidden-sm">{{$v->order_desc}}</th>
                                <th class="hidden-sm">{{date('Y-m-d H:i:s',$v->create_time)}}</th>
                                <th data-sortable="false">
                                    @if(Gate::forUser(auth('admin')->user())->check('admin.order.info'))
                                        <a href="{{url('admin/order/info',['order_id'=>$v->order_id])}}" class="btn btn-success">
                                            <i class="fa"></i> 查看订单详情
                                        </a>
                                    @endif
                                    @if(Gate::forUser(auth('admin')->user())->check('admin.order.changeStatus'))
                                        @if($v->order_status == 1)
                                            <a href="{{url('admin/order/going',['order_id'=>$v->order_id,'order_status'=>$v->order_status])}}" class="btn btn-success">
                                                <i class="fa"></i>发货
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