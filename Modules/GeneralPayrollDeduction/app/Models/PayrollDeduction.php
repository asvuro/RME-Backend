<?php

namespace Modules\GeneralPayrollDeduction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralPayrollDeduction\Database\Factories\PayrollDeductionFactory;

class PayrollDeduction extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): PayrollDeductionFactory
    {
        return PayrollDeductionFactory::new();
    }
}