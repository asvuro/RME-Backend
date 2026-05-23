<?php

namespace Modules\GeneralPositionTitle\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralPositionTitle\Database\Factories\PositionTitleFactory;

class PositionTitle extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): PositionTitleFactory
    {
        return PositionTitleFactory::new();
    }
}