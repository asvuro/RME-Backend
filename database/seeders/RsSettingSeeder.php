<?php

namespace Database\Seeders;

use App\Support\RsSettingService;
use Illuminate\Database\Seeder;

/**
 * Nilai default konfigurasi RS — port penuh PropertiConfig simgos2.
 *
 * Sumber:
 *   - db/new/aplikasi/data/properti_config.sql (22 baris dasar) + seluruh patch
 *     = 41 kunci aplikasi + 4 kunci plugin jadwal operasi (ID 90081–90084).
 *   - 5 kunci hanya ada di DB hidup yang dirujuk kode numerik:
 *     94/95 (farmasi eksekutif), 124/125 (restriksi penjamin), 129 (item ganda).
 *   - Klaim lama 69/118 dipetakan ke slug yang sudah berjalan sejak #7.
 *
 * Idempoten: kunci bernilai non-null tidak ditimpa agar override deploy aman.
 */
class RsSettingSeeder extends Seeder
{
    public function run(RsSettingService $settings): void
    {
        // [key, value, type, description]
        $defaults = [
            // === Gerbang bisnis yang sudah dikonsumsi kode (#7) ===
            ['billing.lock_on_cashier_close', true, 'bool', 'PropertiConfig 69: kunci tagihan kunjungan saat kasir menutup pembayaran'],
            ['billing.auto_accommodation_on_discharge', true, 'bool', 'Gerbang #11: posting akomodasi otomatis saat kunjungan rawat inap pulang (ala storeAkomodasi)'],
            ['admission.block_discharged_patient', true, 'bool', 'Blokir admit bila registrasi terkait sudah pulang'],
            ['admission.check_double_visit', true, 'bool', 'Deteksi & tolak kunjungan aktif ganda pasien yang sama'],
            ['admission.check_bed_availability', true, 'bool', 'Gerbang #11: okupansi bed dicek atomik saat admit membawa bed'],
            ['order.allow_consultation', true, 'bool', 'PropertiConfig 118: izin terima konsul antar DPJP'],
            ['order.allow_lab', true, 'bool', 'PropertiConfig 118: izin order laboratorium dari rawat inap'],
            ['order.allow_radiology', true, 'bool', 'PropertiConfig 118: izin order radiologi dari rawat inap'],
            ['order.allow_pharmacy', true, 'bool', 'PropertiConfig 118: izin resep/order farmasi dari rawat inap'],
            ['general.hospital_name', null, 'string', 'Nama RS (diisi saat deploy)'],
            ['general.city_code', null, 'string', 'Kode kota/kabupaten Kemkes (diisi saat deploy)'],

            // === PropertiConfig dasar (data/properti_config.sql) ===
            ['pasien.norm_max_manual', 5000, 'int', 'PropertiConfig 1 MAX_NORM_MANUAL: batas atas nomor RM manual'],
            ['pasien.norm_allow_manual', true, 'bool', 'PropertiConfig 2 ALLOW_NORM_MANUAL: izinkan input norm manual'],
            ['billing.allow_final_layanan', false, 'bool', 'PropertiConfig 3 ALLOW_FINAL_LAYANAN_DI_PEMBAYARAN: finalisasi layanan dari modul pembayaran'],
            ['antrian.activate_room_queue', false, 'bool', 'PropertiConfig 4 AKTIFKAN_ANTRIAN_RUANGAN: antrian per ruangan aktif'],
            ['inventory.auto_distribute_new_items', true, 'bool', 'PropertiConfig 5 ATOMATIS_BARANG_BARU_MASUK_KE_RUANGAN: barang baru otomatis didistribusikan'],
            ['tariff.effective_from_registration_date', true, 'bool', 'PropertiConfig 6 BERLAKUKAN_TARIF_BARU_BERDASARKAN_TANGGAL_PENDAFTARAN'],
            ['tariff.consult_class_follows_inpatient_class', false, 'bool', 'PropertiConfig 7 TARIF_KELAS_KONSUL_MENGIKUTI_KELAS_RI_YG_MENGKONSUL'],
            ['tariff.outpatient_reg_class_follows_first_inpatient', false, 'bool', 'PropertiConfig 8 TARIF_KELAS_PENDAFTARAN_RJ_MENGIKUTI_KELAS_RI_YG_PERTAMA'],
            ['jkn.follow_hospital_policy', false, 'bool', 'PropertiConfig 9 ATURAN_JKN_MENGIKUTI_KEBIJAKAN_RS'],
            ['imaging.support_pacs_viewer', false, 'bool', 'PropertiConfig 10 SUPPORT_PACS_VIEWER'],
            ['printing.print_wristband', true, 'bool', 'PropertiConfig 12 CETAK_GELANG: cetak gelang identitas pasien'],
            ['pasien.infant_age_limit_days', 365, 'int', 'PropertiConfig 13 BATAS_UMUR_BAYI_DALAM_HARI'],
            ['accommodation.calculation_rule', '1', 'string', 'PropertiConfig 14 ATURAN_PERHITUNGAN_AKOMODASI (kode aturan)'],
            ['inacbg.medical_record_pad_digits', 0, 'int', 'PropertiConfig 15 LPAD_NOL_NORM_KIRIM_KE_INACBG_JUMLAH_DIGIT'],
            ['inacbg.min_tariff_difference_percent', 0, 'int', 'PropertiConfig 16 MINIMAL_SELISIH_DARI_TARIF_INACBG_DALAM_PERSEN'],
            ['billing.show_rs_tariff_for_upgraded_guarantor', false, 'bool', 'PropertiConfig 19 TAMPILKAN_TARIF_RS_UNTUK_PENJAMIN_NAIK_KELAS'],
            ['inacbg.manual_vip_upclass_percentage', true, 'bool', 'PropertiConfig 20 AKTIFKAN_MANUAL_PERSENTASE_INACBG_NAIK_VIP'],
            ['printing.force_pdf_output', true, 'bool', 'PropertiConfig 21 CETAK_SEMUA_OUTPUT_KE_FORMAT_PDF'],
            ['billing.single_admin_fee_when_merged', false, 'bool', 'PropertiConfig 22 KENAKAN_HANYA_SATU_TARIF_ADMINISTRASI_JIKA_GABUNG_TAGIHAN'],
            ['billing.admin_fee_by_patient_visit_history', false, 'bool', 'PropertiConfig 23 AKTIFKAN_TARIF_ADMINISTRASI_BERDASARKAN_PASIEN_BARU_ATAU_LAMA'],
            ['billing.new_patient_admin_fee_by_room_type', false, 'bool', 'PropertiConfig 24 KENAKAN_TARIF_ADMINISTRASI_PASIEN_BARU_JIKA_BELUM_TERDAFTAR_BERDASARKAN_JENIS_RUANGAN'],
            ['printing.auto_print_tracer', true, 'bool', 'PropertiConfig 25 AKTIFKAN_OTOMATIS_CETAK_TRACERT'],
            ['printing.support_printdoc', false, 'bool', 'PropertiConfig 26 SUPPORT_PRINTDOC'],
            ['printing.allow_tracer_inpatient', false, 'bool', 'PropertiConfig 29 ALLOW_CETAK_TRACERT_RAWAT_INAP'],

            // === PropertiConfig dari patch ===
            ['bed.reserved_bed_usage_deadline', '02:00:00', 'string', 'PropertiConfig 35 BATAS_WAKTU_RESERVASI_KAMAR_TIDUR_DIPESAN_DIGUNAKAN'],
            ['auth.failed_login_lock_duration', '00:01:00', 'string', 'PropertiConfig 37 BATAS_WAKTU_LOCK_GAGAL_LOGIN'],
            ['auth.max_failed_logins', 3, 'int', 'PropertiConfig 38 MAKSIMAL_GAGAL_LOGIN'],
            ['auth.captcha_enabled', false, 'bool', 'PropertiConfig 39 AKTIFKAN_CAPTCHA'],
            ['auth.captcha_font', 'ARLRDBD.TTF', 'string', 'PropertiConfig 40 CAPTCHA_FONT'],
            ['billing.unmerge_follows_merge_history', false, 'bool', 'PropertiConfig 41 BATAL_GABUNG_TAGIHAN_SESUAI_RIWAYAT_GABUNG'],
            ['pharmacy.order_by_service_depot', false, 'bool', 'PropertiConfig 42 AKTIFKAN_ORDER_RESEP_BY_DEPO_LAYANAN'],
            ['pharmacy.validate_medication_service_limit', false, 'bool', 'PropertiConfig 43 AKTIFKAN_VALIDASI_BATAS_LAYANAN_OBAT'],
            ['auth.captcha_dot_noise_level', 0, 'int', 'PropertiConfig 44 CAPTCHA_DOT_NOISE_LEVEL'],
            ['auth.captcha_line_noise_level', 0, 'int', 'PropertiConfig 45 CAPTCHA_LINE_NOISE_LEVEL'],
            ['pharmacy.hide_dashboard_receive_button', false, 'bool', 'PropertiConfig 46 HIDDEN_TOMBOL_TERIMA_RESEP_DI_WIDGET_DASHBOARD'],
            ['auth.idle_auto_lock_seconds', 900, 'int', 'PropertiConfig 47 AUTO_LOCK_APP_JIKA_USER_TDK_ADA_AKTIFITAS_DALAM_DETIK'],
            ['pharmacy.allow_order_out_of_stock', true, 'bool', 'PropertiConfig 48 ALLOW_ORDER_RESEP_DAN_LAYANAN_FARMASI_STOK_KOSONG'],
            ['admission.skip_new_visit_validation', false, 'bool', 'PropertiConfig 50 BUKA_VALIDASI_PENDAFTARAN_KUNJUNGAN_BARU'],
            ['pharmacy.send_to_all_service_depots', false, 'bool', 'PropertiConfig 52 OTOMATIS_KIRIM_RESEP_KE_SEMUA_DEPO_LAYANAN'],
            ['pharmacy.validate_prescription_days_supply', true, 'bool', 'PropertiConfig 53 AKTIFKAN_VALIDASI_JUMLAH_HARI_LAYANAN_RESEP'],
            ['pharmacy.screening_requires_all_checked', true, 'bool', 'PropertiConfig 54 VALIDASI_TELAAH_AKHIR_RESEP_HARUS_CENTANG_SEMUA'],

            // === Kunci DB hidup (tanpa dump; dirujuk kode numerik di simgos2) ===
            ['restriction.restricted_payers_visit', [], 'json', 'PropertiConfig 124 (hidup): daftar penjamin yang direstriksi saat pendaftaran kunjungan'],
            ['restriction.restricted_payers_prescription', [], 'json', 'PropertiConfig 125 (hidup): daftar penjamin yang direstriksi untuk order resep'],
            ['restriction.double_item_rooms', [], 'json', 'PropertiConfig 129 (hidup): ruangan dengan validasi item ganda'],
            ['pharmacy.executive_rooms', [], 'json', 'PropertiConfig 94 (hidup): daftar ruangan farmasi eksekutif'],
            ['pharmacy.executive_allowed_origins', [], 'json', 'PropertiConfig 95 (hidup): ruangan asal yang boleh memakai farmasi eksekutif'],

            // === Plugin jadwal operasi (ID 90081–90084) ===
            ['surgery.allow_direct_scheduling', false, 'bool', 'PropertiConfig 90081 PANJDWALAN_OPERASI_LANGSUNG'],
            ['surgery.schedule_request_cutoff_hour', 16, 'int', 'PropertiConfig 90082 BATAS_REQUEST_JADWAL_OPERASI (jam)'],
            ['surgery.admin_can_edit_schedule', true, 'bool', 'PropertiConfig 90083 EDIT_JADWAL_OPERASI_OLEH_ADMIN'],
            ['surgery.first_schedule_hour', 7, 'int', 'PropertiConfig 90084 JAM_JADWAL_AWAL_OPERASI'],

            // === Audit trail (#12; pola logs.* simgos2, tanpa nomor PropertiConfig) ===
            ['audit.activity_log_enabled', true, 'bool', 'Catat jejak aktivitas CRUD & milestone domain ke activity_logs'],
            ['audit.request_log_enabled', true, 'bool', 'Catat request API masuk ke request_logs'],
            ['audit.redact_fields', ['password', 'password_confirmation', 'token', 'remember_token', 'authorization'], 'json', 'Field sensitif yang direduksi sebelum jejak disimpan'],
            ['audit.prune_days', 365, 'int', 'Umur maksimum jejak audit (hari) untuk command audit:prune'],
        ];

        // Idempoten berdasar KEBERADAAN kunci, bukan nilai: cek `get() === null`
        // akan melewati kunci bool berdefault false (false !== null gagal).
        $existing = array_keys($settings->entries());

        foreach ($defaults as [$key, $value, $type, $description]) {
            if (! in_array($key, $existing, true)) {
                $settings->set($key, $value, $type, $description);
            }
        }
    }
}
