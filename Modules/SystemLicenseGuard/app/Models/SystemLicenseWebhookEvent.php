<?php

namespace Modules\SystemLicenseGuard\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLicenseWebhookEvent extends Model
{
    protected $table = 'system_license_webhook_events';

    protected $fillable = [
        'event_id',
        'event_type',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];
}