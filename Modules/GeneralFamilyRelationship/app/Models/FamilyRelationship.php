<?php

namespace Modules\GeneralFamilyRelationship\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralFamilyRelationship\Database\Factories\FamilyRelationshipFactory;

class FamilyRelationship extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): FamilyRelationshipFactory
    {
        return FamilyRelationshipFactory::new();
    }
}