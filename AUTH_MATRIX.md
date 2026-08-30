# Matriks Otorisasi — RME-Backend

Dokumen ini merangkum kondisi *actual* mekanisme autentikasi/otorisasi di repo
ini per **2026-08-31**, disusun dari pembacaan kode langsung (bukan asumsi/desain
ideal). Tujuannya sebagai referensi cepat: apa yang sudah ada, pola apa yang
dipakai, dan apa yang **belum** dikerjakan.

> Revisi 2026-08-30/31: memperbarui versi 2026-08-25 setelah migrasi seluruh rute
> ke RBAC dinamis `RoutePermissionGate` (commit `aee12555`, `263a28d5`,
> `3d2d62ab`, ditambah modul `Grup`), pengaktifan 23 modul baru, penulisan
> ulang middleware `LogApiRequests` menjadi berbasis allowlist, dan penutupan
> gap audit write-path `WardStockTransaction`/`PrintDocument`.

## 1. Model Role & Permission

- Menggunakan paket `spatie/laravel-permission`. Guard mengikuti guard default
  aplikasi (`config('auth.defaults.guard')` → `sanctum`).
- Masih hanya **2 role global**, dibuat oleh
  `database/seeders/RoleAndPermissionSeeder.php`:
  - `admin`
  - `petugas`
- Sejak RBAC dinamis, ada juga **~2.925 Permission granular** bernama
  `{module}.{controller}.{method}`, digenerate dari seluruh rute terdaftar oleh
  `php artisan rbac:sync-permissions` dan di-commit sebagai fixture statis
  `database/seeders/data/route_permissions.php` (sumber kebenaran tunggal,
  dibaca seeder DAN gerbang — keduanya tidak scan rute sendiri).
- Grant baseline saat ini masih **meniru perilaku 2-role lama** (cutover
  behavior-preserving): `admin` = semua permission non-public;
  `petugas` = semua permission tier `petugas_admin` + `authenticated_any`.
- **Keterbatasan yang masih berlaku:** role bersifat **global**, tidak ada
  scoping per unit/ruangan/fasilitas maupun per pasien di luar WardScope
  (lihat 4b). Permission granular sudah ADA sebagai infrastruktur, tetapi
  pemetaan role→permission masih baseline; belum ada role per-job-function
  (mis. kasir, perawat, apoteker) dengan grant berbeda.

## 2. Pola Gating Rute

### 2.1 Gerbang terpusat: RoutePermissionGate (menggantikan middleware role:)

Sejak 27 Agustus 2026 **tidak ada lagi middleware `role:petugas|admin` /
`role:admin` literal di file `routes/api.php` manapun** (terverifikasi grep —
yang tersisa hanya komentar penjelas). Otorisasi per-aksi kini lewat SATU
middleware global `Modules\Authorization\Http\Middleware\RoutePermissionGate`
(di-append ke grup `api` di `bootstrap/app.php`, bersama `LogApiRequests`):

1. Ambil `Controller@method` dari rute, cocokkan ke peta statis
   `RoutePermissionFixture::map()` (di-cache forever).
2. Tier `public` → lolos tanpa sesi.
3. Tier `authenticated_any` → wajib login, **sengaja tidak dicek permission**
   (rute itu dulu juga hanya `auth:sanctum` tanpa `role:` — mewajibkan
   permission adalah pengetatan baru yang tidak diminta; nama permission
   tetap dicatat sebagai metadata untuk masa depan).
4. Tier `petugas_admin` / `admin_only` → wajib `$user->can(permission)`.
5. Rute Closure ad-hoc (hanya terjadi di dalam test) → dilewatkan.
6. Rute controller yang BELUM ada di peta (modul baru / lupa sync) →
   **fail-closed 403** dengan pesan "jalankan php artisan rbac:sync-permissions".

Klasifikasi 2.958 `controller_action` pada fixture (angka per 2026-08-30):

| Tier | Jumlah | Efek gerbang |
|---|---|---|
| `public` | 32 | Tanpa sesi |
| `admin_only` | 31 | Login + permission khusus (baseline: hanya role admin) |
| `petugas_admin` | 1.720 | Login + permission (baseline: petugas & admin) |
| `authenticated_any` | 1.175 | Login saja (permission tercatat tapi belum ditegakkan) |

