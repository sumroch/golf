<?php

namespace App\Http\Requests;

use App\Models\TournamentRound;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StartTimeEditRequest extends FormRequest
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
            'morning_hour' => 'required|numeric|min:0|max:23',
            'morning_minute' => 'required|numeric|min:0|max:59',
            'afternoon_hour' => 'required|numeric|min:0|max:23',
            'afternoon_minute' => 'required|numeric|min:0|max:59',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'morning_hour' => (int) str_pad($this->morning_hour, 2, '0', STR_PAD_LEFT),
            'morning_minute' => (int) str_pad($this->morning_minute, 2, '0', STR_PAD_LEFT),
            'afternoon_hour' => (int) str_pad($this->afternoon_hour, 2, '0', STR_PAD_LEFT),
            'afternoon_minute' => (int) str_pad($this->afternoon_minute, 2, '0', STR_PAD_LEFT),
        ]);
    }

    protected function passedValidation()
    {
        $this->merge([
            'morning' => str_pad($this->morning_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($this->morning_minute, 2, '0', STR_PAD_LEFT) . ':00',
            'afternoon' => str_pad($this->afternoon_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($this->afternoon_minute, 2, '0', STR_PAD_LEFT) . ':00',
        ]);
    }
}
