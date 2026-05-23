<?php

namespace Modules\GeneralReturnCancellationReason\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralReturnCancellationReason\Database\Factories\ReturnCancellationReasonFactory;

class ReturnCancellationReason extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): ReturnCancellationReasonFactory
    {
        return ReturnCancellationReasonFactory::new();
    }
}