Endpoint `admin_only` meliputi area paling sensitif: manajemen
Role/Permission/UserRole dan User (modul `Authorization` & `Auth`),
`ActivityLogController@index`, `RequestLogController@index`,
`RsSettingController@store/update`, CRUD General Ledger (`FinanceGeneralLedger`),
retensi rekam medis (`MedicalRecordRetentionSchedule`), `InvoiceGuarantorController@unlock`,
`TteDocumentController@lock`, `LicenseController@status/fingerprint`,
`GroupContextController@sync`. Ini menutup temuan privilege-escalation lama
pada modul Authorization (endpoint RBAC dulu hanya `auth:sanctum`).

Endpoint bantu: `MyAccessController@index` (`Modules/Authorization`) mengembalikan
modul + permission efektif milik user yang sedang login — dipakai frontend
untuk membangun menu, dan konsisten dengan tier `authenticated_any` yang tidak
digerbang permission.

### 2.2 Endpoint publik yang sengaja tanpa `auth:sanctum`

Dari 32 entri tier `public` (3 di antaranya rute framework: `sanctum/csrf-cookie`,
`up`, `storage/{path}`), semuanya punya mekanisme keamanannya sendiri:

| Endpoint | Modul | Mekanisme |
|---|---|---|
| `POST /v1/login` | `Auth` | Kredensial + `throttle:10,1` |
| `POST /v1/system/license/webhook` + `activate`/`sync` | `SystemLicenseGuard` | HMAC SHA-256 (`X-Hub-Signature-256`, `hash_equals`), fail-closed saat secret kosong, throttle per-endpoint; device-bound, bukan user-bound |
| `GET /v1/antrean-*/mobile-jkn/*` (token + antrean, 18 rute) | `BpjsAntreanRs`, `BpjsAntreanFktp` | Bukan `auth:sanctum` — token endpoint memvalidasi header `x-username`/`x-password`; rute lain digate middleware custom `VerifyBpjsMobileJknToken` (header `x-token`) karena caller-nya aplikasi Mobile JKN milik BPJS |
| `HubRelayController@patients/patient/referral`, `RealtimeNotificationController@store` | `Grup` | Relay hub lintas cabang: HMAC + freshness + anti-replay (nonce) + group/target binding + throttle + PHI terenkripsi at rest; base URL hub tidak pernah dari user (anti-SSRF) |

Modul infrastruktur tanpa rute HTTP sama sekali (dipakai sebagai service-kernel
internal): `Modules/Bpjs` (config/signature/crypto/HTTP client dasar),
`Modules/BpjsSmartClaim`, `Modules/SatuSehat` (OAuth2 token cache + FHIR client).

### 2.3 Cakupan

Fixture RBAC mencakup 2.958 `controller_action` untuk 603 modul aktif
(`modules_statuses.json`). Rute controller modul yang belum terdaftar di
fixture gagal-closed 403 — jadi modul baru otomatis TERKUNCI sampai
`rbac:sync-permissions` dijalankan, bukan terbuka.

## 3. Domain Service Inti & Kontrak Lintas Modul

Kontrak lintas modul didefinisikan di `app/Modules/Contracts/*.php` dan
di-bind ke implementasinya di `app/Providers/AppServiceProvider.php`. Ini
adalah mekanisme "gate" level bisnis (mis. cegah posting ke kunjungan yang
sudah pulang / invoice yang sudah terkunci) — terpisah dari gating RBAC di
Section 2, dan berlaku di dalam service layer, bukan di HTTP layer.

| Kontrak | Model/tabel yang dilindungi | Implementasi | Fungsi inti |
|---|---|---|---|
| `VisitGate` | `Visit` (kunjungan) | `Modules\PendaftaranVisit\Services\VisitService` | `isPatientDischarged()`, `isActive()` — cegah posting layanan ke kunjungan yang sudah pulang/nonaktif |
| `BedGate` | `Bed` (tempat tidur) | `Modules\GeneralBed\Services\BedService` | `occupy()`, `release()`, `setMaintenance()` — state machine okupansi bed |
| `BillingGate` | `Invoice` (tagihan) | `Modules\PembayaranInvoice\Services\InvoiceService` | `isVisitLocked()`, `lock()`/`unlock()`, `postServiceItem()` — modul klinis dilarang menyentuh tabel invoice langsung |
| `StockGate` | `WardStockTransaction` (mutasi stok ruangan) | `Modules\InventoryWardStockTransaction\Services\WardStockService` | `adjust()`, `currentStock()` — ledger mutasi stok per ward/item |
| `HospitalConfig` | `properti_config`-setara (setting RS) | `App\Support\RsSettingService` | `get()`/`set()`/`entries()` — gerbang konfigurasi bisnis (mis. `billing.lock_on_cashier_close`) |
| `WardScope` | objek milik ward | `App\Support\WardAccessResolver` | `canAccessWard()`, `assignedWardIds()`, `applyReadScope()` — least-privilege per ward (lihat 4b) |

