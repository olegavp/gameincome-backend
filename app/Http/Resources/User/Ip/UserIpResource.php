<?php

namespace App\Http\Resources\User\Ip;

use Illuminate\Http\Resources\Json\JsonResource;

class UserIpResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            $this->ip
        ];
    }
}
