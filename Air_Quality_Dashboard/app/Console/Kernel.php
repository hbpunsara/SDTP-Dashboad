<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Cache;

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
        // Only run simulation if it's activated by the admin
        $schedule->call(function () {
            $simulationActive = Cache::get('simulation_active', false);
            $simulationInterval = Cache::get('simulation_interval', 15); // Default: 15 minutes
            
            if ($simulationActive) {
                \Illuminate\Support\Facades\Artisan::call('simulate:data');
                \Illuminate\Support\Facades\Log::info('Air quality data simulation executed at ' . now());
            }
        })->everyMinute();
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