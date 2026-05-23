<?php

namespace Modules\GeneralUserGroup\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralUserGroup\Database\Factories\UserGroupFactory;

class UserGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): UserGroupFactory
    {
        return UserGroupFactory::new();
    }
}