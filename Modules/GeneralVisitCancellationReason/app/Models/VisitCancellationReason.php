<?php

namespace Modules\GeneralVisitCancellationReason\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralVisitCancellationReason\Database\Factories\VisitCancellationReasonFactory;

class VisitCancellationReason extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): VisitCancellationReasonFactory
    {
        return VisitCancellationReasonFactory::new();
    }
}