<?php

namespace Modules\GeneralPatientPickupStatus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralPatientPickupStatus\Database\Factories\PatientPickupStatusFactory;

class PatientPickupStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): PatientPickupStatusFactory
    {
        return PatientPickupStatusFactory::new();
    }
}