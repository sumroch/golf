<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTournamentRequest extends FormRequest
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
            'organizer' => ['required', 'string', 'max:128'],
            'date_start' => ['required', 'date'],
            'round' => ['required', 'integer', 'min:0'],
            'timezone' => ['nullable', Rule::in('Asia/Jakarta', 'Asia/Makassar', 'Asia/Jayapura')],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ];
    }
}
