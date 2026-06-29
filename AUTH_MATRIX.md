# Matriks Otorisasi — RME-Backend

Dokumen ini merangkum kondisi *actual* mekanisme autentikasi/otorisasi di repo
ini per 2026-08-25, disusun dari pembacaan kode langsung (bukan asumsi/desain
ideal). Tujuannya sebagai referensi cepat: apa yang sudah ada, pola apa yang
dipakai, dan apa yang **belum** dikerjakan.

## 1. Model Role

- Menggunakan paket `spatie/laravel-permission`.
- Guard mengikuti guard default aplikasi (`config('auth.defaults.guard')` →
  `sanctum`).
- Hanya **2 role global**, dibuat oleh
  `database/seeders/RoleAndPermissionSeeder.php`:
  - `admin`
  - `petugas`
- Tidak ada definisi `Permission` granular per aksi — role dicek langsung via
  middleware `role:...`, bukan `permission:...`.

**Keterbatasan (bukan fitur yang sudah lengkap):** role bersifat **global**,
tidak ada scoping per unit/ruangan/fasilitas maupun per pasien. Seorang user
dengan role `petugas` memiliki hak yang sama di seluruh data lintas
unit/ward/pasien — tidak ada object-level authorization (mis. "petugas ward A
hanya boleh mengubah kunjungan di ward A"). Lihat Section 4 untuk detail.

## 2. Pola Gating Rute

Dibaca langsung dari beberapa `Modules/*/routes/api.php` yang representatif.

### 2.1 Pola standar: baca publik-otentikasi, tulis butuh role

Pola paling umum di modul-modul domain klinis/administratif — baca (`index`,
`show`) hanya butuh sesi valid, tulis (`store`/`update`/`destroy`/aksi state)
butuh role `petugas` atau `admin`.

Contoh — `Modules/GeneralAbsenceType/routes/api.php`:
```php
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('absence-types', AbsenceTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('absence-types', AbsenceTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
```

Contoh — `Modules/PendaftaranVisit/routes/api.php` (aksi state tambahan juga
di-gate role):
```php
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('visits', VisitController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('visits', VisitController::class)->only(['store', 'update', 'destroy']);
        Route::post('visits/{visit}/transfer', [VisitController::class, 'transfer']);
        Route::post('visits/{visit}/discharge', [VisitController::class, 'discharge']);
    });
});
```

### 2.2 Modul integrasi outbound: seluruh grup di-gate role

Modul yang memanggil layanan eksternal (BPJS V-Claim, BPJS Antrean, dsb.)
tidak membedakan baca/tulis — seluruh endpoint di dalam grup langsung
digate `auth:sanctum` + `role:petugas|admin` sejak awal, karena semua
operasinya berdampak transaksional ke sistem BPJS.

Contoh — `Modules/BpjsVClaim/routes/api.php`:
```php
Route::middleware(['auth:sanctum', 'role:petugas|admin'])->prefix('v1')->group(function () {
    // seluruh endpoint SEP, SPRI, Rencana Kontrol, Rujukan, Peserta, Referensi, PRB/LPK/Monitoring
    ...
});
```

Pola yang sama dipakai modul integrasi outbound lain (mis.
`Modules/BpjsAntreanRs`, blok "Outbound" untuk endpoint yang berasal dari
RS ke WS BPJS).

### 2.3 Endpoint publik yang sengaja tanpa `auth:sanctum`

Ada beberapa endpoint yang memang dirancang tanpa sesi login, dengan
mekanisme keamanannya sendiri:

| Endpoint | Modul | Mekanisme |
|---|---|---|
| `POST /v1/login` | `Auth` | Kredensial + `throttle:10,1` (tidak butuh sesi karena ini yang *menerbitkan* sesi) |
| `POST /v1/system/license/webhook` | `SystemLicenseGuard` | Verifikasi HMAC SHA-256 (`X-Hub-Signature-256`, `hash_equals`) + `throttle:30,1`; endpoint `status`/`fingerprint`/`activate`/`sync` di modul yang sama juga publik (device-bound, bukan user-bound), masing-masing dengan throttle sendiri |
| `GET /v1/antrean-rs/mobile-jkn/token` dan seluruh rute `mobile-jkn/*` | `BpjsAntreanRs` (juga pola sama di `BpjsAntreanFktp`) | Bukan `auth:sanctum` — token endpoint memvalidasi header `x-username`/`x-password`; endpoint lain di grup ini digate middleware custom `VerifyBpjsMobileJknToken` (cek header `x-token`/`x-username`) karena caller-nya adalah aplikasi Mobile JKN milik BPJS, bukan user internal |

Modul infrastruktur tanpa rute HTTP sama sekali (dipakai sebagai
service-kernel internal, bukan endpoint publik maupun ter-auth):
`Modules/Bpjs` (config/signature/crypto/HTTP client dasar dipakai
BpjsVClaim/BpjsApotek/BpjsPCare/dst), `Modules/BpjsSmartClaim` (ledger ID
FHIR outbound), `Modules/SatuSehat` (OAuth2 token cache + FHIR client dasar
dipakai SatuSehatRawatJalan/RawatInap/Igd/Farmasi/dst).

### 2.4 Ringkasan cakupan

Dari 579 file `Modules/*/routes/api.php`, 587 baris memakai middleware
`auth:sanctum` (sebagian modul mendaftarkan lebih dari satu grup rute).
Modul yang tidak menyebut `auth:sanctum` sama sekali hanya 4:
`Bpjs`, `BpjsSmartClaim`, `SatuSehat` (tanpa rute HTTP — lihat 2.3), dan
`SystemLicenseGuard` (publik dengan HMAC/throttle by design — lihat 2.3).

## 3. Domain Service Inti & Kontrak Lintas Modul

Kontrak lintas modul didefinisikan di `app/Modules/Contracts/*.php` dan
di-bind ke implementasinya di `app/Providers/AppServiceProvider.php`. Ini
adalah mekanisme "gate" level bisnis (mis. cegah posting ke kunjungan yang
sudah pulang / invoice yang sudah terkunci) — terpisah dari gating role di
Section 2, dan berlaku di dalam service layer, bukan di HTTP layer.

| Kontrak | Model/tabel yang dilindungi | Implementasi | Fungsi inti |
|---|---|---|---|
| `VisitGate` | `Visit` (kunjungan) | `Modules\PendaftaranVisit\Services\VisitService` | `isPatientDischarged()`, `isActive()` — cegah posting layanan ke kunjungan yang sudah pulang/nonaktif |
| `BedGate` | `Bed` (tempat tidur) | `Modules\GeneralBed\Services\BedService` | `occupy()`, `release()`, `setMaintenance()` — state machine okupansi bed |
| `BillingGate` | `Invoice` (tagihan) | `Modules\PembayaranInvoice\Services\InvoiceService` | `isVisitLocked()`, `lock()`/`unlock()`, `postServiceItem()` — modul klinis dilarang menyentuh tabel invoice langsung |
| `StockGate` | `WardStockTransaction` (mutasi stok ruangan) | `Modules\InventoryWardStockTransaction\Services\WardStockService` | `adjust()`, `currentStock()` — ledger mutasi stok per ward/item |
| `HospitalConfig` | `properti_config`-setara (setting RS) | `App\Support\RsSettingService` | `get()`/`set()`/`entries()` — gerbang konfigurasi bisnis (mis. `billing.lock_on_cashier_close`) |

Service lain yang disebut dalam scope tugas ini:
- `Modules\LayananPharmacyDispense\Services\DispenseService` — melindungi
  model `PharmacyDispense`, tidak diekspos sebagai kontrak lintas modul
  (`app/Modules/Contracts/`), hanya dipakai langsung oleh modul
  `LayananPharmacyDispense` sendiri.
- `Modules\CetakanPrintDocument\Services\PrintDocumentService` — melindungi
  model `PrintDocument`, juga tidak diekspos sebagai kontrak lintas modul.
- `Modules\AuditActivityLog\Support\AuditLogger` — bukan gate bisnis,
  melainkan satu pintu penulisan jejak aktivitas ke model `ActivityLog`
  (lihat Section 4 untuk cakupannya yang sebenarnya).

**Catatan:** hanya 5 kontrak yang ada di `app/Modules/Contracts/`
(`VisitGate`, `BedGate`, `BillingGate`, `StockGate`, `HospitalConfig`).
`DispenseService` dan `PrintDocumentService` **tidak** memiliki kontrak
lintas modul — keduanya dipakai lokal di modulnya masing-masing.

## 4. Audit Trail — Cakupan Aktual

Ada dua mekanisme yang menulis ke `Modules\AuditActivityLog\Models\ActivityLog`:

1. **Trait `Auditable`** (`Modules\AuditActivityLog\Support\Auditable`) —
   ditempel ke model, mencatat otomatis setiap `created`/`updated`/`deleted`.
   Dipakai oleh **3 model**: `Modules\GeneralBed\Models\Bed`,
   `Modules\PembayaranInvoice\Models\Invoice`,
   `Modules\PendaftaranVisit\Models\Visit`.
2. **`DomainEventAuditListener`** — mendengarkan 5 event domain
   (`VisitAdmitted`, `VisitTransferred`, `VisitDischarged`, `InvoiceLocked`,
   `PrescriptionDispensed`) yang di-dispatch dari
   `VisitService`, `InvoiceService`, dan `DispenseService`, lalu mencatat
   baris semantik `action='event'`.

**Temuan penting (deviasi dari asumsi awal):** cakupan audit write-path
**bukan** 6 entitas rata. Berdasarkan kode aktual:

| Entitas | Auditable trait? | Event domain ke audit listener? | Status |
|---|---|---|---|
| Visit | Ya | Ya (admit/transfer/discharge) | Teraudit penuh |
| Bed | Ya | Tidak ada event khusus | Teraudit (create/update/delete generik) |
| Invoice | Ya | Ya (`InvoiceLocked`) | Teraudit penuh |
| PharmacyDispense | Tidak | Ya (`PrescriptionDispensed`) | Teraudit lewat event saja, bukan trait |
| WardStockTransaction | **Tidak** | **Tidak** | **Belum teraudit** |
| PrintDocument | **Tidak** | **Tidak** | **Belum teraudit** |

Jadi dari 6 entitas yang disebut sebagai cakupan "audit write-path baru",
hanya **4 yang benar-benar tertulis ke `ActivityLog`** (Visit, Bed, Invoice,
PharmacyDispense) — `WardStockTransaction` dan `PrintDocument` model-nya
tidak memakai trait `Auditable` maupun men-dispatch event yang didengar
`DomainEventAuditListener`. Ini dicatat sebagai gap di Section 5.

## 5. Belum Dikerjakan / Technical Debt Diketahui

- **Object-level authorization belum ada.** Role bersifat global
  (`admin`/`petugas`); tidak ada scope per unit/ruangan/ward, per fasilitas,
  atau per pasien. Semua user dengan role yang sama punya akses identik ke
  seluruh baris data lintas unit. Tidak ada Laravel Policy per model yang
  mengecek kepemilikan/keterkaitan unit request-er.
- **Konsolidasi modul granular (579 modul) belum dilakukan.** Struktur
  modular saat ini memecah domain menjadi banyak modul kecil (1
  referensi/tabel legacy ≈ 1 modul); belum ada langkah konsolidasi ke modul
  yang lebih kasar/domain-oriented.
- **Audit write-path baru mencakup 4 dari 6 entitas yang dimaksud**, bukan
  6 penuh: `Visit`, `Bed`, `Invoice`, `PharmacyDispense` sudah tertulis ke
  `ActivityLog`; `WardStockTransaction` dan `PrintDocument` **belum**
  memakai trait `Auditable` maupun event yang didengar audit listener —
  mutasi stok ruangan dan penerbitan dokumen cetak saat ini tidak
  meninggalkan jejak audit otomatis. Entitas lain di luar 6 ini juga belum
  diaudit sistematis (audit masih daftar putih per entitas, bukan
  default-on).
- **Tidak ada `Permission` granular** di spatie/laravel-permission — hanya
  role. Middleware `role:petugas|admin` yang dipakai berulang di banyak
  modul pada praktiknya berarti "petugas dan admin setara" untuk sebagian
  besar aksi tulis; belum ada pemisahan hak yang lebih halus antar kedua
  role tsb.

## Lampiran — Sumber yang Dibaca

- `database/seeders/RoleAndPermissionSeeder.php`
- `Modules/GeneralAbsenceType/routes/api.php`
- `Modules/PendaftaranVisit/routes/api.php`
- `Modules/BpjsVClaim/routes/api.php`
- `Modules/BpjsAntreanRs/routes/api.php`
- `Modules/Auth/routes/api.php`
- `Modules/Bpjs/routes/api.php`, `Modules/BpjsSmartClaim/routes/api.php`,
  `Modules/SatuSehat/routes/api.php`
- `Modules/SystemLicenseGuard/app/Http/Controllers/LicenseController.php`
- `app/Modules/Contracts/{VisitGate,BedGate,BillingGate,StockGate,HospitalConfig}.php`
- `app/Providers/AppServiceProvider.php`
- `Modules/AuditActivityLog/app/Support/{AuditLogger,Auditable}.php`
- `Modules/AuditActivityLog/app/Listeners/DomainEventAuditListener.php`
- `Modules/GeneralBed/app/Models/Bed.php`,
  `Modules/PembayaranInvoice/app/Models/Invoice.php`,
  `Modules/PendaftaranVisit/app/Models/Visit.php`
- `Modules/InventoryWardStockTransaction/app/Models/WardStockTransaction.php`
- `Modules/CetakanPrintDocument/app/Models/PrintDocument.php`
- `modules_statuses.json` (579 modul terdaftar)
