# Status Implementasi Grup

## Selesai di RME-Backend

- Modul `Grup`, migrasi idempoten untuk cache grup/cabang, rujukan lintas cabang, nonce replay, dan event realtime.
- Membership read-only dari hub, dengan validasi bahwa instance lokal tercantum dalam snapshot lisensi.
- REST client satu tujuan yang tidak menerima base URL dari user; HTTPS wajib di luar local/testing.
- Endpoint user untuk konteks/cabang, pencarian/detail pasien lintas cabang, rujukan, dan status rujukan.
- Endpoint relay hub untuk pasien/snapshot klinis inti dan rujukan lokal.
- HMAC, freshness, replay prevention, group binding, target binding, throttle, encrypted PHI at rest, dan object-level same-group checks.
- Listener private-channel Reverb dan fallback webhook; event hanya membawa pointer non-PHI.
- Test kritis untuk membership authoritative, fixed hub destination/SSRF, HMAC replay/cross-group, dan isolasi cabang.

## Terbuka / perlu integrasi

- RME-License-Hub kosong pada saat pekerjaan ini; endpoint dan channel belum dapat diuji end-to-end.
- Provisioning `GRUP_HUB_TOKEN`, `GRUP_HUB_HMAC_SECRET`, `GRUP_INSTANCE_ID`, dan kredensial Reverb harus berasal dari alur penerbitan lisensi hub.
- Jalankan listener melalui Supervisor/systemd dan scheduler Laravel pada deployment.
- Audit akses PHI yang lebih granular, consent pasien, pemetaan kode klinis lintas versi, attachment/binary medical record, dan kebijakan retensi perlu keputusan produk/compliance.
- Hub perlu durable event cursor/ack untuk recovery; signed webhook sekarang menjadi fallback provisional.
- Snapshot klinis awal mencakup alergi, diagnosis, tanda vital, dan catatan SOAP. Modul rekam medis lain belum masuk kontrak versi pertama.
