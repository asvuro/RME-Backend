<?php

namespace App\Modules\Contracts;

/**
 * Kontrak konfigurasi RS — port tabel `aplikasi.properti_config` simgos2.
 *
 * simgos2 membaca gerbang bisnis via ID config (mis. 69 = lock tagihan saat
 * kasir menutup, 118 = izin terima order konsul/lab/rad/resep). Di RME kita
 * pakai kunci bernama agar terbaca; pemetaan ID lama dicatat di seeder.
 *
 * Implementasi: App\Support\RsSettingService (cache rememberForever, flush saat update).
 */
interface HospitalConfig
{
    /**
     * Ambil nilai setting; $default bila kunci belum diisi.
     *
     * @param string $key    nama setting (mis. 'billing.lock_on_cashier_close')
     * @return mixed  hasil dikonversi sesuai kolom type (string/json/int/bool)
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Tulis/ubah setting dan flush cache.
     */
    public function set(string $key, mixed $value, string $type = 'string', ?string $description = null): void;

    /**
     * Daftar seluruh setting (port REST PropertiConfig simgos2).
     *
     * @return array<string, array{value: mixed, type: string, description: ?string}>
     *         key => nilai SUDAH ter-cast sesuai type.
     */
    public function entries(): array;
}
