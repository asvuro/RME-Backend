<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Port `aplikasi.properti_config` simgos2 (ID → VALUE) sebagai kunci bernama.
 * Akses bisnis selalu lewat App\Support\RsSettingService / HospitalConfig,
 * bukan model ini langsung.
 */
class RsSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    protected function casts(): array
    {
        return [
            // value disimpan mentah (text); konversi tipe dilakukan service agar
            // default dari kode bisa ikut dikonversi dengan aturan yang sama.
        ];
    }
}
