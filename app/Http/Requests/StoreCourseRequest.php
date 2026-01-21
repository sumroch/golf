<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:128'],
            'location' => ['required', 'string', 'max:64'],
            'par' => ['required', 'numeric', 'max:50'],
            'total_holes' => ['required', 'numeric', 'max:20'],
            'holes' => ['required', 'array'],
            'holes.*.par' => ['required', 'numeric', 'max:50'],
            'holes.*.allowed_time' => ['required', 'numeric', 'max:60'],
        ];
    }
}
