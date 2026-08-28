<?php

namespace Modules\Grup\Models;

use Illuminate\Database\Eloquent\Model;

class RealtimeEvent extends Model
{
    protected $table = 'grup_realtime_events';

    protected $fillable = ['event_id', 'event_type', 'branch_id', 'payload', 'received_at', 'processed_at', 'failure_reason'];

    protected function casts(): array
    {
        return ['payload' => 'encrypted:array', 'received_at' => 'datetime', 'processed_at' => 'datetime'];
    }
}
