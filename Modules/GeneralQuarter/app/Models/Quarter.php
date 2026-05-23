<?php

namespace Modules\GeneralQuarter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralQuarter\Database\Factories\QuarterFactory;

class Quarter extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): QuarterFactory
    {
        return QuarterFactory::new();
    }
}