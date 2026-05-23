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
                $model->getAttributes(),
            );
        });

        static::updated(function ($model): void {
            $changes = Arr::except($model->getChanges(), ['updated_at']);

            if ($changes === []) {
                return;
            }

            app(AuditLogger::class)->log(
                ActivityLog::ACTION_UPDATED,
                $model->auditObject(),
                (string) $model->getKey(),
                Arr::only($model->getOriginal(), array_keys($changes)),
                $changes,
            );
        });

        static::deleted(function ($model): void {
            app(AuditLogger::class)->log(
                ActivityLog::ACTION_DELETED,
                $model->auditObject(),
                (string) $model->getKey(),
                $model->getAttributes(),
                null,
            );
        });
    }

    /** Identitas objek dalam jejak audit; default nama tabel. */
    public function auditObject(): string
    {
        return (new static)->getTable();
    }
}
