<?php

namespace Modules\GeneralSitbTb03RoTransfer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbTb03RoTransfer\Database\Factories\SitbTb03RoTransferFactory;

class SitbTb03RoTransfer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbTb03RoTransferFactory
    {
        return SitbTb03RoTransferFactory::new();
    }
}