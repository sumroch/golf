<?php

namespace App\Http\Requests;

use App\Models\TournamentRound;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResumeRequest extends FormRequest
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
        $tournament = TournamentRound::findOrFail($this->route('round'));

        return [
            "date"  => [
                Rule::date()->afterOrEqual(Carbon::createFromFormat('Y-m-d H:i:s', $tournament->action_date, "UTC")->timezone($tournament->timezone ?? 'Asia/Jakarta')->format('Y-m-d H:i:s')),
            ],
            "start_date"  => 'required|date',
            "start_hour"  => 'required|numeric|min:0|max:23',
            "start_minute"  => 'required|numeric|min:0|max:59',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'date' => $this->start_date . ' ' . str_pad($this->start_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($this->start_minute, 2, '0', STR_PAD_LEFT) . ':00',
        ]);
    }
}
