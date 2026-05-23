<?php

namespace Modules\GeneralVisitActivityStatus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralVisitActivityStatus\Database\Factories\VisitActivityStatusFactory;

class VisitActivityStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): VisitActivityStatusFactory
    {
        return VisitActivityStatusFactory::new();
    }
}