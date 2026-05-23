# Analisis RME-Backend

Tanggal: 25 Agustus 2026

Ruang lingkup dokumen ini adalah rangkuman analisis statis terhadap `RME-Backend`. Dokumen ini tidak didasarkan pada eksekusi test, benchmark, atau validasi runtime pada sesi ini. Fokusnya adalah arsitektur, keamanan, integritas domain, risiko data sensitif, dan checklist audit awal.

## Ringkasan Eksekutif

`RME-Backend` adalah backend modular besar untuk domain rumah sakit dan rekam medis dengan fondasi teknis yang cukup serius. Stack modern, pemisahan domain nyata, dan adanya service layer pada area penting menunjukkan sistem ini tidak dibangun secara ad hoc.

Masalah utamanya bukan framework, bukan kualitas coding dasar, dan bukan sekadar struktur folder. Risiko terbesarnya adalah ketidakseimbangan antara kompleksitas domain dengan kekuatan kontrol akses, pengendalian data sensitif, dan disiplin boundary arsitektur.

Kesimpulan utama:

- Risiko tertinggi ada pada `authorization`, bukan `authentication`.
- Audit logging saat ini berpotensi menyimpan terlalu banyak data sensitif.
- Invariant domain di area penting seperti `Visit` terlihat baik, tetapi kemungkinan masih bisa dibypass dari jalur lain.
- Modularisasi sangat luas, tetapi sudah cenderung terlalu granular dan membebani maintainability.

## Tingkat Keyakinan Temuan

Dokumen ini membedakan temuan menjadi tiga kategori agar lebih defensible secara engineering:

- `Fakta langsung dari kode`: terlihat langsung dari file yang dibaca.
- `Inferensi kuat`: kesimpulan teknis yang sangat mungkin benar berdasarkan pola kode yang terlihat, tetapi belum dibuktikan end-to-end.
- `Hipotesis yang perlu verifikasi`: dugaan masuk akal yang butuh audit kode tambahan atau uji runtime sebelum dianggap terbukti.

### Fakta Langsung dari Kode

- Proyek memakai `PHP 8.3`, `Laravel 13`, `Sanctum`, `spatie/laravel-permission`, dan `nwidart/laravel-modules`.
- Root [README.md](/home/dhrigo/Documents/Dev/RME-Backend/README.md) kosong.
- [bootstrap/app.php](/home/dhrigo/Documents/Dev/RME-Backend/bootstrap/app.php) memuat autoloading kustom untuk namespace `Modules\...`.
- [Modules/AuditRequestLog/app/Http/Middleware/LogApiRequests.php](/home/dhrigo/Documents/Dev/RME-Backend/Modules/AuditRequestLog/app/Http/Middleware/LogApiRequests.php) mencatat metadata request secara luas, termasuk `fullUrl` dan payload dengan filtering terbatas berbasis blacklist.
- [Modules/Authorization/routes/api.php](/home/dhrigo/Documents/Dev/RME-Backend/Modules/Authorization/routes/api.php) memperlihatkan endpoint otorisasi yang pada pembacaan saat itu tidak tampak dibatasi middleware admin/permission yang spesifik.
- Controller `Role`, `Permission`, dan `UserRole` di modul `Authorization` melakukan operasi RBAC langsung tanpa pemanggilan `authorize()`, `Gate`, atau middleware tambahan di controller.
- FormRequest `StoreRoleRequest`, `UpdateRoleRequest`, `StorePermissionRequest`, dan `AssignRoleRequest` mengembalikan `authorize(): true`.
- [Modules/PendaftaranVisit/app/Services/VisitService.php](/home/dhrigo/Documents/Dev/RME-Backend/Modules/PendaftaranVisit/app/Services/VisitService.php) menunjukkan adanya service layer, transaksi, validasi state, dan koordinasi domain untuk alur `Visit`.
- [Modules/PendaftaranVisit/routes/api.php](/home/dhrigo/Documents/Dev/RME-Backend/Modules/PendaftaranVisit/routes/api.php) melindungi route `visits` dengan `auth:sanctum`, tetapi tidak tampak memakai middleware role/permission spesifik.
- `VisitController` memiliki `destroy()` yang memanggil `$visit->delete()` langsung, sedangkan `store`, `transfer`, dan `discharge` melewati `VisitService`.
- `StoreVisitRequest` dan `UpdateVisitRequest` mengembalikan `authorize(): true`.
- Webhook lisensi di `SystemLicenseGuard` sudah memverifikasi `X-Hub-Signature-256` dengan HMAC SHA-256 dan menolak request jika secret tidak dikonfigurasi.

