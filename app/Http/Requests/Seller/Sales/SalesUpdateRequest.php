<?php

namespace App\Http\Requests\Seller\Sales;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;
use OpenApi\Annotations as OA;


/**
 * @OA\Schema(
 *      type="object",
 * )
 */
class SalesUpdateRequest extends FormRequest
{

    /**
     * @OA\Property(property="archive", type="boolean"),
     */

    /**
     * Остановить валидацию после первой неуспешной проверки.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape(['archive' => "string"])]
    public function rules(): array
    {
        return [
            'archive' => 'bail|required|boolean',
        ];
    }
}
