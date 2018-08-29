<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Controllers\Api\Wechat\ErrMsg as Msg;
class SyncController extends ApiBaseController
{
    public $sync_user = 1;//0：关闭；1：开启
    public function __construct()
    {
        header("content-type:text/html;charset=utf8"); //设置编码
        date_default_timezone_set('Asia/Shanghai');//设置时区
        ignore_user_abort(true); //无论客户端是否关闭浏览器都执行代码
        set_time_limit(0); //解除PHP脚本时间30s限制
        parent::__construct();
    }

    //
    public function index(Request $request)
    {
        if($close){//如果没关闭则执行代码
            file_put_contents("hello.txt",date('Y-m-d H:i:s')."\n\r",FILE_APPEND);//执行的动作
            sleep(3);//休息 3 秒
            echo'<meta http-equiv=\'refresh\' content="2; url=\''.$this->base_url."/api/v1/sync/user".'\'" /> '; //再休息 2 秒， 实际休息时间为 5 秒
        }
    }
    //同步用户
    public function test(Request $request)
    {
        if($this->sync_user){//如果没关闭则执行代码
            file_put_contents("hello.txt",date('Y-m-d H:i:s')."\n\r",FILE_APPEND);//执行的动作
            sleep(1);//休息 3 秒
            echo'<meta http-equiv=\'refresh\' content="2; url=\''.$this->base_url."/index.php/api/v1/sync/user".'\'" /> '; //再休息 2 秒， 实际休息时间为 5 秒
        }
    }



}
