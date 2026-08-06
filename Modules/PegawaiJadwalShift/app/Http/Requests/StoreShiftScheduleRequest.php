<?php

namespace Modules\PegawaiJadwalShift\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShiftScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff_member_id' => ['nullable', 'integer', 'exists:staff_members,id'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'ward_id' => ['nullable', 'integer', 'exists:wards,id'],
            'shift_type' => ['required', Rule::in(['pagi', 'siang', 'malam'])],
            'shift_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i:s,H:i'],
            'end_time' => ['required', 'date_format:H:i:s,H:i'],
            'status' => ['sometimes', Rule::in(['scheduled', 'confirmed', 'absent'])],
        ];
    }
}
