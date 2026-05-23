<?php

namespace Modules\GeneralReferralRoom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralReferralRoom\Database\Factories\ReferralRoomFactory;

class ReferralRoom extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): ReferralRoomFactory
    {
        return ReferralRoomFactory::new();
    }
}