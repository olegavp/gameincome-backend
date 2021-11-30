<?php

namespace App\Jobs;


use App\Models\User\Ip\UserIp;
use App\Models\User\Verify\UserVerifyEmail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteUnconfirmedDevicesAndIp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {

    }


    public function handle()
    {
        $time = Carbon::now('Europe/Moscow')->subHours(2)->toDateTimeString();
        UserIp::query()
            ->where('created_at', '<=', $time)
            ->forceDelete();
    }


    public function failed(\Throwable $e){
        info('Произошла ошибка во время очищения неподтверждённых IP адресов и девайсов. Информация об ошибке: ' . $e);
    }
}
