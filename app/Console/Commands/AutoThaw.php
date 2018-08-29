<?php

namespace App\Console\Commands;

use Illuminate\Console\Command; 
use App\Models\Admin\UsersModel; 
use App\Models\Admin\FreezeModel; 
use App\Models\Admin\FinanceModel; 
 
class AutoThaw extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:AutoThaw';//命令名称

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动解冻红人圈！执行命令：/usr/local/php71/bin/php artisan command:AutoThaw';//命令描述

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->usersModel = new UsersModel;
        $this->freezeModel = new FreezeModel;
        $this->financeModel = new FinanceModel;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //功能代码    
		$time = time();
	    $freezes = $this->freezeModel->whereRaw("status = 0 and thaw_time < {$time} ")->get();//查询需要解冻的记录
		if(!empty($freezes)){ 
			foreach($freezes as $k=>$v){
				$this->freezeModel->where(['id'=>$v->id])->update(['status'=>1,'update_time'=>$time]); //更新状态为已解冻
				$this->usersModel->where(['id'=>$v->user_id])->update(['freeze_score'=>\DB::raw("freeze_score - {$v->number}"),'score'=>\DB::raw("score + {$v->number}"),'update_time'=>$time]);//更新用户红人圈：冻结-，可用+
				// 冻结类型 映射 收入类型
				$types = [
					'1' => 3,//点赞收入
					'2' => 10,//星探冻结退回
					'3' => 4, //粉丝投票收入
                   '4' => 14 //购买收入
				];
                /*
                $this->financeModel->insert([
                'user_id' => $v->user_id,
                'type' => $types[$v->type],
                'number' => $v->number,
                'action' => 0,
                'note' => '收入解冻',
                'create_time'=> $time
            ]);*/
			file_put_contents("log.php","自动解冻红人圈：解冻id：".$v->id.";用户id：".$v->user_id."\n\r",FILE_APPEND);	
			}
		}
	  
		
    }
 
}
