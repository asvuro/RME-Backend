<?php

namespace Modules\Grup\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Branch extends Model
{
    protected $table = 'grup_branches';

    protected $fillable = [
        'group_id', 'hub_branch_id', 'instance_id', 'code', 'name', 'status',
        'is_local', 'capabilities', 'last_seen_at',
    ];

    protected function casts(): array
    {
        return ['is_local' => 'boolean', 'capabilities' => 'array', 'last_seen_at' => 'datetime'];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
