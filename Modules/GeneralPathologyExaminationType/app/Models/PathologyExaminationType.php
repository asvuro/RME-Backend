<?php

namespace Modules\GeneralPathologyExaminationType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralPathologyExaminationType\Database\Factories\PathologyExaminationTypeFactory;

class PathologyExaminationType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): PathologyExaminationTypeFactory
    {
        return PathologyExaminationTypeFactory::new();
    }
}