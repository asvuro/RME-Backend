<?php

namespace Modules\GeneralReservationStatus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralReservationStatus\Database\Factories\ReservationStatusFactory;

class ReservationStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): ReservationStatusFactory
    {
        return ReservationStatusFactory::new();
    }
}