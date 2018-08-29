<?php

namespace App\Models\Admin;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SignModel extends Authenticatable
{
    use Notifiable;
    protected $table = 'users_sign'; //定义表名
    protected $primaryKey = 'id'; //定义主键
    public $timestamps = false; //是否使用时间戳



}
