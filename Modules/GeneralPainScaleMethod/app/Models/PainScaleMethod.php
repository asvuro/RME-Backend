<?php

namespace Modules\GeneralPainScaleMethod\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralPainScaleMethod\Database\Factories\PainScaleMethodFactory;

class PainScaleMethod extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): PainScaleMethodFactory
    {
        return PainScaleMethodFactory::new();
    }
}