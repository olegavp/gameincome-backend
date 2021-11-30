<?php

namespace App\Http\Controllers\MainPage;

use App\Http\Controllers\Controller;
use App\Http\Resources\MainPage\MainPageResource;
use Illuminate\Http\JsonResponse;


class MainPageController extends Controller
{
    public function all(): MainPageResource|JsonResponse
    {
        try
        {
            return (new MainPageResource(null))
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка при загрузке всех блоков главной страницы. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
