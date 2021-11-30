<?php

namespace App\Http\Controllers\DigiSeller;

use App\Http\Controllers\Controller;
use http\Env;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class ParsingItemsAndSellersController extends Controller
{
    public function parsing()
    {
        try
        {
            $sellerId = \env('SELLER_ID');
            $categoryId = \env('CATEGORY_ID');
            $response = Http::withHeaders([
                'X-First' => 'foo',
                'X-Second' => 'bar'
            ])->get('https://api.digiseller.ru/api/shop/products?seller_id=' . $sellerId . '&category_id={category_id}'
            );
        }
        catch (\Throwable $e)
        {
            return $e;
        }
    }
}