### Inferensi Kuat

- Model keamanan sistem cenderung lebih kuat di `authentication` daripada `authorization`.
- Modul `Authorization` secara statis memiliki risiko tinggi menjadi jalur `privilege escalation`: route hanya `auth:sanctum`, FormRequest mengizinkan semua request terautentikasi, dan controller melakukan create/update/delete/sync role langsung.
- Audit request logging terlalu permisif untuk domain RME dan berpotensi membuat audit table menjadi penyimpanan data sensitif sekunder.
- Arsitektur modul sudah terlalu granular dan akan menekan maintainability jangka menengah dan panjang.
- Invariant domain `Visit` terlihat dirancang baik, tetapi perlindungannya kemungkinan sangat bergantung pada disiplin semua jalur write untuk selalu lewat service yang benar.

### Hipotesis yang Perlu Verifikasi

- User non-admin benar-benar bisa melakukan perubahan role/permission pada runtime.
- Banyak endpoint klinis, operasional, atau finansial benar-benar tidak punya policy/gate tambahan di luar route.
- Ada jalur write lain yang membypass service utama pada entitas sensitif seperti `Visit`.
- Endpoint webhook lisensi belum terlihat memiliki replay protection berbasis timestamp/nonce/idempotency key, walaupun signature HMAC sudah ada.
- Endpoint publik lisensi selain webhook, seperti `status`, `fingerprint`, `activate`, dan `sync`, masih perlu ditinjau dari sisi informasi yang diekspos dan rate limiting.
- Ada risiko `partial success` nyata pada sinkronisasi `Visit`, billing, bed occupancy, atau event pada runtime.

### Batas Analisis

Hal-hal berikut tidak dilakukan dalam analisis ini:

- tidak ada pengujian request end-to-end
- tidak ada verifikasi seluruh controller, policy, gate, observer, dan middleware kustom di semua modul
- tidak ada uji exploitability
- tidak ada pembuktian runtime bahwa semua risiko benar-benar aktif

Karena itu, dokumen ini paling tepat dipakai sebagai `risk assessment awal`, bukan sebagai `bukti final`.

## Konteks Teknis

Beberapa karakteristik teknis utama yang sudah teridentifikasi:

- Stack utama menggunakan `PHP 8.3` dan `Laravel 13`.
- Autentikasi memakai `Laravel Sanctum`.
- Otorisasi memakai `spatie/laravel-permission`.
- Struktur aplikasi berbasis modular monolith dengan `nwidart/laravel-modules`.
- Terdapat ratusan modul aktif, ratusan model, controller, route, migration, dan ratusan file test.
- `bootstrap/app.php` memuat autoloading kustom untuk namespace `Modules\...`.
- `README.md` di root proyek kosong, sehingga dokumentasi operasional sangat minim.

## Temuan Prioritas Tinggi

### 1. Broken Authorization

Pola yang terlihat menunjukkan banyak endpoint sensitif hanya dilindungi oleh `auth:sanctum`. Untuk aplikasi rekam medis, ini tidak cukup. Login hanya membuktikan identitas, bukan hak akses terhadap data dan aksi domain tertentu.

Risiko:

- User internal dapat mengakses domain yang terlalu luas.
- Batas antar fungsi klinis, operasional, dan finansial menjadi kabur.
- Frontend berpotensi menjadi satu-satunya pembatas perilaku, bukan backend.

### 2. Privilege Escalation di Modul Authorization

