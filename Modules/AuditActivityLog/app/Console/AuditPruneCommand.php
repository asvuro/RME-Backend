<?php

namespace Modules\AuditActivityLog\Console;

use App\Modules\Contracts\HospitalConfig;
use Illuminate\Console\Command;
use Modules\AuditActivityLog\Models\ActivityLog;
use Modules\AuditRequestLog\Models\RequestLog;

/**
 * Pengganti sederhana partisi tahunan logs.* simgos2: buang jejak lebih tua
 * dari N hari. Jalankan terjadwal dari scheduler deploy.
 */
class AuditPruneCommand extends Command
{
    protected $signature = 'audit:prune {--days= : umur maksimum jejak dalam hari}';

    protected $description = 'Hapus activity_logs & request_logs lebih tua dari batas hari';

    public function handle(HospitalConfig $config): int
    {
        $days = (int) ($this->option('days') ?: $config->get('audit.prune_days', 365));

        if ($days <= 0) {
            $this->error('Batas hari harus positif.');

            return self::FAILURE;
        }

        $cutoff = now()->subDays($days);

        $activity = ActivityLog::query()->where('created_at', '<', $cutoff)->delete();
        $requests = RequestLog::query()->where('created_at', '<', $cutoff)->delete();

        $this->info("Terhapus {$activity} activity log dan {$requests} request log (batas {$days} hari).");

        return self::SUCCESS;
    }
}
