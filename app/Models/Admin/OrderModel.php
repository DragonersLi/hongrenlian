<?php

namespace App\Models\Admin;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class OrderModel extends Authenticatable
{
    use Notifiable;
    protected $table = 'orders'; //定义表名
    protected $primaryKey = 'id'; //定义主键
    public $timestamps = false; //是否使用时间戳

    public static function getOrderId(){
        $order_id = date("ymdHis").mt_rand(10000,99999);
        $res = self::where(['order_id'=>$order_id])->first();
        return empty($res) ? $order_id : self::getOrderId();
    }

    public static function getOneOrderId($order_id){
        $res = self::where(['order_id'=>$order_id])->first();
        return $res;
    }

    //发货  修改订单
    public static function updateOneOrder($order_id,$data){
        $res = self::where(['order_id'=> $order_id])->update($data);
        return $res;
    }
}
