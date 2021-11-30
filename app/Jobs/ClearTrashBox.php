<?php

namespace App\Jobs;

use App\Models\AdminPanel\News\News;
use App\Models\AdminPanel\News\NewsComment;
use App\Models\AdminPanel\PromoCode\PromoCode;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearTrashBox implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }


    public function handle()
    {
        $time = Carbon::now('Europe/Moscow')->subMonths(2)->toDateTimeString();
        News::query()
            ->where('deleted_at', '<=', $time)
            ->forceDelete();

        NewsComment::query()
            ->where('deleted_at', '<=', $time)
            ->forceDelete();

        PromoCode::query()
            ->where('deleted_at', '<=', $time)
            ->forceDelete();
    }

    public function failed(\Throwable $e){
        info('Произошла ошибка во время очищения корзины. Информация об ошибке: ' . $e);
    }
}
