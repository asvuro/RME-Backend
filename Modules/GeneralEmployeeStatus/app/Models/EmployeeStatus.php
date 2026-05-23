<?php

namespace Modules\GeneralEmployeeStatus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralEmployeeStatus\Database\Factories\EmployeeStatusFactory;

class EmployeeStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): EmployeeStatusFactory
    {
        return EmployeeStatusFactory::new();
    }
}