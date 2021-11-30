<?php

namespace App\Console;

use App\Jobs\ClearTrashBox;
use App\Jobs\DeletePromoCodes;
use App\Jobs\DeleteUnconfirmedAccounts;
use App\Jobs\DeleteUnconfirmedDevicesAndIp;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [

    ];


    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new DeleteUnconfirmedDevicesAndIp)->hourly();
        $schedule->job(new DeleteUnconfirmedAccounts())->daily();
        $schedule->job(new ClearTrashBox())->daily();
        $schedule->job(new DeletePromoCodes)->everyTwoHours();
        //$schedule->job(new BlockToAvailableTransactions())->hourly();
    }



    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
