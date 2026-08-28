<?php

namespace Modules\Grup\Console\Commands;

use Illuminate\Console\Command;
use Modules\Grup\Services\ReverbSubscriber;

class ListenGroupRealtimeCommand extends Command
{
    protected $signature = 'grup:listen {--once : Berhenti setelah koneksi pertama terputus}';

    protected $description = 'Subscribe notifikasi private channel instance di Reverb hub';

    public function handle(ReverbSubscriber $subscriber): int
    {
        if (! config('grup.reverb.enabled')) {
            $this->warn('GRUP_REVERB_ENABLED=false; listener tidak dijalankan.');

            return self::SUCCESS;
        }

        do {
            try {
                $subscriber->listen();
            } catch (\Throwable $exception) {
                report($exception);
                $this->error('Koneksi realtime terputus; mencoba kembali.');
            }
            if (! $this->option('once')) {
                sleep(5);
            }
        } while (! $this->option('once'));

        return self::SUCCESS;
    }
}
