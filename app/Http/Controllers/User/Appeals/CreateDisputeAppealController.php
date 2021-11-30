<?php

namespace App\Http\Controllers\User\Appeals;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\PersonalArea\Purchases\CreateDisputeRequest;
use App\Models\Item\GameKey;
use App\Models\Item\SoftwareKey;
use App\Models\Purchase\GamePurchase;
use App\Models\Purchase\SoftwarePurchase;
use App\Models\User\PersonalArea\Appeals\DisputeAppeal;
use App\Models\User\PersonalArea\Appeals\DisputeAppealMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CreateDisputeAppealController extends Controller
{
    public function createDispute($type, $keyId, CreateDisputeRequest $request): JsonResponse
    {
        try
        {
            if ($type === 'game')
            {
                $query = GamePurchase::query();
            }
            elseif ($type === 'software')
            {
                $query = SoftwarePurchase::query();
            }
            else
            {
                return response()->json(['error' => 'Произошла ошибка во время создания спора, вы не выбрали тип товара. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }
            Log::info('dispute');

            $user = $request->user()->with('balance');
Log::info($user);
            $userId = $user->id;
            $purchase = $query->where(['user_id' => $userId, 'key_id' => $keyId])->first();

            if ($purchase === null)
            {
                return response()->json(['error' => 'Произошла ошибка, в ваших покупках нет данного ключа!',
                    'status' => 400], 400);
            }

            $appeal = DisputeAppeal::query()->where(['user_id' => $userId, 'key_id' => $keyId])->first();

            if ($appeal !== null)
            {
                return response()->json(['warning' => 'У вас уже есть спор по данному ключу! Вы можете найти его в списке споров на вкладке "обращения" в личном кабинете.',
                    'status' => 400], 400);
            }

            if ($type === 'game')
            {
                $key = GameKey::query()->where('id', $keyId)->first();
            }
            elseif ($type === 'software')
            {
                $key = SoftwareKey::query()->where('id', $keyId)->first();
            }

            DB::transaction(function () use ($key, $request, $userId)
            {
                $appeal = new DisputeAppeal;
                $appeal->number = rand(100000,999999);
                $appeal->user_id = $userId;
                $appeal->key_id = $key->id;
                $appeal->seller_id = $key->seller_id;
                $appeal->save();

                if( $request->hasFile('image')){
                    $filenameWithExt = $request->file('image')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $fileNameToStore = "image/".$filename."_".time().".".$extension;
                    $request->file('image')->storeAs('public/support-files', $fileNameToStore);
                }

                $message = new DisputeAppealMessage;
                $message->appeal_id = $appeal->id;
                $message->user_id = $userId;
                $message->text = $request->text;
                $message->path_to_image = env('URL_FOR_FILES') . '/storage/support-files/' . $fileNameToStore;
                $message->save();
            });

            return response()->json(['message' => 'Следите за ходом спора в разделе Обращений в вашем Личном кабинете!',
                'status' => 201], 201);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время создания обращения. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
