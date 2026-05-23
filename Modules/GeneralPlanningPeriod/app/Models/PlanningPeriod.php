<?php

namespace Modules\GeneralPlanningPeriod\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralPlanningPeriod\Database\Factories\PlanningPeriodFactory;

class PlanningPeriod extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): PlanningPeriodFactory
    {
        return PlanningPeriodFactory::new();
    }
}