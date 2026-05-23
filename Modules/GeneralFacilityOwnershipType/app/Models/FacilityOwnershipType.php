<?php

namespace Modules\GeneralFacilityOwnershipType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralFacilityOwnershipType\Database\Factories\FacilityOwnershipTypeFactory;

class FacilityOwnershipType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): FacilityOwnershipTypeFactory
    {
        return FacilityOwnershipTypeFactory::new();
    }
}