<?php

namespace App\Http\Controllers\Seller\Sales\AddItem\Key;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\SearchItemRequest;
use App\Http\Services\Search\SearchItemsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class SearchGameAndSoftwareController extends Controller
{
    private SearchItemsService $searchItemsService;

    public function __construct(SearchItemsService $searchItemsService)
    {
        $this->searchItemsService = $searchItemsService;
    }

    public function searchItem(SearchItemRequest $request): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            /*
             * @description Передаём $request для поиска товаров
             * @param Request
             * @return ResourceCollection or Array(items, status)
             * @throw Json
             */
            return $this->searchItemsService->search($request);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время работы сервиса поиска. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
