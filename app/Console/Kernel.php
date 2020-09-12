<?php

namespace App\Console;

use App\Http\Controllers\TestController;
use function foo\func;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
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
        $schedule->call(function () {
            try {

                $testController = new TestController();

                $testController->finishExpiredTests();
            } catch (\Exception $err) {}
        })->everyMinute();

        $schedule->call(function() {
            try {
                $testController = new TestController();

                $testController->removeOldTests();
            } catch (\Exception $err) {}
        })->monthly();
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
