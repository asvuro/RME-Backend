<?php

namespace Modules\GeneralYesNoOption\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralYesNoOption\Database\Factories\YesNoOptionFactory;

class YesNoOption extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): YesNoOptionFactory
    {
        return YesNoOptionFactory::new();
    }
}