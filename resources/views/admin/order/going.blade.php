@extends('admin.layouts.base')

@section('title','控制面板')

@section('pageHeader','控制面板')

@section('pageDesc','DashBoard')

@section('content')
    <div class="box box-info col-xs-12">
        <div class="box-header width-border" style="float:left">
            <h3 class="box-title">发货</h3>
        </div>
        <div class="box-body" style="float:right">
            <div class="row">
                <a href="{{url('admin/order/index')}}" class="btn btn-success">
                    <i class="fa"></i>返回订单列表
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <form action="{{url('admin/order/changego')}}" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                                <label class="col-sm-3 control-label">快递公司</label>
                                <div class="col-sm-9">
                                    <input class="form-control" style="width: 156px;" type="text" name="wuliu_name" id="title"  value="" placeholder="快递公司">
                                    <input type="hidden" value="{{$order_id}}" name="order_id">
                                    <input type="hidden" value="{{$user_id}}" name="user_id">
                                    <input type="hidden" value="{{$total_score}}" name="total_score">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">物流单号</label>
                                <div class="col-sm-9">
                                    <input class="form-control" style="width: 156px;" type="text" name="wuliu_code" id="alias"  value="" placeholder="物流单号">
                                </div>
                            </div>
                        <button type="submit" class="layui-btn layui-btn-normal" id="testList">订单发货</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop


<script src="/plugins/jQuery/jQuery-2.2.0.min.js"></script>
<script src="/plugins/laydate/laydate.js"></script> <!-- lay日期插件 -->
<script type="text/javascript" src="/layui/layui.js"></script> <!-- lay上传插件 -->
<link rel="stylesheet" type="text/css" href="/layui/css/layui.css" /> <!-- lay上传插件样式 -->