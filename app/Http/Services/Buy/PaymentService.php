<?php

namespace App\Http\Services\Buy;

use App\Models\Item\GameKey;
use App\Models\Item\SoftwareKey;
use App\Models\User;
use App\Models\User\PersonalArea\Finance\UserBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class PaymentService extends WriteOffService
{
    public function balancePayment($chekOnActuality, $request)
    {
        try
        {
            $user = $request->user();

            $defineKeys = $this->defineKeys($chekOnActuality);
            if (!is_array($defineKeys))
            {
                return $defineKeys;
            }

            $price = null;
            $gameKeys = collect();
            $softwareKeys = collect();
            if ($defineKeys['gameKeys']->isNotEmpty())
            {
                $gameKeys = GameKey::query()->with('item', 'seller.user')->whereIn('id', $defineKeys['gameKeys'])->get();
                foreach ($gameKeys as $gameKey)
                {
                    if ($gameKey->service_sale_price === null)
                    {
                        $price = $price + $gameKey->service_price;
                    }
                    else
                    {
                        $price = $price + $gameKey->service_sale_price;
                    }
                }
            }
            elseif ($defineKeys['softwareKeys']->isNotEmpty())
            {
                $softwareKeys = SoftwareKey::query()->with('item', 'seller.user')->whereIn('id', $defineKeys['softwareKeys'])->get();
                foreach ($softwareKeys as $softwareKey)
                {
                    if ($softwareKey->service_sale_price === null)
                    {
                        $price = $price + $softwareKey->service_price;
                    }
                    else
                    {
                        $price = $price + $softwareKey->service_sale_price;
                    }
                }
            }
            else
            {
                return response()->json(['warning' => 'Произошла ошибка во время списывании товаров, все товары уже куплены! Попробуйте перезагрузить страницу, должны появиться предложения от дургих продавцов, Спасибо!',
                    'status' => 400], 400);
            }

            $userBalance = $user->load('balance')->balance;
            if ($price > $userBalance->available_balance)
            {
                return response()->json(['warning' => 'Недостаточно средств на балансе, ',
                    'status' => 400], 400);
            }


            DB::transaction(function () use ($gameKeys, $softwareKeys, $user, $userBalance, $price)
            {
                $gamePurchases = array();
                $softwarePurchases = array();
                $userTransactions = array();
                $sellerTransactions = array();
                // списание с баланса покупателя
                UserBalance::query()->where('user_id', $user->id)
                    ->update([
                        'available_balance' => $userBalance->available_balance - $price,
                        'overall_balance' => $userBalance->overall_balance - $price,
                    ]);
                if ($gameKeys->isNotEmpty())
                {
                    foreach ($gameKeys as $gameKey)
                    {
                        if ($gameKey->service_sale_price === null)
                        {
                            $price = $gameKey->service_price;
                        } else
                        {
                            $price = $gameKey->service_sale_price;
                        }
                        $sellerUser = User::where('id', $gameKey->seller->user->id)->with('balance')->first();
                        $sellerUserBalance = $sellerUser->balance;
                        // пополнения замороженных балансов продавцов
                        UserBalance::query()->where('user_id', $sellerUser->id)
                            ->update([
                                'pending_balance' => $sellerUserBalance->pending_balance + $price,
                                'overall_balance' => $sellerUserBalance->overall_balance + $price,
                            ]);
                        // запись о покупке товара
                        array_push($gamePurchases,
                            [
                                'id' => Str::uuid()->toString(),
                                'user_id' => $user->id,
                                'seller_id' => $gameKey->seller_id,
                                'key_id' => $gameKey->id,
                                'item_id' => $gameKey->item_id
                            ]);

                        array_push($userTransactions,
                            [
                                'id' => Str::uuid()->toString(),
                                'user_id' => $user->id,
                                'product' => $gameKey->item->name,
                                'product_id' => $gameKey->item_id,
                                'item_type' => 'games',
                                'key_id' => $gameKey->id,
                                'operation' => 'Покупка игры',
                                'action' => 0,
                                'available' => 1,
                                'amount' => $price
                            ]);

                        array_push($sellerTransactions,
                            [
                                'id' => Str::uuid()->toString(),
                                'user_id' => $sellerUser->id,
                                'product' => $gameKey->item->name,
                                'product_id' => $gameKey->item_id,
                                'item_type' => 'games',
                                'key_id' => $gameKey->id,
                                'operation' => 'Продажа игры',
                                'action' => 1,
                                'available' => 0,
                                'amount' => $price
                            ]);
                    }
                }
                elseif ($softwareKeys->isNotEmpty())
                {
                    foreach ($softwareKeys as $softwareKey)
                    {
                        if ($softwareKey->service_sale_price === null)
                        {
                            $price = $softwareKey->service_price;
                        }
                        else
                        {
                            $price = $softwareKey->service_sale_price;
                        }
                        $sellerUser = User::where('id', $softwareKey->seller->user->id)->with('balance')->first();
                        $sellerUserBalance = $sellerUser->balance;
                        // пополнения замороженных балансов продавцов
                        UserBalance::query()->where('user_id', $sellerUser->id)
                            ->update([
                                'pending_balance' => $sellerUserBalance->pending_balance + $price,
                                'overall_balance' => $sellerUserBalance->overall_balance + $price,
                            ]);
                        // запись о покупке товара
                        array_push($softwarePurchases,
                            [
                                'id' => Str::uuid()->toString(),
                                'user_id' => $user->id,
                                'seller_id' => $softwareKey->seller_id,
                                'key_id' => $softwareKey->id,
                                'item_id' => $softwareKey->item_id
                            ]);

                        array_push($userTransactions,
                            [
                                'id' => Str::uuid()->toString(),
                                'user_id' => $user->id,
                                'product' => $softwareKey->item->name,
                                'product_id' => $softwareKey->item_id,
                                'item_type' => 'software',
                                'key_id' => $softwareKey->id,
                                'operation' => 'Покупка софта',
                                'action' => 0,
                                'available' => 1,
                                'amount' => $price
                            ]);

                        array_push($sellerTransactions,
                            [
                                'id' => Str::uuid()->toString(),
                                'user_id' => $sellerUser->id,
                                'product' => $softwareKey->item->name,
                                'product_id' => $softwareKey->item_id,
                                'item_type' => 'software',
                                'key_id' => $softwareKey->id,
                                'operation' => 'Продажа софта',
                                'action' => 1,
                                'available' => 0,
                                'amount' => $price
                            ]);
                    }
                }

                if (count($gamePurchases) !== 0)
                {
                    DB::table('game_purchases')->insert($gamePurchases);
                }
                if (count($softwarePurchases) !== 0)
                {
                    DB::table('software_purchases')->insert($gamePurchases);
                }
                DB::table('transactions')->insert(array_merge($userTransactions, $sellerTransactions));
            });

            return $this->writeOff($gameKeys, $softwareKeys);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время оплаты через баланс! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


    public function cardPayment($items, $request)
    {
        try
        {
            //Сервис платёжки. Морозим товары, так как ответ от платёжки может быть долгим.
            return response()->json(['warning' => 'Недостаточно средств на карте', 'status' => 400], 400);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время оплаты через карту! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
