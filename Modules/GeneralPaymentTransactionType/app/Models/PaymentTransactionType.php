<?php

namespace Modules\GeneralPaymentTransactionType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralPaymentTransactionType\Database\Factories\PaymentTransactionTypeFactory;

class PaymentTransactionType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): PaymentTransactionTypeFactory
    {
        return PaymentTransactionTypeFactory::new();
    }
}