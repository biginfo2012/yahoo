<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
        Commands\GetCategory::class,
        Commands\GetProduct::class,
        Commands\GetToken::class,
        Commands\GetShopCategory::class,
        Commands\GetProductDetail::class,
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('command:get-product')->everyMinute();
//        $schedule->command('command:get-category')->everyTenMinutes();
        $schedule->command('command:get-token')->everyTenMinutes();
        $schedule->command('command:get-shop-category')->everyMinute();
        $schedule->command('command:get-product-detail')->everyMinute();
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
