<?php

namespace App\Models\User\PersonalArea\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\UseUuid as Uuid;
use OpenApi\Annotations as OA;


/**
 * @OA\Schema(
 *     @OA\Xml(
 *         name="Transaction"
 *     )
 * )
 */
class Transaction extends Model
{
    use HasFactory, Uuid;

    /**
     * @OA\Property(property="id", type="string"),
     * @OA\Property(property="product", type="string"),
     * @OA\Property(property="product_id", type="string"),
     * @OA\Property(property="item_type", type="string"),
     * @OA\Property(property="key_id", type="string"),
     * @OA\Property(property="operation", type="string"),
     * @OA\Property(property="action", type="boolean"),
     * @OA\Property(property="amount", type="integer"),
     * @OA\Property(property="created_at", type="string"),
     */
}
