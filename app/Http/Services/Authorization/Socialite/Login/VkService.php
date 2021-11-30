<?php

namespace App\Http\Services\Authorization\Socialite\Login;

use App\Http\Resources\User\UserResource;
//use App\Http\Services\CheckingUserDeviceInputParameters\FindOrCreateDeviceAndIpService;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
//use Illuminate\Http\Request;


class VkService
{
//    private FindOrCreateDeviceAndIpService $findOrCreateDeviceAndIpService;
//    private Request $request;
//
//    public function __construct(FindOrCreateDeviceAndIpService $findOrCreateDeviceAndIpService, Request $request)
//    {
//        $this->findOrCreateDeviceAndIpService = $findOrCreateDeviceAndIpService;
//        $this->request = $request;
//    }


    public function searchUser($userData): JsonResponse|UserResource
    {
        try
        {
            $user = User::query()->where('email', $userData->getEmail())->firstOrFail();

            //Нашли данного пользователя и идём проверять данные в сервисе об его устройстве и IP
            /*
            @param User $user
            @param Request $request
            @return UserIp(Model)
            @throws Array ['type', 'message', 'status']
            */
//            $processedInfo = $this->findOrCreateDeviceAndIpService->findOrCreate($user, $this->request);
//            if (gettype($processedInfo) == 'array')
//            {
//                return response()->json([
//                    $processedInfo['type'] => $processedInfo['message'],
//                    'status' => $processedInfo['status'],
//                    ], $processedInfo['status']);
//            }

//            $token = PersonalAccessToken::query()->where(['id' => $processedInfo->token_id, 'tokenable_id' => $user->id])->first();
            $token = PersonalAccessToken::query()->where('tokenable_id', $user->id)->first();
            if ($token == null)
            {
                return response()->json(['error' => 'Произошла ошибка во время входа, личный токен не был найден, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            return (new UserResource($user->load('balance')))->additional(['token' => $token->getKey() . '|' . $token->token, 'status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время входа, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
