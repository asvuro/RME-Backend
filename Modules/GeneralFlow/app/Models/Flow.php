<?php

namespace Modules\GeneralFlow\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralFlow\Database\Factories\FlowFactory;

class Flow extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): FlowFactory
    {
        return FlowFactory::new();
    }
}