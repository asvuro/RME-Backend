<?php

namespace Modules\GeneralOperationGroup\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralOperationGroup\Database\Factories\OperationGroupFactory;

class OperationGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): OperationGroupFactory
    {
        return OperationGroupFactory::new();
    }
}