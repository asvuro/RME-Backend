<?php

namespace Modules\GeneralMixtureInstruction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralMixtureInstruction\Database\Factories\MixtureInstructionFactory;

class MixtureInstruction extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): MixtureInstructionFactory
    {
        return MixtureInstructionFactory::new();
    }
}