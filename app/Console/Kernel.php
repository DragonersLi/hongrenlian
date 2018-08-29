<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [	 
			\App\Console\Commands\AutoCloseOrder::class,//自动关闭未支付订单
			\App\Console\Commands\AutoSureOrder::class,//自动确认收货完成订单
			\App\Console\Commands\AutoThaw::class,//自动解冻红人圈
			//\App\Console\Commands\SyncUser::class,

        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
		$schedule->command('command:AutoCloseOrder')->timezone('Asia/Shanghai')->everyMinute(); 
		$schedule->command('command:AutoSureOrder')->timezone('Asia/Shanghai')->everyMinute(); 
		$schedule->command('command:AutoThaw')->timezone('Asia/Shanghai')->everyMinute(); 
		//$schedule->command('command:sync_user')->timezone('Asia/Shanghai')->everyMinute(); 
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
