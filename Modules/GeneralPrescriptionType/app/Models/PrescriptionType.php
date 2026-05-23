<?php

namespace Modules\GeneralPrescriptionType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralPrescriptionType\Database\Factories\PrescriptionTypeFactory;

class PrescriptionType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): PrescriptionTypeFactory
    {
        return PrescriptionTypeFactory::new();
    }
}