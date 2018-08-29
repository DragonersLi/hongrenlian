<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync_user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步用户信息！执行命令：/usr/local/php71/bin/php artisan command:sync_user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
		// file_put_contents("hello.txt",date('Y-m-d H:i:s')."\n\r",FILE_APPEND);//执行的动作
    }
}
