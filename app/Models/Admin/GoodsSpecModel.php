<?php

namespace App\Models\Admin;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class GoodsSpecModel extends Authenticatable
{
    use Notifiable;
    protected $table = 'goods_spec'; //定义表名
    protected $primaryKey = 'spec_id'; //定义主键
    public $timestamps = false; //是否使用时间戳



}
