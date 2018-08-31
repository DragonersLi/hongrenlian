@extends('admin.layouts.base')

@section('title','控制面板')

@section('pageHeader','控制面板')

@section('pageDesc','DashBoard')

@section('content')
    <div class="box box-info col-xs-12">
           <div class="box-header width-border" style="float:left">
                            <h3 class="box-title">查看订单详情</h3>
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
                            <table id="tags-table" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="hidden-sm">订单编号</th>
                                    <th class="hidden-sm">用户名称</th>
                                    <th class="hidden-sm">订单地址</th>
                                    <th class="hidden-sm">支付状态</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="hidden-sm">{{$data[0]->order_id}}</th>
                                        <th class="hidden-sm">{{$data[0]->username}}</th>
                                        <th class="hidden-sm">{{$data[0]->receive_address}}</th>
                                         <th class="hidden-sm">@if($data[0]->pay_status==0) 未支付 @else 已支付 @endif</th>
                                    </tr>
                                </tbody>
                            </table>
                            <table id="tags-table" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="hidden-sm">订单状态</th>
                                    <th class="hidden-sm">订单备注</th>
                                    <th class="hidden-sm">下单时间</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="hidden-sm">
                                            {{orderstatus($data[0]->order_status)}}
                                        </th>
                                        <th class="hidden-sm">{{$data[0]->order_desc}}</th>
                                        <th class="hidden-sm">{{date('Y-m-d H:i:s',$data[0]->create_time)}}</th>
                                    </tr>
                                </tbody>
                            </table>
                             <table id="tags-table" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="hidden-sm">物流名称</th>
                                    <th class="hidden-sm">物流单号 </th>
                                    <th class="hidden-sm">收货人手机号</th>

                                </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <th class="hidden-sm">{{$data[0]->wuliu_name}}</th>
                                        <th class="hidden-sm">{{$data[0]->wuliu_code}}</th>
                                        <th class="hidden-sm">{{$data[0]->receive_phone}}</th>
                                    </tr>
                                </tbody>

                            </table>
                            <table id="tags-table" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="hidden-sm">商品名称</th>
                                    <th class="hidden-sm">图片 </th>
                                    <th class="hidden-sm">商品数量</th>
                                    <th class="hidden-sm">订单价值红人圈</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($data as $k=>$v)
                                    <tr>
                                        <th class="hidden-sm">{{$v->title}}</th>
                                        <th class="hidden-sm"><img src="{{url($v->indexpic)}}" width="50" height="50" alt=""></th>
                                        <th class="hidden-sm">{{$v->number}}</th>
                                        <th class="hidden-sm">{{$v->score}}</th>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>

                        </div>
                    </div>
            </div>
    </div>
@stop