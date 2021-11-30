<?php

namespace App\Http\Controllers\Authorization\Confirm;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Models\User\Ip\UserIp;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;


class ConfirmNewDeviceAndIpController extends Controller
{

    public function confirmDeviceAndIp($hash): JsonResponse|UserResource
    {
        try
        {
            //Ищем в пока не подтверждённых записях запись с новыми IP/устройстве для подтверждения по hash из ссылки письма
            $userIp = UserIp::query()->where(['hash' => $hash, 'token_id' => null, 'confirmed' => 0])->first();
            if ($userIp == null)
            {
                return response()->json(['warning' => 'Не удалось найти ваш IP/устройство в списке ожидающих подтверждения, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            $user = User::query()->find($userIp->user_id);
            if ($user == null)
            {
                return response()->json(['warning' => 'Не удалось найти аккаунт, вероятно ошибка на сервере, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            $token = $user->createToken('token');

            $userIp->update([
                'confirmed' => 1,
                'confirmed_at' => Carbon::now('Europe/Moscow')->toDateTimeString(),
                'token_id' => $token->accessToken->id
            ]);

            return (new UserResource($user))->additional([
                'token' => $token->plainTextToken,
                'message' => 'Вы успешно подтвердили новые входные данные! Сейчас мы вас перенаправим на главную страницу.',
                'status' => 200
            ]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время подтверждения новых данных, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
