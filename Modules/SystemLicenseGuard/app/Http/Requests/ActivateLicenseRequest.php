<?php

namespace Modules\SystemLicenseGuard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivateLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'license_token' => 'required_without:license_key|string',
            'license_key' => 'required_without:license_token|string',
            // Hanya boleh override ke host yang diizinkan; nilai lain memicu
            // SSRF dari server (Http::post() memakai nilai ini langsung).
            'central_hub_url' => ['nullable', 'url', Rule::in(array_filter([
                config('license.central_hub_url'),
                config('license.central_hub_url_fallback'),
            ]))],
        ];
    }
}
