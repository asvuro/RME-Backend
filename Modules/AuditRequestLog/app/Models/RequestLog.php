<?php

namespace Modules\AuditRequestLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Models\User;

/**
 * Port semangat logs.bridge_log simgos2 untuk request API masuk.
 * Ditulis HANYA oleh LogApiRequests middleware.
 */
class RequestLog extends Model
{
    protected $fillable = [
        'method',
        'url',
        'status',
        'duration_ms',
        'user_id',
        'ip',
        'payload',
    ];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
