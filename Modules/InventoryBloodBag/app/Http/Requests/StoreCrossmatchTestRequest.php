<?php

namespace Modules\InventoryBloodBag\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\InventoryBloodBag\Models\CrossmatchTest;

/**
 * Dipakai untuk POST blood-bags/{bag}/crossmatch — blood_bag_id diambil
 * dari parameter route, bukan dari body, jadi tidak divalidasi di sini.
 */
class StoreCrossmatchTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $results = Rule::in([CrossmatchTest::RESULT_POSITIVE, CrossmatchTest::RESULT_NEGATIVE]);

        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'major_result' => ['required', 'string', $results],
            'minor_result' => ['required', 'string', $results],
            'auto_control' => ['required', 'string', $results],
            'tested_by' => ['nullable', 'integer', 'exists:employees,id'],
        ];
    }
}
