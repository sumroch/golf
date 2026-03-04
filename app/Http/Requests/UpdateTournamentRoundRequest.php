<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTournamentRoundRequest extends FormRequest
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
            'start_interval_hour' => ['required', 'numeric', 'min:0', 'max:23'],
            'start_interval_minute' => ['required', 'numeric', 'min:0', 'max:59'],
            'morning_hour' => ['required', 'numeric', 'min:0', 'max:23'],
            'morning_minute' => ['required', 'numeric', 'min:0', 'max:59'],
            'afternoon_hour' => ['required', 'numeric', 'min:0', 'max:23'],
            'afternoon_minute' => ['required', 'numeric', 'min:0', 'max:59'],
            'crossover_one_hour' => ['required', 'numeric', 'min:0', 'max:23'],
            'crossover_one_minute' => ['required', 'numeric', 'min:0', 'max:59'],
            'crossover_ten_hour' => ['required', 'numeric', 'min:0', 'max:23'],
            'crossover_ten_minute' => ['required', 'numeric', 'min:0', 'max:59'],
            'ball' => ['required', Rule::in([2, 3, 4])],
            'transportation' => ['required', Rule::in(['walk', 'cart', 'combine'])],
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'tee_area' => json_encode($this->tee_area),
            'start_interval' => $this->formatTime($this->start_interval_hour, $this->start_interval_minute),
            'morning' => $this->formatTime($this->morning_hour, $this->morning_minute),
            'afternoon' => $this->formatTime($this->afternoon_hour, $this->afternoon_minute),
            'crossover_one' => $this->formatTime($this->crossover_one_hour, $this->crossover_one_minute),
            'crossover_ten' => $this->formatTime($this->crossover_ten_hour, $this->crossover_ten_minute),
        ]);
    }

    protected function formatTime($hour, $minute)
    {
        return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT) . ':00';
    }
}
