<?php

namespace App\Jobs;

use App\Models\User\PersonalArea\Finance\Transaction;
use App\Models\User\PersonalArea\Finance\UserBalance;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BlockToAvailableTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        $availableTransactions = Transaction::query()
            ->where(['counted' => 0, 'available' => 1])
            ->select('id', 'amount', 'user_id')
            ->get();

        $sum = 0;
        $id = null;
        foreach ($availableTransactions as $transaction)
        {
            $sum = $sum + $transaction['amount'];
            $id[] =  $transaction['id'];
            UserBalance::query()->where('user_id', $transaction['user_id']);
        }
        Transaction::query()
            ->whereIn('id', $id)
            ->update(['counted' => 1]);


        $time = Carbon::now('Europe/Moscow')->subHours(240)->toDateTimeString();
        $availableForTimeTransactions = Transaction::query()
            ->where(['counted' => 0, 'available' => 0, 'updated_at', '<=' => $time])
            ->select('id', 'amount', 'user_id')
            ->get();

        $sum = 0;
        $id = null;
        foreach ($availableForTimeTransactions as $transaction)
        {
            $sum = $sum + $transaction['amount'];
            $id[] =  $transaction['id'];
        }

        Transaction::query()
            ->whereIn('id', $id)
            ->update(['counted' => 1]);
    }


    public function failed(\Throwable $e){
        info('Произошла ошибка во время разморозки средств. Информация об ошибке: ' . $e);
    }
}
