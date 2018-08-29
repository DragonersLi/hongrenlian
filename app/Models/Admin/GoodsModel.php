<?php

namespace App\Models\Admin;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class GoodsModel extends Authenticatable
{
    use Notifiable;
    public $table = 'goods'; //定义表名
    protected $primaryKey = 'goods_id'; //定义主键
    public $timestamps = false; //是否使用时间戳


}
