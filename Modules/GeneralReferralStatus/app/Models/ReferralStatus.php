<?php

namespace Modules\GeneralReferralStatus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralReferralStatus\Database\Factories\ReferralStatusFactory;

class ReferralStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): ReferralStatusFactory
    {
        return ReferralStatusFactory::new();
    }
}