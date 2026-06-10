# SystemLicenseGuard Hardening Notes

## 1. Rate Limiting (Throttle Middleware)
Telah ditambahkan Laravel throttle middleware pada endpoint publik di `routes/api.php` yang sebelumnya belum memiliki perlindungan rate limiting. Hal ini bertujuan untuk mencegah serangan brute-force dan penyalahgunaan endpoint (abuse).
- `GET /status` -> `throttle:60,1`
- `GET /fingerprint` -> `throttle:60,1`
- `POST /activate` -> `throttle:10,1` (Lebih ketat karena merupakan proses sensitif)
- `POST /sync` -> `throttle:10,1` (Lebih ketat karena melakukan request ke central hub)

## 2. Pencegahan Information Disclosure
Dilakukan pengurangan field yang diekspos pada response endpoint `/fingerprint` di `LicenseController.php`. Field internal server yang tidak perlu diketahui oleh client (seperti `hostname`, `php_version`, dan `os`) telah dihapus. Hal ini bertujuan untuk meminimalisir attack surface dengan tidak memberikan informasi detail terkait infrastruktur internal ke pihak luar.
