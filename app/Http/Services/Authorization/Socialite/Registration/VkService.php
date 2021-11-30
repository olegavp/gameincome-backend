<?php

namespace App\Http\Services\Authorization\Socialite\Registration;

use App\Http\Resources\User\UserResource;
use App\Models\User\Ip\UserIp;
use App\Models\User;
use App\Models\User\PersonalArea\Finance\UserBalance;
use App\Models\User\Verify\UserVerifyEmail;
use App\Http\Services\CheckingUserDeviceInputParameters\DefineDeviceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class VkService
{
    private Request $request;
    private DefineDeviceService $deviceInfo;
    private $token;
    private $user;

    public function __construct(Request $request, DefineDeviceService $deviceInfo)
    {
        $this->request = $request;
        $this->deviceInfo = $deviceInfo;
    }


    public function dataUserVk($userData): JsonResponse
    {
        try
        {
            $userVerify = new UserVerifyEmail;
            $userVerify->user_email = $userData->getEmail();
            $userVerify->user_name = $userData->user['first_name'];
            $userVerify->user_surname = $userData->user['last_name'];
            $userVerify->user_avatar = $userData->getAvatar();
            $userVerify->hash = Hash::make($userData->getEmail() . $userData->getAvatar());
            $userVerify->save();

            return response()->json(['hash' => $userVerify->hash, 'status' => 200], 200);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время регистрации пользователя через социальную сеть! Пожалуйста, попробуйте зарегистрироваться с помощью email, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


    public function saveUserVk($request): JsonResponse|UserResource
    {
        try
        {
            $userVerify = UserVerifyEmail::query()->where('hash', $request->hash)->first();

            if ($userVerify === null)
            {
                return response()->json(['error' => 'Ошибка создания/поиска пользователя, пожалуйста, попробуйте зарегистрироваться/войти через email, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
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


            DB::transaction(function () use($userVerify, $request, $aboutDevice)
            {
                $user = new User;
                $user->name =  $userVerify->user_name;
                $user->surname =  $userVerify->user_surname;
                $user->email =  $userVerify->user_email;
                $user->nickname = $request->nickname;
                $user->password = Hash::make($request->password);
                $user->avatar = $userVerify->user_avatar;
                $user->save();
                $this->user = $user;

                $balance = new UserBalance;
                $balance->user_id = $user->id;
                $balance->save();

                $token = $user->createToken('token');
                $this->token = $token;

                $userIp = new UserIp;
                $userIp->user_id = $user->id;
                $userIp->ip = $this->request->ip();
                $userIp->token_id = $token->accessToken->id;
                $userIp->device = $aboutDevice['device'];
                $userIp->browser = $aboutDevice['browser'];
                $userIp->confirmed = 1;
                $userIp->save();

                UserVerifyEmail::where('user_email', $userVerify->user_email)->delete();
            });

            return (new UserResource($this->user->load('balance')))->additional(['token' => $this->token->plainTextToken, 'status' => 201]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Ошибка создания пользователя, пожалуйста, попробуйте зарегистрироваться/войти через email, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
