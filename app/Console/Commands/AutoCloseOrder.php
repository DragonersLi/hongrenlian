<?php

namespace App\Console\Commands;

use Illuminate\Console\Command; 
use App\Models\Admin\OrderModel; 
use App\Http\Controllers\Api\V1\OrderController;
class AutoCloseOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:AutoCloseOrder';//命令名称

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动关闭超时订单！执行命令：/usr/local/php71/bin/php artisan command:AutoCloseOrder';//命令描述

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
		$close_time = time() - $orderController->getCloseOrderTime();
		$where = " order_status=0 and pay_status=0 and  (create_time <= {$close_time} )"; 
		
	    $order = $this->orderModel->whereRaw($where)->get();//未支付，待兑换,订单超时
		if(!empty($order)){ 
			foreach($order as $k=>$v){
				$this->orderModel->where(['order_id'=>$v->order_id])->update(['order_status'=>6,'update_time'=>time()]); 
				file_put_contents("log.php","订单超时自动关闭：".$v->order_id."\n\r",FILE_APPEND);
			}
		}
	  
		
    }
}
