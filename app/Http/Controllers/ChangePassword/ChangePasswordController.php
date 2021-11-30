<?php

namespace App\Http\Controllers\ChangePassword;

use App\Http\Controllers\Controller;
use App\Http\Requests\Once\PasswordRequest;
use App\Http\Requests\User\PersonalArea\Security\PasswordRequest as newPasswordAndOldPasswordRequest;
use App\Http\Services\CheckingUserDeviceInputParameters\DefineDeviceService;
use App\Models\User;
use App\Models\User\Ip\UserIp;
use App\Models\User\Verify\UserVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;


class ChangePasswordController extends Controller
{
    private DefineDeviceService $deviceInfo;

    public function __construct(DefineDeviceService $deviceInfo)
    {
        $this->deviceInfo = $deviceInfo;
    }

    public function changePasswordOnMailLink($hash, PasswordRequest $request): JsonResponse
    {
        try
        {
            //Ищем в таблице временных изменений и новых аккаунтов запись по hash, для которой нужно изменить пароль,
            //а затем удалить
            $userVerifyEmail = UserVerifyEmail::query()->where('hash', $hash)->first();
            if ($userVerifyEmail == null)
            {
                return response()->json(['error' => 'На данный момент невозможно изменить пароль, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            $user = User::query()->where('email', $userVerifyEmail->user_email)->first();
            if ($user == null)
            {
                return response()->json(['error' => 'На данный момент невозможно изменить пароль, попробуйте позже, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            /*
            @return Array $var['device', 'browser']
            @throws Json
            */
            try
            {
                //Берём информацию о девайсе и IP пользователя
                $aboutDevice = $this->deviceInfo->getInfoAboutDevice();
            }
            catch (\Throwable)
            {
                return response()->json(['error' => 'Произошла ошибка при определении устройства, пожалуйста, попробуйте зайти на сайт с другого устройства, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            $token = $user->createToken('token');

            DB::transaction(function () use($user, $request, $userVerifyEmail, $aboutDevice, $token)
            {
                $user->update(['password' => Hash::make($request->password)]);

                $userVerifyEmail->delete();

                //Выше изменили пароль, теперь удаляем все сессии и токены к ним и создаём новые для нового входа с новым паролем
                UserIp::query()->where('user_id', $user->id)->forceDelete();

                PersonalAccessToken::query()->where('tokenable_id', $user->id)->where('id','!=', $token->accessToken->id)->delete();

                $newUserIp = new UserIp;
                $newUserIp->user_id = $user->id;
                $newUserIp->token_id = $token->accessToken->id;
                $newUserIp->ip = $request->ip();
                $newUserIp->device = $aboutDevice['device'];
                $newUserIp->browser = $aboutDevice['browser'];
                $newUserIp->confirmed = 1;

                $newUserIp->save();
            });

            return response()->json(['message' => 'Вы успешно изменили пароль от своей учётной записи! Все сессии доступов, были аннулированы. Теперь попробуйте войти с новым паролем.',
                'status' => 200], 200);
        }
        catch (\Throwable)
        {
            if (isset($token))
            {
                PersonalAccessToken::query()->find($token->accessToken->id)->delete();
            }

            return response()->json(['error' => 'Произошла ошибка во время изменения пароля, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


    public function changePasswordInPersonalArea(newPasswordAndOldPasswordRequest $request): JsonResponse
    {
        try {
            //Проверяем по token, жива ли еще сессия, чтобы пользователь мог понять, успел он или нет сменить пароль.
            $user = User::query()->find($request->user()->id);
            if ($user == null)
            {
                return response()->json(['error' => 'Произошла ошибка! Данная сессия была аннулирована. Пожалуйста, обратитесь в поддержку!',
                    'status' => 400], 400);
            }

            if (!Hash::check($request->password, $user->password))
            {
                return response()->json(['warning' => 'Введён неверный старый пароль!',
                    'status' => 400], 400);
            }

            DB::transaction(function () use ($request, $user)
            {
                $user->update(['password' => Hash::make($request->newPassword)]);

                //Выше изменили пароль, теперь удаляем все сессии и токены к ним кроме нынешней сессии и токена
                $tokenId = stristr($request->bearerToken(), '|', true);

                UserIp::query()->where('user_id', $user->id)
                    ->where('token_id','!=', $tokenId)->forceDelete();

                PersonalAccessToken::query()->where('tokenable_id', $user->id)
                    ->where('id','!=', $tokenId)->delete();
            });

            return response()->json(['message' => 'Вы успешно изменили пароль от своей учётной записи! Все сессии доступов, кроме нынешнего, были аннулированы.',
                'status' => 200], 200);
        }
        catch (\Throwable)
        {
            return response()->json([
                'error' => 'Произошла ошибка во время изменения пароля. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
