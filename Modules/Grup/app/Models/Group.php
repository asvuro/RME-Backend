<?php

namespace Modules\Grup\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $table = 'grup_groups';

    protected $fillable = ['hub_group_id', 'legal_name', 'legal_identifier', 'status', 'synced_at'];

    protected function casts(): array
    {
        return ['synced_at' => 'datetime'];
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }
}
