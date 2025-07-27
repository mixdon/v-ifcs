<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\RunProphetForecast;

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
     */
    protected function schedule(Schedule $schedule): void
    {
        // Scheduler berbasis konfigurasi (opsional)
        $hour = config('app.hour');
        $min = config('app.min');
        $scheduledInterval = $hour !== '' 
            ? (($min !== '' && $min != 0) ?  $min .' */'. $hour .' * * *' : '0 */'. $hour .' * * *') 
            : '*/'. $min .' * * * *';

        if (env('IS_DEMO')) {
            $schedule->command('migrate:fresh --seed')->cron($scheduledInterval);
        }

        // Tambahkan job RunProphetForecast secara otomatis
        $schedule->job(new RunProphetForecast('jumlah_produksi', 12))->monthlyOn(1, '01:00');
        $schedule->job(new RunProphetForecast('total_pendapatan', 12))->monthlyOn(1, '01:30');
        $schedule->job(new RunProphetForecast('total_laba', 12))->monthlyOn(1, '02:00');

        // Sesuaikan metrik dan waktu sesuai kebutuhan Anda
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
