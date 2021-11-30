<?php

namespace App\Jobs\DigiSeller;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class ParsingItemsAndSellers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }


    public function handle()
    {

    }


    public function failed(\Throwable $e){
        info('Произошла ошибка во время парсинга товаров и продавцов. Информация об ошибке: ' . $e);
    }
}
