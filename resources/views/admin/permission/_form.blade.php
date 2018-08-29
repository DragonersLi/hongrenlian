


<div class="form-group">
    <label for="tag" class="col-md-3 control-label">权限规则</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="name" id="tag" value="{{ $name }}" autofocus>
        <input type="hidden" class="form-control" name="cid" id="tag" value="{{ $cid }}" autofocus>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">权限名称</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="label" id="tag" value="{{ $label }}" autofocus>
    </div>
</div>
@if($cid == 0 )
{{--图标修改--}}
    <link rel="stylesheet" href="/plugins/bootstrap-iconpicker/icon-fonts/font-awesome-4.2.0/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="/plugins/bootstrap-iconpicker/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css"/>

    <div class="form-group">
    <label for="tag" class="col-md-3 control-label">图标</label>
    <div class="col-md-6">
        <!-- Button tag -->
        <button class="btn btn-default" name="icon" data-iconset="fontawesome" data-icon="{{ $icon?$icon:'fa-sliders' }}" role="iconpicker"></button>
    </div>

    </div>
@section('js')

    <script type="text/javascript" src="/plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/iconset/iconset-fontawesome-4.3.0.min.js"></script>
    <script type="text/javascript" src="/plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/bootstrap-iconpicker.js"></script>

@stop
@endif
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">排序(越小越靠前)</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="sort" id="tag" value="{{ $sort }}" autofocus>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">权限概述</label>
    <div class="col-md-6">
        <textarea name="description" class="form-control" rows="3">{{ $description }}</textarea>
    </div>
</div>



<div class="text-center m-t-10">
    <button type="submit" class="btn btn-success btn-md">
        <i class="fa fa-plus-circle"></i>提交
    </button>
    <button type="button"  class="btn btn-success btn-md" onclick="location.href='{{url('admin/permission/index')}}'">
        <i class="fa layui-icon"></i>返回上级
    </button>
</div>
</div>
<div class="form-group">
    <div class="col-md-7 col-md-offset-3">

    </div>
</div>