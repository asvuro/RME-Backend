<?php

namespace Modules\GeneralReferralType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralReferralType\Database\Factories\ReferralTypeFactory;

class ReferralType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): ReferralTypeFactory
    {
        return ReferralTypeFactory::new();
    }
}