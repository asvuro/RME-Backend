<?php

namespace Modules\AplikasiSetting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveRsSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Otorisasi ditangani middleware role:admin di rute.
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        // Konteks update ({key} dari rute): key di body diabaikan, bukan ditolak.
        $isUpdate = $this->route('key') !== null;

        return [
            'key' => $isUpdate
                ? []
                : ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:\.[a-z0-9_-]+)*$/'],
            'value' => ['present'],
            'type' => ['sometimes', 'string', 'in:string,int,bool,json'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $type = $this->input('type', 'string');
            $value = $this->input('value');

            if ($type === 'json' && $value !== null && json_encode($value) === false) {
                $validator->errors()->add('value', 'Nilai tidak dapat diserialisasi sebagai JSON.');
            }
        });
    }
}
