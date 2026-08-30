<?php

namespace Modules\AuditActivityLog\Support;

use Illuminate\Support\Arr;
use Modules\AuditActivityLog\Models\ActivityLog;

/**
 * Tempel pada model yang perlu diaudit mekanis: setiap create/update/delete
 * tercatat di activity_logs bersama transaksi domainnya (bukan efek samping
 * pasca-commit). Nama objek default = nama tabel; override lewat auditObject().
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model): void {
            app(AuditLogger::class)->log(
                ActivityLog::ACTION_CREATED,
                $model->auditObject(),
                (string) $model->getKey(),
                null,
                Arr::except($model->getAttributes(), $model->auditHidden()),
            );
        });

        static::updated(function ($model): void {
            $changes = Arr::except($model->getChanges(), ['updated_at']);

            if ($changes === []) {
                return;
            }

            // Kolom tersembunyi tetap menghasilkan baris audit (peristiwanya
            // sah untuk dicatat), tapi isinya di-mask, bukan dibuang — kalau
            // dibuang, update yang HANYA menyentuh kolom itu (mis. segarkan
            // payload karcis) lenyap dari jejak audit sama sekali.
            $mask = fn (array $values) => collect($values)
                ->map(fn ($value, $key) => in_array($key, $model->auditHidden(), true) ? '[hidden]' : $value)
                ->all();

            app(AuditLogger::class)->log(
                ActivityLog::ACTION_UPDATED,
                $model->auditObject(),
                (string) $model->getKey(),
                $mask(Arr::only($model->getOriginal(), array_keys($changes))),
                $mask($changes),
            );
        });

        static::deleted(function ($model): void {
            app(AuditLogger::class)->log(
                ActivityLog::ACTION_DELETED,
                $model->auditObject(),
                (string) $model->getKey(),
                Arr::except($model->getAttributes(), $model->auditHidden()),
                null,
            );
        });
    }

    /** Identitas objek dalam jejak audit; default nama tabel. */
    public function auditObject(): string
    {
        return (new static)->getTable();
    }

    /**
     * Kolom yang DILARANG masuk jejak audit — untuk snapshot PHI/PII yang
     * duplikat-data (mis. payload karcis berisi identitas pasien): audit cukup
     * mencatat identifier (document_number/ref_id), bukan menyalin isinya.
     *
     * @return array<int, string>
     */
    public function auditHidden(): array
    {
        return [];
    }
}
