<?php

namespace App\Models\Admin;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UsersModel extends Authenticatable
{
    use Notifiable;
    protected $table = 'users'; //定义表名
    protected $primaryKey = 'id'; //定义主键
    public $timestamps = false; //是否使用时间戳

    public function fans(){
       // return $this->from($this->table)->select('u.*')->join('users_follow as f','f.user_id','users.id')->get();
       // return $this->hasOne(FollowModel::class);

       return $this->hasMany(FollowModel::class,'user_id','id')->select('avatar','username');
    }

}
