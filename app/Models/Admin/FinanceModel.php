<?php

namespace App\Models\Admin;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class FinanceModel extends Authenticatable
{
    use Notifiable;
    protected $table = 'finance'; //定义表名
    protected $primaryKey = 'id'; //定义主键
    public $timestamps = false; //是否使用时间戳

    //添加消息
    public static function addFinance($user_id=0,$type='',$number='',$note=''){
        return self::insert([
            'user_id' => $user_id,
            'type' => $type,
            'number' => $number,
            'action' => $number >0 ? 0 : 1,
            'note' => ''
        ]);
    }


}
