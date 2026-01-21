<?php

namespace App\Http\Requests;

use App\Models\TournamentRefereeDuty;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;

class StoreTournamentRefereeRequest extends FormRequest
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
            'referees' => ['required', 'array'],
            'referees.*.user_id' => ['distinct', 'exists:users,id'],
            'referees.*.observer_type' => ['required', 'in:group,hole'],
            'referees.*.observer_id' => ['required', 'array'],
            'referees.*.observer_id.*' => ['required', 'numeric'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                foreach ($this->referees as $index => $referee) {
                    $type = Relation::getMorphedModel($referee['observer_type']);
                    $ids   = $referee['observer_id'];

                    if (!$type) {
                        $validator->errors()->add(
                            "referees.$index.observer_type",
                            "Invalid type."
                        );
                        continue;
                    }

                    foreach ($ids as $id) {
                        if (!$type::whereKey($id)->exists()) {
                            $validator->errors()->add(
                                "referees.$index.observer_id",
                                "Does not exist."
                            );
                        }
                    }
                }
            }
        ];
    }
}
