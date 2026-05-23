<?php

namespace Modules\GeneralIcdType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralIcdType\Database\Factories\IcdTypeFactory;

class IcdType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): IcdTypeFactory
    {
        return IcdTypeFactory::new();
    }
}