<?php

namespace Modules\Grup\Console\Commands;

use Illuminate\Console\Command;
use Modules\Grup\Services\MembershipSynchronizer;

class SyncGroupMembershipCommand extends Command
{
    protected $signature = 'grup:sync-membership';

    protected $description = 'Sinkronkan grup dan cabang authoritative dari hub lisensi';

    public function handle(MembershipSynchronizer $sync): int
    {
        $group = $sync->sync();
        $this->info("Grup {$group->legal_name}: {$group->branches->count()} cabang tersinkron.");

        return self::SUCCESS;
    }
}
