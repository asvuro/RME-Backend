<?php

namespace Modules\GeneralAccidentGuarantorType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralAccidentGuarantorType\Database\Factories\AccidentGuarantorTypeFactory;

class AccidentGuarantorType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): AccidentGuarantorTypeFactory
    {
        return AccidentGuarantorTypeFactory::new();
    }
}