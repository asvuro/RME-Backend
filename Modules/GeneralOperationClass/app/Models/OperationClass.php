<?php

namespace Modules\GeneralOperationClass\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralOperationClass\Database\Factories\OperationClassFactory;

class OperationClass extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): OperationClassFactory
    {
        return OperationClassFactory::new();
    }
}