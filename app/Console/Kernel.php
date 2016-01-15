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
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\Seed::class,
        \App\Console\Commands\SeedFull::class,
        \App\Console\Commands\SmartMailEngine::class,
        \App\Console\Commands\SmartMailSegmentEngine::class,
        \App\Console\Commands\SmartMailAutoresponder::class,
        \App\Console\Commands\AnalysisEngine::class,
        \App\Console\Commands\SupportTicketThreeDayPending::class,
        \App\Console\Commands\QATornado::class,
        \App\Console\Commands\SupportTicketFiveDayPending::class,
        \App\Console\Commands\ImportMemberEngine::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('smartmail-engine')->everyMinute();
        $schedule->command('smartmail-segment-engine')->everyMinute();
        $schedule->command('importmember-engine')->everyMinute();
        $schedule->command('smartmail-autoresponder')->hourly();
        $schedule->command('support-three-day')->hourly();
        $schedule->command('support-five-day')->hourly();
        $schedule->command('analysis-engine')->hourly();
    }
}
