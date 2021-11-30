<?php

namespace App\Jobs;


use App\Models\User\Verify\UserVerifyEmail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteUnconfirmedAccounts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {

    }


    public function handle()
    {
        $time = Carbon::now('Europe/Moscow')->subHours(24)->toDateTimeString();
        UserVerifyEmail::query()
            ->where('updated_at', '<=', $time)
            ->delete();
    }


    public function failed(\Throwable $e){
        info('Произошла ошибка во время очищения неподтверждённых аккаунтов. Информация об ошибке: ' . $e);
    }
}
