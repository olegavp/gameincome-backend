<?php

namespace App\Http\Resources\User\Finances;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     @OA\Xml(
 *         name="BalanceResource"
 *     )
 * )
 */
class BalanceResource extends JsonResource
{
    /**
     * @OA\Property(property="data", ref="#/components/schemas/UserBalance"),
     * @OA\Property(property="status", type="integer", example="200"),
     */

    #[ArrayShape(['overallBalance' => "float|int", 'availableBalance' => "float|int", 'pendingBalance' => "float|int", 'blockingBalance' => "float|int"])]
    public function toArray($request): array
    {
        $balance = $this->balance;

        return [
            'overallBalance' => $balance->overall_balance / 100,
            'availableBalance' => ($balance->available_balance - $balance->blocking_balance) / 100,
            'pendingBalance' => $balance->pending_balance / 100,
            'blockingBalance' => $balance->blocking_balance / 100,
        ];
    }
}
