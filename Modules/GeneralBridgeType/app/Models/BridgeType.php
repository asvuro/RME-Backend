<?php

namespace Modules\GeneralBridgeType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralBridgeType\Database\Factories\BridgeTypeFactory;

class BridgeType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): BridgeTypeFactory
    {
        return BridgeTypeFactory::new();
    }
}