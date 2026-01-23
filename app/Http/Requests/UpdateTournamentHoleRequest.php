<?php

namespace App\Http\Requests;

use App\Models\TournamentRound;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTournamentHoleRequest extends FormRequest
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
            "holes"  => ['required', 'array', 'min:1'],
            "holes.*.id"  => ['required', 'exists:tournament_holes,id'],
            "holes.*.par"  => ['required', 'integer', 'min:0', 'max:15'],
            "holes.*.allowed_time"  => ['required', 'integer', 'min:0', 'max:59'],
        ];
    }
}
