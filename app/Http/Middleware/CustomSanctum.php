<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use Closure;
use Illuminate\Http\Request;

class CustomSanctum
{
    public function handle(Request $request, Closure $next)
    {
        try
        {
            echo $request->bearerToken();
            $status = PersonalAccessToken::query()->where('token', $request->bearerToken())->get();

            if ($status->isEmpty())
            {
                return response()->json(['message' => 'Вы не авторизованы.'], 401);
            }
            else
            {
                return $next($request);
            }
        }
        catch (\Throwable $e)
        {
            return response()->json(['error' => 'Произошла ошибка во время определения статуса авторизации! Пожалуйста, обратитесь в поддержку. Текст ошибки: ' . $e], 200);
        }
    }
}
