<?php

namespace App\Console;

use App\Models\Config;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        info("hello anh em");

        $timeClose = Config::find("timeclose")->cfg_value;
        // $schedule->command('cron:close')->cron("0 */$timeClose * * *")->withoutOverlapping();
        $schedule->command('cron:close')->everyMinute()->withoutOverlapping();
        $schedule->command('cron:alertDeadline')->everyMinute()->withoutOverlapping();
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
