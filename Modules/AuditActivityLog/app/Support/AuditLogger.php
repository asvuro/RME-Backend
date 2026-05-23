<?php

namespace Modules\AuditActivityLog\Support;

use App\Modules\Contracts\HospitalConfig;
use Illuminate\Support\Facades\Auth;
use Modules\AuditActivityLog\Models\ActivityLog;
use Modules\Auth\Models\User;

/**
 * Satu pintu penulisan jejak aktivitas — port semangat pengguna_akses_log
 * simgos2 yang ditulis eksplisit dari kode aplikasi.
 *
 * Field sensitif direduksi sebelum disimpan; daftar field dari config
 * audit.redact_fields (default password/token family).
 */
class AuditLogger
{
    /** Batas panjang JSON per kolom agar baris audit tak menelan memori. */
    protected const MAX_FIELD_LENGTH = 10000;

    public function __construct(protected HospitalConfig $config) {}

    /**
     * @param  array<string, mixed>|null  $before  keadaan lama (untuk update/delete)
     * @param  array<string, mixed>|null  $after   keadaan baru (create/update/event)
     */
    public function log(
        string $action,
        string $object,
        ?string $ref = null,
        ?array $before = null,
        ?array $after = null,
        ?User $user = null,
    ): void {
        if (! $this->config->get('audit.activity_log_enabled', true)) {
            return;
        }

        ActivityLog::create([
            'user_id' => ($user ?? Auth::user())?->getAuthIdentifier(),
            'action' => $action,
            'object' => $object,
            'ref' => $ref,
            'before' => $this->prepare($before),
            'after' => $this->prepare($after),
            'ip' => request()?->ip(),
        ]);
    }

    /** Reduksi field sensitif + batas panjang; null dibiarkan null. */
    protected function prepare(?array $payload): ?array
    {
        if ($payload === null) {
            return null;
        }

        $prepared = $this->redact($payload);

        return $this->truncate($prepared);
    }

    /** @return array<string, mixed> */
    protected function redact(array $payload): array
    {
        $sensitive = (array) $this->config->get('audit.redact_fields', [
            'password', 'password_confirmation', 'token', 'remember_token', 'authorization',
        ]);

        foreach ($payload as $key => $value) {
            if (in_array(strtolower((string) $key), $sensitive, true)) {
                $payload[$key] = '[redacted]';

                continue;
            }

            if (is_array($value)) {
                $payload[$key] = $this->redact($value);
            }
        }

        return $payload;
    }

    /** @param  array<string, mixed>  $payload */
    protected function truncate(array $payload): array
    {
        foreach ($payload as $key => $value) {
            if (is_string($value) && strlen($value) > self::MAX_FIELD_LENGTH) {
                $payload[$key] = substr($value, 0, self::MAX_FIELD_LENGTH).'…[truncated]';
            }
        }

        return $payload;
    }
}
