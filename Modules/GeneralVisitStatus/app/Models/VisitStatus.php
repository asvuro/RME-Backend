<?php

namespace Modules\GeneralVisitStatus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralVisitStatus\Database\Factories\VisitStatusFactory;

class VisitStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): VisitStatusFactory
    {
        return VisitStatusFactory::new();
    }
}