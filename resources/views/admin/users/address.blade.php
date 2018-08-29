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


                        <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{ url('admin/users/index') }}'">返回上级</button>




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
                            <th class="hidden-sm">收货人姓名</th>
                            <th class="hidden-sm">收货人电话</th>
                            <th class="hidden-sm">省</th>
                            <th class="hidden-sm">市</th>
                            <th class="hidden-sm">区</th>
                            <th class="hidden-sm">详细地址</th>
                            <th class="hidden-sm">是否默认</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($data as $k=>$v)
                            <tr>
                                <th data-sortable="false" class="hidden-sm">{{$v->id}}</th>
                                <th class="hidden-sm">{{$v->username}}</th>
                                <th class="hidden-sm">{{$v->mobile}}</th>
                                <th class="hidden-sm">{{$v->receive_name}}</th>
                                <th class="hidden-sm">{{$v->receive_phone}}</th>
                                <th class="hidden-sm">{{$v->province}}</th>
                                <th class="hidden-sm">{{$v->city}}</th>
                                <th class="hidden-sm">{{$v->area}}</th>
                                <th class="hidden-sm">{{$v->address}}</th>
                                <th class="hidden-sm">{{$v->is_default ? '是':'否'}}</th>
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