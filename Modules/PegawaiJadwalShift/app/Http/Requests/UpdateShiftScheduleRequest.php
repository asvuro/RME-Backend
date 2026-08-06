<?php

namespace Modules\PegawaiJadwalShift\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShiftScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff_member_id' => ['sometimes', 'nullable', 'integer', 'exists:staff_members,id'],
            'employee_id' => ['sometimes', 'nullable', 'integer', 'exists:employees,id'],
            'ward_id' => ['sometimes', 'nullable', 'integer', 'exists:wards,id'],
            'shift_type' => ['sometimes', Rule::in(['pagi', 'siang', 'malam'])],
            'shift_date' => ['sometimes', 'date'],
            'start_time' => ['sometimes', 'date_format:H:i:s,H:i'],
            'end_time' => ['sometimes', 'date_format:H:i:s,H:i'],
            'status' => ['sometimes', Rule::in(['scheduled', 'confirmed', 'absent'])],
        ];
    }
}
