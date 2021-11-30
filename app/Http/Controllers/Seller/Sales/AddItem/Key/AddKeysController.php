<?php

namespace App\Http\Controllers\Seller\Sales\AddItem\Key;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\AddKeyRequest;
use App\Http\Resources\Seller\ItemInfoResource;
use App\Models\Item\Game;
use App\Models\Item\GameKey;
use App\Models\Item\Region;
use App\Models\Item\Software;
use App\Models\Item\SoftwareKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;


class AddKeysController extends Controller
{
    public function addKey(AddKeyRequest $request): JsonResponse|ItemInfoResource
    {
        try
        {
            if (substr($request->keys, 0) === ',')
            {
                return response()->json(['warning' => 'Произошла ошибка во время считывания ключей! Проверьте начало ключей, там не должно быть запятых.',
                    'status' => 400], 400);
            }

            if ($request->itemType === 'game' or $request->itemType === 'software')
            {
                $keys = explode(',', $request->keys);
                if (collect($keys)->count() > 1)
                {
                    foreach ($keys as $key) {
                        $key = ltrim(rtrim($key));
                        if (strlen($key) <= 4)
                        {
                            return response()->json(['warning' => 'Произошла ошибка во время считывания ключей! Проверьте ключи, некоторые из них имеют короткое значение или же запятую в конце последнего ключа в списке.',
                                'status' => 400], 400);
                        }
                    }

                    if ($request->itemType === 'game')
                    {
                        $isUnique = GameKey::query()->whereIn('key', $keys)->where('item_id', $request->itemId)->select('id')->withTrashed()->get();

                        if ($isUnique->isNotEmpty())
                        {
                            return response()->json(['warning' => 'Некоторые ключи не являются действительными! Обратитесь в поддержку.',
                                'status' => 400], 400);
                        }

                        $item = Game::query()->find($request->itemId);
                        $model = new GameKey;
                    }
                    elseif ($request->itemType === 'software')
                    {
                        $isUnique = SoftwareKey::query()->whereIn('key', $keys)->where('item_id', $request->itemId)->select('id')->withTrashed()->get();

                        if ($isUnique->isNotEmpty())
                        {
                            return response()->json(['warning' => 'Некоторые ключи не являются действительными! Обратитесь в поддержку.',
                                'status' => 400], 400);
                        }

                        $item = Software::query()->find($request->itemId);
                        $model = new SoftwareKey;
                    }

                    if ($item === null)
                    {
                        return response()->json(['error' => 'Произошла ошибка во время добавления товара, выбранная игра или программа отсутствует на нашем сервисе! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                            'status' => 400], 400);
                    }

                    if (Region::query()->where('id', $request->regionId)->first() === null)
                    {
                        return response()->json(['error' => 'Произошла ошибка во время добавления товара, выбранный вами регион отсутствует на нашем сервисе! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                            'status' => 400], 400);
                    }

                    $userSellerId = $request->user()->seller->id;
                    $itemId = $request->itemId;
                    $sellerPrice = $request->price;
                    $servicePrice = $request->price * env('PERCENT');
                    $regionId = $request->regionId;

                    $records = array();
                    foreach ($keys as $key)
                    {
                        $records[] = [
                            'id' => (string) Str::uuid(),
                            'seller_id' => $userSellerId,
                            'item_id' => $itemId,
                            'key' => $key,
                            'seller_price' => $sellerPrice,
                            'service_price' => $servicePrice,
                            'region_id' => $regionId,
                        ];
                    }

                    $model->query()->insert($records);

                    return response()->json(['message' => 'Вы успешно добавили товары на продажу!',
                        'status' => 201], 201);
                }

                if ($request->itemType === 'game')
                {
                    $isUnique = GameKey::query()->where('key', $keys[0])->where('item_id', $request->itemId)->select('id')->withTrashed()->get();

                    if ($isUnique->isNotEmpty())
                    {
                        return response()->json(['warning' => 'Некоторые ключи не являются действительными! Обратитесь в поддержку.',
                            'status' => 400], 400);
                    }

                    $item = Game::query()->find($request->itemId);
                    $model = new GameKey;
                }
                elseif ($request->itemType === 'software')
                {
                    $isUnique = SoftwareKey::query()->where('key', $keys[0])->where('item_id', $request->itemId)->select('id')->withTrashed()->get();

                    if ($isUnique->isNotEmpty())
                    {
                        return response()->json(['warning' => 'Некоторые ключи не являются действительными! Обратитесь в поддержку.',
                            'status' => 400], 400);
                    }

                    $item = Software::query()->find($request->itemId);
                    $model = new SoftwareKey;
                }

                if ($item === null)
                {
                    return response()->json(['error' => 'Произошла ошибка во время добавления товара, выбранная игра или программа отсутствует на нашем сервисе! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                        'status' => 400], 400);
                }

                if (Region::query()->where('id', $request->regionId)->first() === null)
                {
                    return response()->json(['error' => 'Произошла ошибка во время добавления товара, выбранный вами регион отсутствует на нашем сервисе! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                        'status' => 400], 400);
                }

                $itemKey = $model;
                $itemKey->seller_id = $request->user()->seller->id;
                $itemKey->item_id = $request->itemId;
                $itemKey->key = $keys[0];
                $itemKey->seller_price = $request->price;
                $itemKey->service_price = $request->price * env('PERCENT');
                $itemKey->region_id = $request->regionId;
                $itemKey->save();

                return response()->json(['message' => 'Вы успешно добавили товар на продажу!',
                    'status' => 201], 201);
            }
            else
            {
                return response()->json(['error' => 'Произошла ошибка во время добавления товара, выбранный тип товара отсутствует на нашем сервисе! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время добавления товара на продажу. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                 'status' => 400], 400);
        }
    }
}
