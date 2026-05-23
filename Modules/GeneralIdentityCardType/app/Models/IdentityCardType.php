<?php

namespace Modules\GeneralIdentityCardType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralIdentityCardType\Database\Factories\IdentityCardTypeFactory;

class IdentityCardType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): IdentityCardTypeFactory
    {
        return IdentityCardTypeFactory::new();
    }
}