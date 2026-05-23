<?php

namespace Modules\AuditActivityLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Models\User;

/**
 * Port logs.pengguna_akses_log simgos2: siapa mengubah apa, kapan, dengan
 * keadaan sebelum/sesudah. Tulis HANYA lewat Modules\AuditActivityLog\
 * Support\AuditLogger — bukan create() langsung.
 */
class ActivityLog extends Model
{
    public const ACTION_CREATED = 'created';

    public const ACTION_READ = 'read';

    public const ACTION_UPDATED = 'updated';

    public const ACTION_DELETED = 'deleted';

    public const ACTION_EVENT = 'event';

    protected $fillable = [
        'user_id',
        'action',
        'object',
        'ref',
        'before',
        'after',
        'ip',
    ];

    protected function casts(): array
    {
        return ['before' => 'array', 'after' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
