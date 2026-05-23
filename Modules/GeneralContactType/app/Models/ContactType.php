<?php

namespace Modules\GeneralContactType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralContactType\Database\Factories\ContactTypeFactory;

class ContactType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): ContactTypeFactory
    {
        return ContactTypeFactory::new();
    }
}