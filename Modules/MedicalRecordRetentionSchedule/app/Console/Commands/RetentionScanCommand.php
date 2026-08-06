<?php

namespace Modules\MedicalRecordRetentionSchedule\Console\Commands;

use Illuminate\Console\Command;
use Modules\MedicalRecordRetentionSchedule\Services\RetentionScheduleService;

class RetentionScanCommand extends Command
{
    protected $signature = 'retention:scan';

    protected $description = 'Isi jadwal retensi rekam medis untuk registrasi baru & tandai jadwal yang sudah lewat masa simpan';

    public function handle(RetentionScheduleService $service): int
    {
        $result = $service->scan();

        $this->info("Dibuat {$result['created']} jadwal retensi baru, {$result['marked_eligible']} ditandai eligible_for_destruction.");

        return self::SUCCESS;
    }
}
