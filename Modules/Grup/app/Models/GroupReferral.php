<?php

namespace Modules\Grup\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupReferral extends Model
{
    protected $table = 'grup_referrals';

    protected $fillable = [
        'hub_referral_id', 'group_id', 'source_branch_id', 'destination_branch_id',
        'local_patient_id', 'source_patient_id', 'patient_snapshot', 'reason',
        'clinical_summary', 'status', 'last_event_id', 'created_by', 'referred_at',
    ];

    protected function casts(): array
    {
        return [
            'patient_snapshot' => 'encrypted:array',
            'clinical_summary' => 'encrypted',
            'referred_at' => 'datetime',
        ];
    }

    public function sourceBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'source_branch_id');
    }

    public function destinationBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }
}
