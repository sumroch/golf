<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupHoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'holes' => ['required', 'array'],
            'holes.*.par' => ['required', 'numeric', 'in:3,4,5'],
            'holes.*.allowed_time' => ['required', 'numeric', 'max:60'],
        ];
    }
}
