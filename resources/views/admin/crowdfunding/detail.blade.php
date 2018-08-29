@extends('admin.layouts.base')

@section('title','控制面板')

@section('pageHeader','控制面板')

@section('pageDesc','DashBoard')

@section('content')


    <div class="box box-info">
        <div class="box-header width-border">
            <h3 class="box-title"> </h3>
        </div>
        <div class="box-body">
            <div class="row">

                <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{ url('admin/crowdfunding/index') }}'">返回上级</button>


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
                            <th class="hidden-sm">用户ID</th>
                            <th class="hidden-sm">昵称</th> 
                            <th class="hidden-sm">手机号码</th>
                            <th class="hidden-md">支持份数</th>
                            <th class="hidden-md">支持红人圈</th>
                            <th class="hidden-md">创建时间</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($data as $k=>$v)
                            <tr>
                                <th data-sortable="false" class="hidden-sm">{{$v->id}}</th>
                                <th class="hidden-sm">{{$v->uid}}</th>
                                <th class="hidden-sm">{{$v->username}}</th>
                                <th class="hidden-sm">{{$v->mobile}}</th>
                                <th class="hidden-sm">{{$v->current_count}}</th>
                                <th class="hidden-sm">{{$v->current_score}}</th>
                                <th class="hidden-md">{{date('Y-m-d H:i:s',$v->create_time)}}</th>

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

