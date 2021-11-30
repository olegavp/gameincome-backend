<?php

namespace App\Http\Controllers\User\Finances;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\Finances\TransactionsResource;
use App\Models\User\PersonalArea\Finance\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;


class GetTransactionsController extends Controller
{
    public function getTransactions(Request $request): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            return TransactionsResource::collection(Transaction::query()->where('user_id', $request->user()->id)->orderByDesc('created_at')->paginate('15'))
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время загрузки транзакций. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
