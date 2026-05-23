<?php

namespace Modules\GeneralPayrollAddition\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralPayrollAddition\Database\Factories\PayrollAdditionFactory;

class PayrollAddition extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): PayrollAdditionFactory
    {
        return PayrollAdditionFactory::new();
    }
}