Pada [Modules/Authorization/routes/api.php](/home/dhrigo/Documents/Dev/RME-Backend/Modules/Authorization/routes/api.php), endpoint manajemen role dan permission tampak hanya dijaga autentikasi biasa. Secara statis, ini memberi indikasi bahwa user login biasa bisa berpotensi:

- membuat role
- membuat permission
- mengubah permission pada role
- mengubah role user

Jika benar, maka kontrol keamanan sistem berubah menjadi data yang dapat dimanipulasi oleh aktor yang seharusnya tidak berwenang.

### 3. Risiko Data Sensitif pada Audit Logging

Di [Modules/AuditRequestLog/app/Http/Middleware/LogApiRequests.php](/home/dhrigo/Documents/Dev/RME-Backend/Modules/AuditRequestLog/app/Http/Middleware/LogApiRequests.php), request dicatat secara luas, termasuk URL penuh dan payload.

Masalah utama:

- filtering masih berbasis blacklist
- `fullUrl` dapat membawa query string sensitif
- payload dapat berisi data pasien, data klinis, data billing, atau identifier internal
- audit log berpotensi menjadi salinan kedua data sensitif

Untuk sistem RME, ini adalah temuan prioritas tinggi karena audit tidak boleh berubah menjadi repositori PHI/PII sekunder.

### 4. Endpoint Publik untuk License/Integration Perlu Audit Khusus

Pada `SystemLicenseGuard`, ada endpoint publik seperti status, aktivasi, sinkronisasi, dan webhook. Ini belum otomatis salah, tetapi area seperti ini wajib dipastikan memiliki:

- verifikasi signature
- secret management yang benar
- replay protection
- rate limit yang sesuai
- validasi asal request

Validasi statis tambahan menunjukkan webhook sudah memiliki HMAC signature verification dan fail-closed saat secret kosong. Karena itu, klaim bahwa webhook tidak memiliki signature verification tidak benar. Yang masih menjadi perhatian adalah replay protection, rate limiting pada endpoint publik lain, dan informasi yang diekspos oleh endpoint status/fingerprint.

## Analisis Domain Visit

Modul `PendaftaranVisit` adalah salah satu area yang relatif matang dari sisi desain. [Modules/PendaftaranVisit/app/Services/VisitService.php](/home/dhrigo/Documents/Dev/RME-Backend/Modules/PendaftaranVisit/app/Services/VisitService.php) menunjukkan pola yang baik:

- service layer dipakai untuk alur penting
- admit, transfer, dan discharge dibungkus transaksi
- ada validasi state
- ada pengecekan bed occupancy
- ada integrasi dengan billing dan bed management lewat contract
- ada event domain

Ini menunjukkan bahwa tim memahami bahwa `Visit` adalah proses bisnis, bukan CRUD biasa.

Namun ada beberapa risiko:

- invariant domain tampaknya hidup di service layer, sehingga rawan dibypass oleh jalur lain
- adanya operasi `destroy` pada entitas visit akan menjadi red flag bila memang tersedia untuk use case umum
- sinkronisasi visit, billing, dan bed occupancy berpotensi tidak selalu atomik
- pembatasan siapa yang boleh melakukan admit, transfer, atau discharge belum terlihat cukup kuat dari pola route yang ada

Kesimpulan khusus area `Visit`:

- logic inti terlihat baik
- risiko utamanya adalah bypass, bukan kelemahan model domain

## Analisis RBAC

Proyek ini menggunakan pondasi RBAC yang benar secara library, yaitu `spatie/laravel-permission`. Masalahnya ada pada enforcement.

Model keamanan yang terlihat cenderung `authenticated-first`, bukan `least privilege`.

Implikasi:

- user yang sudah login bisa memiliki akses terlalu luas
- role dan permission mungkin hanya menjadi fitur admin, bukan trust boundary sistem
- belum terlihat bukti kuat adanya pembatasan berbasis unit, fasilitas, pasien, atau encounter

Untuk domain rumah sakit, RBAC yang sehat biasanya memerlukan kombinasi:

