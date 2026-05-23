<?php

namespace App\Support;

use App\Models\RsSetting;
use App\Modules\Contracts\HospitalConfig;
use Illuminate\Support\Facades\Cache;

/**
 * Implementasi HospitalConfig di atas tabel rs_settings.
 *
 * Ala simgos2 (dataAkses->PC): nilai config di-cache agar gerbang admission
 * tidak memukul DB setiap request; cache di-flush saat set().
 */
class RsSettingService implements HospitalConfig
{
    // v2: bentuk cache lama ('rs_settings:all', tanpa key/description) tidak
    // kompatibel dengan entries()/raw(); versi dinaikkan agar tidak pernah
    // dibaca silang saat deploy di atas cache rememberForever yang sudah hangat.
    public const CACHE_KEY = 'rs_settings:all:v2';

    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        if (! array_key_exists($key, $settings)) {
            return $default;
        }

        return $this->cast($settings[$key]['value'], $settings[$key]['type']);
    }

    public function set(string $key, mixed $value, string $type = 'string', ?string $description = null): void
    {
        RsSetting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $this->serialize($value, $type),
                'type' => $type,
                'description' => $description,
            ],
        );

        Cache::forget(self::CACHE_KEY);
    }

    /** @return array<string, array{value: mixed, type: string, description: ?string}> */
    public function entries(): array
    {
        return collect($this->raw())
            ->map(fn (array $row) => [
                'value' => $this->cast($row['value'], $row['type']),
                'type' => $row['type'],
                'description' => $row['description'],
            ])
            ->all();
    }

    /** @return array<string, array{value: ?string, type: string}> */
    protected function all(): array
    {
        return collect($this->raw())
            ->mapWithKeys(fn (array $row) => [
                $row['key'] => ['value' => $row['value'], 'type' => $row['type']],
            ])
            ->all();
    }

    /**
     * Baris mentah rs_settings ter-cache; satu sumber untuk get/entries.
     *
     * @return array<string, array{key: string, value: ?string, type: string, description: ?string}>
     */
    protected function raw(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return RsSetting::query()
                ->get(['key', 'value', 'type', 'description'])
                ->mapWithKeys(fn (RsSetting $s) => [
                    $s->key => [
                        'key' => $s->key,
                        'value' => $s->value,
                        'type' => $s->type,
                        'description' => $s->description,
                    ],
                ])
                ->all();
        });
    }

    /** Konversi tersimpan → PHP, sesuai kolom type. */
    protected function cast(?string $raw, string $type): mixed
    {
        if ($raw === null) {
            return null;
        }

        return match ($type) {
            'bool' => in_array(strtolower($raw), ['1', 'true', 'yes', 'on'], true),
            'int' => (int) $raw,
            'json' => json_decode($raw, true),
            default => $raw,
        };
    }

    /** PHP → teks tersimpan; bool/int dinormalisasi agar round-trip stabil. */
    protected function serialize(mixed $value, string $type): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'bool' => $value ? 'true' : 'false',
            'int' => (string) (int) $value,
            'json' => json_encode($value),
            default => (string) $value,
        };
    }
}
