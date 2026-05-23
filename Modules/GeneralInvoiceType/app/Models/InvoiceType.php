<?php

namespace Modules\GeneralInvoiceType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralInvoiceType\Database\Factories\InvoiceTypeFactory;

class InvoiceType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): InvoiceTypeFactory
    {
        return InvoiceTypeFactory::new();
    }
}