Service lain yang dilindungi lokal (tanpa kontrak lintas modul):
`DispenseService` (model `PharmacyDispense`), `PrintDocumentService`
(model `PrintDocument`), `AuditLogger` (satu pintu tulis `ActivityLog`).

## 4. Audit Trail — Cakupan Aktual

Ada tiga mekanisme penulisan jejak:

1. **Trait `Auditable`** (`Modules\AuditActivityLog\Support\Auditable`) —
   otomatis mencatat `created`/`updated`/`deleted`. Model pemakai saat ini
   (18): `Visit`, `Bed`, `Invoice`, `Payment`, `WardStockTransaction`,
   `PrintDocument` (payload dikecualikan — lihat catatan tabel), plus
   8 entitas finansial Pembayaran (`CashierTransaction`, `Deposit`, `Edc`,
   `Transfer`, `PatientReceivable`, `RegistrationInvoice`, `ClaimInvoice`,
   `CorporateReceivable`) dan model-model baru `AuditInfectionSurveillance`
   (DeviceDay, InfectionCase) dan `AuditQualityIndicator`
   (QualityIndicator, QualityIndicatorRecord). Trait menyediakan hook
   `auditHidden()` untuk kolom yang dilarang tersalin ke audit log
   (snapshot PHI/PII); perubahan pada kolom itu tetap tercatat sebagai
   peristiwa dengan isi di-mask `'[hidden]'`, bukan lenyap dari jejak.
2. **`DomainEventAuditListener`** — mendengarkan 5 event domain
   (`VisitAdmitted`, `VisitTransferred`, `VisitDischarged`, `InvoiceLocked`,
   `PrescriptionDispensed`) dan mencatat baris semantik `action='event'`.
3. **`LogApiRequests`** (`AuditRequestLog`, middleware global) — kini sudah
   **berbasis allowlist** (bukan blacklist lagi): hanya field referensi aman
   (`id`, `uuid`, `code`, `ref_id`, `external_id`, dst.), maks 20 field,
   maks 255 karakter per nilai, URL tanpa query string, hanya status response
   yang dicatat, bisa dimatikan per-instance via
   `HospitalConfig` key `audit.request_log_enabled`. Ini menutup temuan lama
   "audit log berisiko jadi shadow PHI store".

Cakupan audit write-path per entitas inti:

| Entitas | Auditable trait? | Event domain ke audit listener? | Status |
|---|---|---|---|
| Visit | Ya | Ya (admit/transfer/discharge) | Teraudit penuh |
| Bed | Ya | Tidak ada event khusus | Teraudit (create/update/delete generik) |
| Invoice | Ya | Ya (`InvoiceLocked`) | Teraudit penuh |
| Payment | Ya | Tidak ada event khusus | Teraudit (generik) |
| PharmacyDispense | Tidak | Ya (`PrescriptionDispensed`) | Teraudit lewat event saja |
| WardStockTransaction | Ya (sejak 2026-08-31) | Tidak | Teraudit (generik) |
| PrintDocument | Ya, `payload` dikecualikan (sejak 2026-08-31) | Tidak | Teraudit (generik; payload berisi snapshot identitas pasien — hanya document_number+ref yang dicatat, perubahan payload tercatat ter-mask `'[hidden]'`) |

## 4b. Least-Privilege per Ward — Status per 2026-08-30

Tidak berubah sejak 2026-08-25. `App\Modules\Contracts\WardScope`
(impl. `App\Support\WardAccessResolver`): rantai
`User -> Employee.user_id -> StaffMember/Doctor/Nurse.employee_id ->
*WardAssignment.ward_id`. `admin` selalu lolos; `petugas` yang PUNYA minimal
1 ward assignment dibatasi hanya ke ward itu; `petugas` TANPA assignment
sama sekali default masih akses penuh (rollout bertahap — lihat komentar di
`WardAccessResolver::canAccessWard()`). `applyReadScope()` juga tersedia untuk
membatasi query baca per ward.

Diterapkan ke 3 entitas yang benar-benar "milik" satu ward:

