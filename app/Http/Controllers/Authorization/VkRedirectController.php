<?php

namespace App\Http\Controllers\Authorization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authorization\SocialiteRegistrationRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Http\Services\Authorization\Socialite\Login\VkService as VkServiceLogin;
use App\Http\Services\Authorization\Socialite\Registration\VkService as VkServiceRegistration;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\RedirectResponse;


class VkRedirectController extends Controller
{
    private VkServiceRegistration $dataRegistration;
    private VkServiceLogin $dataLogin;

    public function __construct(VkServiceRegistration $dataRegistration, VkServiceLogin $dataLogin)
    {
        $this->dataRegistration = $dataRegistration;
        $this->dataLogin = $dataLogin;
    }


    /**
     *
     *
     * @OA\Get (
     *  tags={"OLD_Registration"},
     *  path="/authorization/registration/socialite/vk",
     *  operationId="registration-socialite-vk",
     *  summary="validates an account",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="error",
     *      )
     * )
     */
    public function connect(): RedirectResponse|JsonResponse
    {
        try
        {
            return Socialite::driver('vkontakte')->redirect();
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошёл сбой модуля ВКонтакте, Пожалуйста, сообщите об этом в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }

    /**
     * @OA\Get  (
     *  tags={"OLD_Registration"},
     *  path="/authorization/vk-redirect",
     *  operationId="registration-socialite-vk-save-user",
     *  summary="validates an account",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="error",
     *      )
     * )
     */
    public function redirect(): JsonResponse|UserResource
    {
        try
        {
            $userData = Socialite::driver('vkontakte')->user();
            if (User::query()->where('email', $userData->getEmail())->get()->isNotEmpty())
            {
                return $this->dataLogin->searchUser($userData);
            }
            else
            {
                return $this->dataRegistration->dataUserVk($userData);
            }
        }
        catch (\Throwable $e)
        {
            if ($e->getCode() === 401)
            {
                return response()->json(['message' => 'redirect',
                    'status' => 200], 200);

            }
            return response()->json(['error' => 'Произошла ошибка во время определения аккаунта в базе данных, пожалуйста, попробуйте зарегистрироваться/войти через email, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


    /**
     * @OA\Post  (
     *  tags={"OLD_Registration"},
     *  path="/authorization/registration/socialite/vk/save-user",
     *  operationId="registration-socialite-vk-save-user",
     *  summary="validates an account",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="error",
     *      )
     * )
     */
    public function saveUser(SocialiteRegistrationRequest $request): JsonResponse|UserResource
    {
        try
        {
            return $this->dataRegistration->saveUserVk($request);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время завершения регистрации через социальную сеть, пожалуйста, попробуйте зарегистрироваться с помощью email, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


}
