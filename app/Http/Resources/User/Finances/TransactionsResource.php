<?php

namespace App\Http\Resources\User\Finances;

use App\Models\User\PersonalArea\Finance\Transaction;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     @OA\Xml(
 *         name="TransactionsResource"
 *     )
 * )
 */
class TransactionsResource extends JsonResource
{
    /**
     * @OA\Property(property="data", type="array",
     *  @OA\Items(ref="#/components/schemas/Transaction"),
     * ),
     * @OA\Property(property="status", type="integer", example="200"),
     */

    #[ArrayShape(['id' => "mixed", 'product' => "mixed", 'operation' => "mixed", 'action' => "mixed", 'amount' => "float|int", 'created_at' => "mixed"])]
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'product' => $this->product,
            'operation' => $this->operation,
            'action' => $this->action,
            'amount' => $this->amount / 100,
            'created_at' => $this->created_at
        ];
    }
}
