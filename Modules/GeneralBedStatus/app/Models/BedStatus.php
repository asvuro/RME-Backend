<?php

namespace Modules\GeneralBedStatus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralBedStatus\Database\Factories\BedStatusFactory;

class BedStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): BedStatusFactory
    {
        return BedStatusFactory::new();
    }
}