| Entitas | Method digate | Ward diambil dari |
|---|---|---|
| `Bed` | `store`/`update`/`destroy` (`BedController`/`BedService`) | `Room.ward_id` |
| `Visit` | `admit`/`transfer`/`discharge`/`cancel`/`updateDetails` (`VisitService`) | `Visit.ward_id` (transfer: asal ATAU tujuan) |
| `WardStockTransaction` | `store` (`InventoryWardStockTransactionController`, cuma endpoint langsung) | `ward_id` request |

**Sengaja TIDAK di-gate** (keputusan eksplisit, bukan lupa): `Invoice`
(billing/kasir) dan `PharmacyDispense` (farmasi) — keduanya fungsi
lintas-ward di operasional RS nyata. `StockGate`/`BedGate` yang dipanggil
INTERNAL dari modul lain juga tidak digate — hanya endpoint HTTP langsung
ke modul pemiliknya.

## 5. Belum Dikerjakan / Technical Debt Diketahui

- **Pengetatan tier `authenticated_any` belum dilakukan.** 1.175 aksi rute
  masih "login saja boleh" — nama permission-nya sudah tercatat di fixture,
  jadi menaikkan tier ke `petugas_admin`/`admin_only` cukup ubah fixture +
  re-seed, tapi keputusan per-route-nya belum pernah dibuat.
- **Pemetaan role→permission masih baseline 2-role.** Permission granular ada
  sebagai infrastruktur, tetapi belum ada role per-job-function (kasir,
  perawat, apoteker, DPJP, dst.) dengan grant berbeda — semua petugas masih
  setara untuk semua aksi tier `petugas_admin`.
- **Object-level authorization masih parsial.** Ward-scope hanya menyentuh
  Visit/Bed/WardStockTransaction (write) dengan rollout default-terbuka untuk
  user tanpa assignment. Belum ada scope per fasilitas atau per pasien
  (mis. "cuma DPJP pasien ini yang boleh ubah rekam mediknya").
- **Audit write-path cakupan bertambah tapi belum default-on.** Sejak
  2026-08-31, 18 model teraudit (6 entitas inti + 8 entitas finansial
  Pembayaran + DeviceDay/InfectionCase/QualityIndicator/
  QualityIndicatorRecord). Sisa ~608 model belum diaudit sistematis —
  prioritas terbesar domain MedicalRecord (178 model, PHI tinggi,
  keputusan cakupan audit masih terbuka) dan Pendaftaran (28 model).
  Audit masih daftar putih per-entitas, bukan default-on.
- **Konsolidasi modul granular (603 modul) belum dilakukan.** Struktur
  modular memecah domain menjadi banyak modul kecil (1 referensi/tabel legacy
  ≈ 1 modul); belum ada langkah konsolidasi ke bounded context yang lebih kasar.
- **Secret/deploy hygiene tetap catatan deploy:** `GRUP_HUB_TOKEN`,
  `GRUP_HUB_HMAC_SECRET`, `GRUP_INSTANCE_ID`, kredensial Reverb harus
  diprovision dari alur penerbitan lisensi hub; listener realtime + scheduler
  harus dijalankan Supervisor/systemd (lihat `docs/grup-status.md`).

## Lampiran — Sumber yang Dibaca (revisi 2026-08-30)

- `bootstrap/app.php` (registrasi middleware global `LogApiRequests` +
  `RoutePermissionGate`)
- `Modules/Authorization/app/Http/Middleware/RoutePermissionGate.php`
- `Modules/Authorization/app/Support/RoutePermissionFixture.php`
- `Modules/Authorization/app/Http/Controllers/MyAccessController.php`
- `Modules/Authorization/app/Models/RoutePermission.php`,
  `app/Console/Commands/SyncRoutePermissionsCommand.php`
- `database/seeders/data/route_permissions.php` (2.958 baris entri)
- `database/seeders/RoleAndPermissionSeeder.php`
- `Modules/AuditRequestLog/app/Http/Middleware/LogApiRequests.php`
- `Modules/AuditActivityLog/app/Listeners/DomainEventAuditListener.php`,
  `app/Support/{AuditLogger,Auditable}.php`
- `app/Support/WardAccessResolver.php`, `app/Modules/Contracts/*.php`
- `app/Providers/AppServiceProvider.php`
- `modules_statuses.json` (603 modul aktif)
- `docs/grup-status.md`
- git log: `aee12555`, `263a28d5`, `3d2d62ab`, `a36659d4`, `18c20349`,
  `46651284`, `8ae4a777`