- role platform
- role operasional
- scope per fasilitas atau unit
- permission per aksi domain
- object-level authorization

Saat ini yang paling mengkhawatirkan adalah kemungkinan bahwa backend terlalu percaya pada status login, bukan pada hak akses kontekstual.

## Analisis Audit dan Compliance Data Sensitif

### Risiko Utama

Audit request logging saat ini lebih kuat sebagai alat observability daripada sebagai kontrol compliance.

Risiko compliance yang perlu dicatat:

- kegagalan `data minimization`
- kaburnya `purpose limitation`
- mismatch antara sensitivitas data dan akses ke audit table
- risiko retensi berlebih
- audit menjadi titik kebocoran tambahan

### Dampak Nyata

Kalau middleware audit diterapkan luas ke seluruh `api/*`, maka endpoint dengan risiko tertinggi adalah:

- pencarian pasien
- pendaftaran
- visit/rawat inap
- asesmen dan medical record
- billing dan penjamin
- integrasi eksternal

Konsekuensinya, tabel audit bisa berkembang menjadi `shadow PHI store`.

### Arah Perbaikan yang Tepat

Pendekatan yang lebih aman:

- gunakan allowlist, bukan blacklist
- simpan metadata audit, bukan payload mentah
- batasi atau hapus query string dari logging
- batasi akses ke audit log
- tetapkan retensi yang jelas

## Analisis Arsitektur

### Kelebihan

- Modular monolith adalah pilihan yang masuk akal untuk domain rumah sakit.
- Ada upaya nyata memisahkan domain.
- Service layer dan event sudah dipakai di area tertentu.
- Belum ada indikasi pemecahan prematur ke microservices.

### Risiko Arsitektural

Masalah terbesar adalah `over-modularization`.

Dengan ratusan modul aktif, sistem tampak terstruktur, tetapi biaya koordinasinya tinggi:

- cognitive load tinggi
- tracing alur end-to-end sulit
- refactor lintas domain mahal
- ownership aturan bisnis bisa kabur

Autoloading kustom di [bootstrap/app.php](/home/dhrigo/Documents/Dev/RME-Backend/bootstrap/app.php) juga menambah kompleksitas non-standar:

- toolchain bisa kurang presisi
- debugging lebih sulit
- upgrade framework bisa lebih sensitif

### Kesimpulan Arsitektur

Arah arsitektur dasar tidak salah. Yang bermasalah adalah granularitas modul yang terlalu halus dan konsistensi penjagaan boundary yang belum selevel dengan kompleksitas bisnis.

## Top 10 Risk

1. Broken authorization pada level domain.
2. Privilege escalation pada modul role/permission.
3. Audit log terlalu permisif terhadap data sensitif.
4. Object-level access control belum tampak kuat.
5. Domain invariant berpotensi dibypass dari jalur lain.
6. Operasi delete/update pada entitas sensitif berisiko merusak integritas.
7. Partial success pada proses lintas domain seperti visit, billing, dan bed management.
8. Public integration/license endpoint perlu audit trust model.
9. Over-modularization menurunkan maintainability.
10. Dokumentasi teknis dan operasional sangat minim.

## Dampak Bisnis

Jika risiko-risiko di atas terjadi di produksi, dampaknya dapat berupa:

- akses tidak sah ke data rekam medis pasien
- perubahan status layanan oleh aktor yang tidak berwenang
- ketidaksinkronan antara proses klinis, bed management, dan billing
- audit trail menjadi sumber kebocoran data
- investigasi insiden menjadi lebih lambat
- biaya maintenance meningkat
- kecepatan delivery menurun

## Prioritas Pembenahan

Urutan pembenahan yang paling rasional:

1. Kunci endpoint di modul `Authorization`.
2. Audit semua route sensitif dan bedakan `authentication` dari `authorization`.
3. Terapkan model `least privilege` dengan scope domain yang jelas.
4. Perbaiki `AuditRequestLog` agar berbasis allowlist.
5. Paksa semua write path entitas inti lewat service/domain layer yang sama.
6. Review khusus entitas `Visit` dan transisi state pentingnya.
7. Audit endpoint publik lisensi dan webhook.
8. Konsolidasikan modul kecil ke bounded context yang lebih besar.
9. Dokumentasikan auth matrix, domain ownership, dan aturan data sensitif.

