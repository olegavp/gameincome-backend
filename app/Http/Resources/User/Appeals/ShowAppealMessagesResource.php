<?php

namespace App\Http\Resources\User\Appeals;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;


/**
 * @OA\Schema(
 *     @OA\Xml(
 *         name="ShowAppealMessagesResource"
 *     )
 * )
 */
class ShowAppealMessagesResource extends JsonResource
{

    /**
     * @OA\Property (property="data", type="array",
     *     @OA\Items(
     * @OA\Property(property="userName", type="string"),
     * @OA\Property(property="userSurname", type="string"),
     * @OA\Property(property="userAvatar", type="string"),
     * @OA\Property(property="text", type="string"),
     * @OA\Property(property="image", type="string"),
     * @OA\Property(property="isAdmin", type="boolean"),
     * @OA\Property(property="createdAt", type="string"),
     *     ),
     *)
     * @OA\Property (property="status", type="integer", example="200")
     */

    public function toArray($request): array
    {
        if ($this->admin_id !== null)
        {
            $user = $this->admin;
            return [
                'userName' => $user->name,
                'userSurname' => null,
                'userAvatar' => null,
                'text' => $this->text,
                'image' => $this->path_to_image,
                'isAdmin' => true,
                'createdAt' => $this->created_at
            ];
        }
        else
        {
            $user = $this->user;
            return [
                'userName' => $user->name,
                'userSurname' => $user->surname,
                'userAvatar' => $user->avatar,
                'text' => $this->text,
                'image' => $this->path_to_image,
                'isAdmin' => false,
                'createdAt' => $this->created_at
            ];
        }
    }
}
