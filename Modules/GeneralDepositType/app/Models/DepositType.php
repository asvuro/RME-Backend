<?php

namespace Modules\GeneralDepositType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralDepositType\Database\Factories\DepositTypeFactory;

class DepositType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): DepositTypeFactory
    {
        return DepositTypeFactory::new();
    }
}