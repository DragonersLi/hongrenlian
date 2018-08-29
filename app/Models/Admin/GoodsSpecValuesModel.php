<?php

namespace App\Models\Admin;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class GoodsSpecValuesModel extends Authenticatable
{
    use Notifiable;
    protected $table = 'goods_spec_values'; //定义表名
    protected $primaryKey = 'spec_value_id'; //定义主键
    public $timestamps = false; //是否使用时间戳



}
