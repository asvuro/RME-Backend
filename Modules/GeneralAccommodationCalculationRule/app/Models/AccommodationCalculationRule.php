<?php

namespace Modules\GeneralAccommodationCalculationRule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralAccommodationCalculationRule\Database\Factories\AccommodationCalculationRuleFactory;

class AccommodationCalculationRule extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): AccommodationCalculationRuleFactory
    {
        return AccommodationCalculationRuleFactory::new();
    }
}