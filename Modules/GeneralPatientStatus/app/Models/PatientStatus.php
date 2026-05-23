<?php

namespace Modules\GeneralPatientStatus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralPatientStatus\Database\Factories\PatientStatusFactory;

class PatientStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): PatientStatusFactory
    {
        return PatientStatusFactory::new();
    }
}