<?php

namespace App\Models\Admin;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class MessageModel extends Authenticatable
{
    use Notifiable;
    protected $table = 'message'; //定义表名
    protected $primaryKey = 'id'; //定义主键
    public $timestamps = false; //是否使用时间戳

    //添加消息
    public static function addMessage($user_id=0,$title='',$content=''){
            $data['user_id'] = $user_id;
            $data['title'] = $title;
            $data['content'] = $content;
            $data['create_time'] = time();
            return self::insert($data);
    }

}
