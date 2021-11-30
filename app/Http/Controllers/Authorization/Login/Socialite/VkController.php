<?php

namespace App\Http\Controllers\Authorization\Login\Socialite;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;


class VkController extends Controller
{
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
