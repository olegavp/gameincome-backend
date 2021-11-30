<?php

namespace App\Http\Controllers\Authorization\Login;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authorization\LoginRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Services\CheckingUserDeviceInputParameters\FindOrCreateDeviceAndIpService;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class LoginController extends Controller
{
    private FindOrCreateDeviceAndIpService $findOrCreateDeviceAndIpService;

//    public function __construct(FindOrCreateDeviceAndIpService $findOrCreateDeviceAndIpService)
//    {
//        $this->findOrCreateDeviceAndIpService = $findOrCreateDeviceAndIpService;
    /**
     * @OA\Post (
     *  tags={"OLD_Authentication & Authorization"},
     *  path="/authorization/login",
     *  operationId="login",
     *  summary="validates an account",
     *  @OA\Parameter(name="email",
     *    in="query",
     *    required=true,
     *    @OA\Schema(type="string")
     *  ),
     *  @OA\Parameter(name="password",
     *    in="query",
     *    required=true,
     *    @OA\Schema(type="string")
     *  ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=201,
     *          description="Вы ввели неверные данные, попробуйте снова!",
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Произошла ошибка во время входа, личный токен не был сгенерирован, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!",
     *      ),
     * )
     */
    public function login(LoginRequest $request): JsonResponse|UserResource
    {
        try {
            $user = User::where('email', $request->email)->first();
            if ($user == null || !Hash::check($request->password, $user->password)) {
                return response()->json(['warning' => 'Вы ввели неверные данные, попробуйте снова!', 'status' => 201], 201);
            }

            //Нашли данного пользователя и идём проверять данные в сервисе об его устройстве и IP
            /*
            @param User $user
            @param Request $request
            @return UserIp(Model)
            @throws Array ['type', 'message', 'status']
            */
//            $processedInfo = $this->findOrCreateDeviceAndIpService->findOrCreate($user, $request);
//            if (gettype($processedInfo) == 'array')
//            {
//                return response()->json([
//                    $processedInfo['type'] => $processedInfo['message'],
//                    'status' => $processedInfo['status'],
//                    ], $processedInfo['status']);
//            }
//
//            $token = PersonalAccessToken::query()->where(['id' => $processedInfo->token_id, 'tokenable_id' => $user->id])->first();
//            $token = PersonalAccessToken::query()->where('tokenable_id', $user->id)->first();
            $token = $user->createToken('token')->plainTextToken;
            //   Log::info($user->createToken('token')->plainTextToken);
            if ($token == null) {
                return response()->json(['error' => 'Произошла ошибка во время входа, личный токен не был сгенерирован, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            return (new UserResource($user->load('balance')))->additional(['token' => $token, 'status' => 200]);
        } catch (\Throwable) {
            return response()->json(['error' => 'Произошла ошибка во время входа, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }

//    }
}
