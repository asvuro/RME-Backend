<?php

namespace Modules\GeneralPrintType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralPrintType\Database\Factories\PrintTypeFactory;

class PrintType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): PrintTypeFactory
    {
        return PrintTypeFactory::new();
    }
}