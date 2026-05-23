<?php

namespace Modules\GeneralVisitType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralVisitType\Database\Factories\VisitTypeFactory;

class VisitType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): VisitTypeFactory
    {
        return VisitTypeFactory::new();
    }
}