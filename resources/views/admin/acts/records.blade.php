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
                <form action="{{url('admin/acts/records',['act_id'=>$act_id,'vote_id'=>$vote_id])}}" method="post">
                    <div class="col-lg-2">
                        <input type="text"  name="keywords"  value="{{$page['keywords'] or ''}}"  class="form-control" placeholder="请输入关键词搜索" />
                    </div>

                    <div class="form-group col-lg-4">
                        <input type="hidden" name="act_id" value="{{ $act_id }}">
                        <input type="hidden" name="vote_id" value="{{ $vote_id }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-default">搜索</button>
                        <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{ url('admin/acts/records',['act_id'=>$act_id,'vote_id'=>$vote_id]) }}'">重置</button>
                        <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{ url('admin/acts/detail',['act_id'=>$act_id,'vote_id'=>$vote_id]) }}'">返回上级</button>

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
                            <th class="hidden-sm">用户ID</th>
                            <th class="hidden-sm">昵称</th> 
                            <th class="hidden-sm">手机号码</th> 
                            <th class="hidden-md">投票数</th>
                            <th class="hidden-md">投票时间</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($data as $k=>$v)
                            <tr>
                                <th data-sortable="false" class="hidden-sm">{{$v->id}}</th>
                                <th class="hidden-sm">{{$v->user_id}}</th>
                                <th class="hidden-sm">{{$v->username}}</th>  
                                <th class="hidden-sm">{{$v->mobile}}</th> 
                                <th class="hidden-md">{{$v->voted_count}}</th>
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

