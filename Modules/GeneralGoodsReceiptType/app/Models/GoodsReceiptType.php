<?php

namespace Modules\GeneralGoodsReceiptType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralGoodsReceiptType\Database\Factories\GoodsReceiptTypeFactory;

class GoodsReceiptType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): GoodsReceiptTypeFactory
    {
        return GoodsReceiptTypeFactory::new();
    }
}