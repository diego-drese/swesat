<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\PreDisparo::class,
        Commands\FinalizaAgendamento::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('swesat:pre_disparo')
//            ->everyMinute()
//            ->withoutOverlapping()
//            //->unlessBetween('23:00', '4:00')
//            ->sendOutputTo(storage_path()."/logs/schedule.log",true);

        $schedule->command('swesat:finaliza_agendamento')
            ->everyMinute()
            ->withoutOverlapping()
            //->unlessBetween('23:00', '4:00')
            ->sendOutputTo(storage_path()."/logs/schedule.log",true);
    }
}
