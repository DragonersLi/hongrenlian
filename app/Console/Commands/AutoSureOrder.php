<?php

namespace App\Console\Commands;

use Illuminate\Console\Command; 
use App\Models\Admin\OrderModel; 
use App\Http\Controllers\Api\V1\OrderController;
class AutoSureOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:AutoSureOrder';//命令名称

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动确认收货订单！执行命令：/usr/local/php71/bin/php artisan command:AutoSureOrder';//命令描述

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->orderModel = new OrderModel;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //功能代码  
		$orderController = new OrderController;
		$sure_time = time() - $orderController->getSureOrderTime();
		$where = " order_status=2 and pay_status=1 and  (pay_time <= {$sure_time} )"; //已支付，待收货
		
	    $order = $this->orderModel->whereRaw($where)->get();//自动完成订单
		if(!empty($order)){ 
			foreach($order as $k=>$v){
				$this->orderModel->where(['order_id'=>$v->order_id])->update(['order_status'=>3,'update_time'=>time()]); 
				file_put_contents("log.php","订单自动确认收货：".$v->order_id."\n\r",FILE_APPEND);
			}
		}
	  
		
    }
}
