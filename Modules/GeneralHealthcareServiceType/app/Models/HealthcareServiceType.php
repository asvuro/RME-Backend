<?php

namespace Modules\GeneralHealthcareServiceType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralHealthcareServiceType\Database\Factories\HealthcareServiceTypeFactory;

class HealthcareServiceType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): HealthcareServiceTypeFactory
    {
        return HealthcareServiceTypeFactory::new();
    }
}