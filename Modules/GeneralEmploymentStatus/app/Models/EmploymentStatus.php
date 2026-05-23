<?php

namespace Modules\GeneralEmploymentStatus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralEmploymentStatus\Database\Factories\EmploymentStatusFactory;

class EmploymentStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): EmploymentStatusFactory
    {
        return EmploymentStatusFactory::new();
    }
}