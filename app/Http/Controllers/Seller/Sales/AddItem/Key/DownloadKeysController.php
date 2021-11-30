<?php

namespace App\Http\Controllers\Seller\Sales\AddItem\Key;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\ProcessKeyRequest;
use App\Http\Resources\Seller\ItemInfoResource;
use Illuminate\Http\JsonResponse;


class DownloadKeysController extends Controller
{
    public function  downloadKeys(ProcessKeyRequest $request): JsonResponse|ItemInfoResource
    {
        try
        {
            $keys = array();
            foreach (file($request->file) as $id => $key)
            {
                if (strpos($key, ','))
                {
                    return response()->json(['warning' => 'Обнаружена запятая, проверьте загружаемый файл на наличие запятой, её не должно быть!',
                        'status' => 400], 400);
                }

                $keys[$id] = ltrim(rtrim(str_replace("\r\n", NULL, $key)));
            }
            return response()->json(['keys' => $keys, 'countKeys' => collect($keys)->count(), 'status' => 200], 200);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время обработки ключей из текстового документа. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
