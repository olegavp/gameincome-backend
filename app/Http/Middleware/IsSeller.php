<?php

namespace App\Http\Middleware;

use App\Models\Seller\Seller;
use Closure;
use Illuminate\Http\Request;

class IsSeller
{
    public function handle(Request $request, Closure $next)
    {
        try
        {
            $seller = $request->user()->seller;
            if ($seller !== null)
            {
                return $next($request);
            }
            else
            {
                return response()->json(['warning' => 'Вы не являетесь продавцом. Если вы хотите им стать, то оставьте заявку на нашем сервисе!',
                    'status' => 400], 400);
            }
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время определения статуса на продавца! Пожалуйста, обратитесь в поддержку.',
                'status' => 400], 400);
        }
    }
}
