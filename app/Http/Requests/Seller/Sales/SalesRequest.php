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
class SalesRequest extends FormRequest
{

    /**
     * @OA\Property(property="state", type="string", description="parameters: bought, archived"),
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

    protected function prepareForValidation()
    {
        $this->merge(['type' => $this->route('type')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape(['type' => "string", 'state' => "string"])]
    public function rules(): array
    {
        return [
            'type' => 'bail|required|string',
            'state' => 'nullable|string'
        ];
    }
}
