<?php

namespace Modules\GeneralCardType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralCardType\Database\Factories\CardTypeFactory;

class CardType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): CardTypeFactory
    {
        return CardTypeFactory::new();
    }
}