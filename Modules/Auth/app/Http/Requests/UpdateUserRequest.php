<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('user')?->id;
        $authId = $this->user()?->id;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'username' => ['sometimes', 'string', 'max:255', Rule::unique('users', 'username')->ignore($id)],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'password' => ['sometimes', 'string', Password::min(8)->mixedCase()->numbers()],
            // Tidak boleh diterapkan ke akun milik sendiri -- kalau tidak,
            // admin (atau sesi admin yang dibajak) bisa mengunci/menonaktifkan
            // dirinya sendiri dan kehilangan satu-satunya akses admin.
            'is_locked' => [Rule::excludeIf(fn () => $id === $authId), 'boolean'],
            'is_active' => [Rule::excludeIf(fn () => $id === $authId), 'boolean'],
        ];
    }
}
