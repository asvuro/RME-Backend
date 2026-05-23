<?php

namespace Modules\GeneralAgeGroup\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralAgeGroup\Database\Factories\AgeGroupFactory;

class AgeGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): AgeGroupFactory
    {
        return AgeGroupFactory::new();
    }
}