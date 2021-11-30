<?php

namespace App\Http\Resources\User\Appeals;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;
use OpenApi\Annotations as OA;


/**
 * @OA\Schema(
 *     @OA\Xml(
 *         name="ShowAppealsResource"
 *     )
 * )
 */
class ShowAppealsResource extends JsonResource
{

    /**
     * @OA\Property (property="data", type="array",
     *     @OA\Items(
     * @OA\Property(property="appealId", type="string"),
     * @OA\Property(property="appealNumber", type="number"),
     * @OA\Property(property="appealTheme", type="string"),
     * @OA\Property(property="appealAnswered", type="string"),
     * @OA\Property(property="appealClosedAt", type="string"),
     *     ),
     *)
     * @OA\Property (property="status", type="integer", example="200")
     */

    #[ArrayShape(['appealId' => "mixed", 'appealNumber' => "mixed", 'appealTheme' => "mixed", 'appealAnswered' => "mixed", 'appealClosedAt' => "mixed"])]
    public function toArray($request): array
    {
        return [
            'appealId' => $this->id,
            'appealNumber' => $this->number,
            'appealTheme' => $this->theme,
            'appealAnswered' => $this->answered,
            'appealClosedAt' => $this->closed_at,
        ];
    }
}
