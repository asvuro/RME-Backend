<?php

namespace Modules\GeneralOtherStatus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralOtherStatus\Database\Factories\OtherStatusFactory;

class OtherStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): OtherStatusFactory
    {
        return OtherStatusFactory::new();
    }
}