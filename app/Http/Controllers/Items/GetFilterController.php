<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Http\Services\Filter\MakeFilterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;


class GetFilterController extends Controller
{
    private MakeFilterService $makeFilterService;

    public function __construct(MakeFilterService $makeFilterService)
    {
        $this->makeFilterService = $makeFilterService;
    }

    public function get($type): JsonResponse|array
    {
        try
        {
            if ($type !== 'games' and $type !== 'software')
            {
                return response()->json(['error' => 'Произошла ошибка во время отображения фильтров, выбранный тип товара отсутствует на нашем сервисе! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            //Передаём параметр для формирования фильтра
            /*
            @param String $type
            @return Json
            @throws Array ['type', 'message', 'status']
            */
            $madeFilter = $this->makeFilterService->make($type);

            if (gettype($madeFilter) == 'array')
            {
                return response()->json([
                    $madeFilter['type'] => $madeFilter['message'],
                    'status' => $madeFilter['status'],
                ], $madeFilter['status']);
            }
            return $madeFilter;
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время запуска сервиса создания фильтров. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
