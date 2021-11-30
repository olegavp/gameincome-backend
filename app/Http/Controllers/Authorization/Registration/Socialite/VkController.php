<?php

namespace App\Http\Controllers\Authorization\Registration\Socialite;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\RedirectResponse;


class VkController extends Controller
{


    /**
     * @OA\Get (tags={"OLD_Authentication & Authorization"},
     *  path="/authorization/login/socialite/vk",
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
}
