<?php

namespace App\Http\Controllers\Api\V1\Types;

use App\Http\Controllers\Controller;
use JetBrains\PhpStorm\ArrayShape;


class TypesController extends Controller
{

    #[ArrayShape(['games' => "string[]", 'software' => "string[]", 'skins' => "string[]", 'cases' => "string[]"])]
    static function itemTypes(): array
    {
        return [
            'games' => [
                'modelPurchase' => 'App\Models\Purchase\GamePurchase',
                'modelKey' => 'App\Models\Item\GameKey',
                'error' => 'Произошла ошибка во время отображения активных игровых ключей на продаже. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
            ],
            'software' => [
                'modelPurchase' => 'App\Models\Purchase\SoftwarePurchase',
                'modelKey' => 'App\Models\Item\SoftwareKey',
                'error' => 'Произошла ошибка во время отображения активных софтовых ключей на продаже. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
            ],
            'skins' => [
                'modelPurchase' => 'App\Models\Purchase\SkinPurchase',
                'modelKey' => 'App\Models\Item\SkinKey',
                'error' => 'Произошла ошибка во время отображения информации. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
            ],
            'cases' => [
                'modelPurchase' => 'App\Models\Purchase\CasesPurchase',
                'modelKey' => 'App\Models\Item\CasesKey',
                'error' => 'Произошла ошибка во время отображения информации. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
            ],
        ];
    }

    #[ArrayShape(['general' => "string[]", 'dispute' => "string[]", 'partnership' => "string[]", 'tech-support' => "string[]"])]
    static function appealTypes(): array
    {
        return [
            'general' => [
                'name' => 'general',
                'model' => 'App\Models\User\PersonalArea\Appeals\GeneralAppeal',
                'error' => 'Произошла ошибка во время отображения активных игровых ключей на продаже. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
            ],
            'dispute' => [
                'name' => 'dispute',
                'model' => 'App\Models\User\PersonalArea\Appeals\DisputeAppeal',
                'error' => 'Произошла ошибка во время отображения активных софтовых ключей на продаже. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
            ],
            'partnership' => [
                'name' => 'partnership',
                'model' => 'App\Models\User\PersonalArea\Appeals\PartnershipAppeal',
                'error' => 'Произошла ошибка во время отображения информации. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
            ],
            'tech-support' => [
                'name' => 'tech-support',
                'model' => 'App\Models\User\PersonalArea\Appeals\TechSupportAppeal',
                'error' => 'Произошла ошибка во время отображения информации. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
            ],
        ];
    }

}
