<?php

namespace Modules\AplikasiSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Contracts\HospitalConfig;
use Illuminate\Http\JsonResponse;
use Modules\AplikasiSetting\Http\Requests\SaveRsSettingRequest;

/**
 * Port REST PropertiConfig simgos2 (Aplikasi/V1/Rest/PropertiConfig).
 * Baca untuk semua pengguna terautentikasi; tulis hanya admin (rute).
 */
class RsSettingController extends Controller
{
    public function __construct(protected HospitalConfig $config) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->config->entries()]);
    }

    public function show(string $key): JsonResponse
    {
        $entries = $this->config->entries();

        if (! array_key_exists($key, $entries)) {
            abort(404, "Setting '{$key}' tidak ditemukan.");
        }

        return response()->json(['data' => [$key => $entries[$key]]]);
    }

    public function store(SaveRsSettingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $this->config->set(
            $validated['key'],
            $validated['value'],
            $validated['type'] ?? 'string',
            $validated['description'] ?? null,
        );

        return response()->json([
            'data' => [$validated['key'] => $this->config->entries()[$validated['key']]],
        ], 201);
    }

    public function update(SaveRsSettingRequest $request, string $key): JsonResponse
    {
        $validated = $request->validated();
        $entries = $this->config->entries();

        if (! array_key_exists($key, $entries)) {
            abort(404, "Setting '{$key}' tidak ditemukan.");
        }

        // Update parsial: type/description lama dipertahankan bila tak dikirim,
        // agar nilai bool/int/json tidak rusak oleh fallback 'string'.
        $this->config->set(
            $key,
            $validated['value'],
            $validated['type'] ?? $entries[$key]['type'],
            array_key_exists('description', $validated) ? $validated['description'] : $entries[$key]['description'],
        );

        return response()->json(['data' => [$key => $this->config->entries()[$key]]]);
    }
}
