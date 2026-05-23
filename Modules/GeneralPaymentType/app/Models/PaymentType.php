<?php

namespace Modules\GeneralPaymentType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralPaymentType\Database\Factories\PaymentTypeFactory;

class PaymentType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): PaymentTypeFactory
    {
        return PaymentTypeFactory::new();
    }
}