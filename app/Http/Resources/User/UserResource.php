<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;
use OpenApi\Annotations as OA;

class UserResource extends JsonResource
{
    #[ArrayShape(['id' => "mixed", 'name' => "mixed", 'surname' => "mixed", 'email' => "mixed", 'nickname' => "mixed", 'avatar' => "mixed", 'balance' => "array"])]
    public function toArray($request): array
    {
        $balance = $this->balance;
        $balance = [
            'overall' => $balance->overall / 100,
            'pending' => $balance->pending / 100,
            'available' => $balance->available / 100];

        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'nickname' => $this->nickname,
            'avatar' => $this->avatar,
            'balance' => $balance,
            ];
    }
}
