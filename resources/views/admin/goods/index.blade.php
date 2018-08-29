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
                <form action="{{url('admin/goods/index')}}" method="post">
                    <div class="col-lg-2">
                        <input type="text"  name="keywords"  value="{{$page['keywords'] or ''}}"  class="form-control" placeholder="请输入关键词搜索" />
                    </div>
                    <div class="col-lg-2" style="width: 10%;" >
                        <select class="form-control"  id="sj" name="sj"  tabindex="-1" aria-hidden="true">
                            <option value="-1" @if(!isset($page['sj']) || $page['sj'] == -1) selected="selected" @endif>状态</option>
                            <option value="0"  @if(isset($page['sj']) && $page['sj'] == 0) selected="selected" @endif >下架</option>
                            <option value="1"  @if(isset($page['sj']) && $page['sj'] == 1) selected="selected" @endif >上架</option>
                        </select>
                    </div>

                    <div class="col-lg-2">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-default">搜索</button>
                        <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{ url('admin/goods/index') }}'">重置</button>
                        @if(Gate::forUser(auth('admin')->user())->check('admin.goods.add'))
                            <button type="button" style="margin-left:5px;" class="btn btn-default" onclick="location.href='{{url('admin/goods/add')}}'"><i class="fa fa-plus-circle"></i>添加商品</button>
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
                            <th class="hidden-sm">商品编号</th>
                            <th class="hidden-sm">商品名称</th>
                            {{--<th class="hidden-sm">商品分类</th>--}}
                            <th class="hidden-sm">商品主图</th>
                            <th class="hidden-sm">商品价格</th>
                            <th class="hidden-sm">商品库存</th>
                            <th class="hidden-sm">上架状态</th>
                            <th class="hidden-md">商品状态</th>
                            <th data-sortable="false">操作</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($data as $k=>$v)
                            <tr>
                                <th data-sortable="false" class="hidden-sm">{{$v->goods_id}}</th>
                                <th class="hidden-sm">{{$v->goods_sn}}</th>
                                <th class="hidden-sm">{{$v->title}}</th>
                                {{--<th class="hidden-sm">{{$v->goods_cat}}</th>--}}
                                <th class="hidden-sm"><img src="{{$v->indexpic}}" style="width:100px;height:100px;"/></th>
                                <th class="hidden-sm">{{$v->score}}</th>
                                <th class="hidden-sm">{{$v->store}}</th>
                                <th class="hidden-sm">{{$v->sj ? '上架':'下架'}}</th>
                                <th class="hidden-sm">{{$v->status?'正常':'冻结'}}</th>
                                <th data-sortable="false">
                                    @if(Gate::forUser(auth('admin')->user())->check('admin.goods.edit'))
                                        <a href="{{url('admin/goods/edit',['goods_id'=>$v->goods_id])}}" class="btn btn-success">
                                            <i class="fa"></i> 编辑
                                        </a>
                                    @endif
                                    @if(Gate::forUser(auth('admin')->user())->check('admin.goods.changeStatus'))
                                        @if($v->status)
                                            <a href="{{url('admin/goods/changeStatus',['goods_id'=>$v->goods_id,'status'=>$v->status])}}" class="btn btn-success">
                                                <i class="fa"></i>冻结
                                            </a>
                                        @else
                                            <a href="{{url('admin/goods/changeStatus',['goods_id'=>$v->goods_id,'status'=>$v->status])}}" class="btn btn-success">
                                                <i class="fa"></i>正常
                                            </a>
                                        @endif
                                    @endif
                                    @if(Gate::forUser(auth('admin')->user())->check('admin.goods.sjStatus'))
                                        @if($v->sj)
                                            <a href="{{url('admin/goods/sjStatus',['goods_id'=>$v->goods_id,'sj'=>$v->sj])}}" class="btn btn-success">
                                                <i class="fa"></i>下架
                                            </a>
                                        @else
                                            <a href="{{url('admin/goods/sjStatus',['goods_id'=>$v->goods_id,'sj'=>$v->sj])}}" class="btn btn-success">
                                                <i class="fa"></i>上架
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