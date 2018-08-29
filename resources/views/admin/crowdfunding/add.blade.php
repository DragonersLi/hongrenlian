@extends('admin.layouts.base')

@section('title','控制面板')

@section('pageHeader','控制面板')

@section('pageDesc','DashBoard')

@section('content')
    <div class="main animsition">
        <div class="container-fluid">

            <div class="row">
                <div class="">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">添加众筹</h3>
                        </div>
                        <div class="panel-body">

                            @include('admin.partials.errors')
                            @include('admin.partials.success')

                            <form class="form-horizontal"   method="POST" action="{{url('admin/crowdfunding/add')}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                @include('admin.crowdfunding._form')


                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop