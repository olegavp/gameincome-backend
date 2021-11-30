<?php

namespace App\Jobs;

use App\Models\AdminPanel\PromoCode\PromoCode;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeletePromoCodes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }


    public function handle()
    {
        $time = Carbon::now('Europe/Moscow')->toDateTimeString();
        PromoCode::query()
            ->where('finish_time', '!=', null)
            ->where('finish_time', '<=', $time)
            ->delete();
    }


    public function failed(\Throwable $e){
        info('Произошла ошибка во время деактивации промокода. Информация об ошибке: ' . $e);
    }
}