## Audit Checklist Awal

Checklist berikut dirancang sebagai daftar audit awal per modul atau per domain, tanpa bergantung pada eksekusi sistem.

### A. Route dan Access Control

- Apakah setiap route sensitif memiliki middleware selain `auth:sanctum`?
- Apakah route admin dibatasi dengan `role`, `permission`, atau policy yang spesifik?
- Apakah ada endpoint publik? Jika ada, apakah memang seharusnya publik?
- Apakah endpoint `index/show` memiliki pembatasan akses berdasarkan domain user?
- Apakah endpoint `update/delete` dibatasi lebih ketat daripada `read`?

### B. Controller dan Service Authorization

- Apakah controller memanggil `authorize()` atau mekanisme policy yang setara?
- Apakah service memvalidasi hak akses kontekstual, bukan hanya status login?
- Apakah ada aksi yang bergantung pada validasi frontend saja?
- Apakah ada operasi write langsung ke model yang seharusnya lewat service?

### C. Role, Permission, dan Scope

- Siapa yang boleh membuat role?
- Siapa yang boleh membuat permission?
- Siapa yang boleh mengubah role user?
- Apakah role berlaku global atau scoped per unit/fasilitas?
- Apakah permission cukup spesifik per aksi domain?
- Apakah ada object-level restriction untuk pasien, visit, atau dokumen klinis?

### D. Domain Invariant

- Apakah semua transisi penting melewati service/domain layer yang sama?
- Apakah ada `destroy` pada entitas sensitif?
- Apakah operasi `cancel/void/delete` dibedakan secara jelas?
- Apakah billing, status layanan, dan occupancy tetap sinkron saat failure parsial?
- Apakah ada constraint tambahan di database untuk mencegah state tidak valid?

### E. Audit dan Logging

- Data apa saja yang disimpan di audit log?
- Apakah query string ikut terekam?
- Apakah payload request lengkap disimpan?
- Apakah ada field medis atau identitas pasien yang ikut tercatat?
- Siapa yang bisa mengakses audit log?
- Berapa lama retensi audit log?
- Apakah audit log dienkripsi atau diproteksi tambahan?

### F. Endpoint Publik dan Integrasi

- Endpoint mana saja yang bisa diakses tanpa login?
- Apakah webhook menggunakan signature verification?
- Apakah ada replay protection?
- Apakah rate limiting cukup ketat?
- Apakah secret rotation dimungkinkan?
- Apakah payload integrasi divalidasi secara ketat?

### G. Arsitektur dan Maintainability

- Apakah modul saat ini benar-benar mewakili bounded context?
- Modul mana yang terlalu kecil dan layak dikonsolidasikan?
- Di mana source of truth untuk aturan bisnis utama?
- Apakah dependency antar modul jelas dan terdokumentasi?
- Apakah ada domain yang terlalu bergantung pada utilitas silang?

### H. Dokumentasi dan Operasional

- Apakah ada README setup yang memadai?
- Apakah auth matrix terdokumentasi?
- Apakah alur pasien, visit, billing, dan audit terdokumentasi?
- Apakah proses incident response terhadap data sensitif sudah jelas?
- Apakah ada owner teknis untuk domain-domain utama?

## Penilaian Akhir

Secara keseluruhan, `RME-Backend` adalah sistem yang serius dan punya potensi kuat, tetapi profil risikonya masih tinggi untuk domain rekam medis. Masalah utamanya adalah:

- authorization belum tampak cukup ketat
- data sensitif berpotensi terlalu luas tersebar ke audit
- invariant domain penting belum tentu dipaksa secara konsisten di semua jalur
- arsitektur terlalu granular untuk sehat dalam jangka panjang

Kalau pembenahan dilakukan dengan prioritas yang benar, sistem ini masih sangat layak diperkuat tanpa perlu rewrite total.
