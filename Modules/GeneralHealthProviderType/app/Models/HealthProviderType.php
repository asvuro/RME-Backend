<?php

namespace Modules\GeneralHealthProviderType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralHealthProviderType\Database\Factories\HealthProviderTypeFactory;

class HealthProviderType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): HealthProviderTypeFactory
    {
        return HealthProviderTypeFactory::new();
    }
}