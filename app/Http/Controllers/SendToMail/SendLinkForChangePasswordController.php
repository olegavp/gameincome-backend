<?php

namespace App\Http\Controllers\SendToMail;

use App\Http\Controllers\Controller;
use App\Http\Requests\Once\EmailRequest;
use App\Jobs\SendChangePasswordLink;
use App\Models\User;
use App\Models\User\Verify\UserVerifyEmail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;


class SendLinkForChangePasswordController extends Controller
{
    public function linkForChangePassword(EmailRequest $request): JsonResponse
    {
        try
        {
            //Добавляем в таблицу временных изменений и новых аккаунтов запись, которая после смены пароля удалится.
            $time = Carbon::now()->toDateTimeString();
            $email = $request->email;

            $user = User::query()->where('email', $email)->first();
            if ($user == null)
            {
                return response()->json(['warning' => 'Пользователя с данным email не существует!',
                    'status' => 400], 400);
            }

            $hash = hash("sha256", $time . $email . rand(0, 99), false);

            $userVerify = new UserVerifyEmail;
            $userVerify->user_email = $email;
            $userVerify->hash = $hash;
            $userVerify->save();

            SendChangePasswordLink::dispatch($email, $hash);

            return response()->json(['message' => 'На ваш Email отправлено письмо!',
                'status' => 200], 200);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время отправки письма для восстановления пароля, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
