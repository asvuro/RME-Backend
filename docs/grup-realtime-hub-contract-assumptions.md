# Asumsi Kontrak Grup Realtime–Hub

Tanggal: 2026-08-28. Dokumen kontrak RME-License-Hub belum tersedia (direktorinya kosong saat implementasi dimulai), sehingga kontrak berikut bersifat provisional dan harus direkonsiliasi sebelum produksi.

## Batas kepercayaan

- Hub adalah satu-satunya alamat outbound: `GRUP_HUB_URL`. Backend tidak menyimpan atau menghubungi URL cabang sibling.
- Membership ditentukan hub berdasarkan lisensi milik `GRUP_INSTANCE_ID`. Tidak ada endpoint join, pindah grup, tambah cabang, atau edit membership lokal.
- Kredensial REST outbound: Bearer `GRUP_HUB_TOKEN` + `X-RME-Instance-ID`.
- Request hub ke instance ditandatangani HMAC-SHA256 dengan `GRUP_HUB_HMAC_SECRET`. Material tanda tangan: `<unix_timestamp>\n<request_uuid>\n<raw_body>`.
- Header ingress wajib: `X-RME-Timestamp`, `X-RME-Request-ID`, `X-RME-Signature`, `X-RME-Group-ID`, dan `X-RME-Target-Instance-ID`. Toleransi waktu default 300 detik dan request ID hanya boleh dipakai sekali.
- Token/secret harus diterbitkan per instance, dapat dirotasi, dan tidak dikirim ke browser.

## REST instance → hub

- `GET /api/v1/group/context` → `{data:{group:{id,legal_name,legal_identifier,status},branches:[{id,instance_id,code,name,status,capabilities,last_seen_at}]}}`.
- `GET /api/v1/group/relay/patients?branch_id=&q=&page=&per_page=` → paginator pasien minimum. `q` minimal 3 karakter.
- `GET /api/v1/group/relay/branches/{branch_uuid}/patients/{patient_id}` → `{data:{demografi,clinical:{allergies,diagnoses,vital_signs,clinical_notes}}}`.
- `GET /api/v1/group/relay/referrals` dan `GET /api/v1/group/relay/referrals/{uuid}`.
- `POST /api/v1/group/relay/referrals` dan `PATCH /api/v1/group/relay/referrals/{uuid}` wajib menerima `Idempotency-Key` UUID dan mengembalikan `{data:{id,status,referred_at,...}}`.
- `POST /api/v1/group/realtime/auth` menerima `{socket_id,channel_name}` dan mengembalikan respons auth private-channel Pusher/Reverb (`{auth}`), setelah memastikan channel tepat milik instance pemanggil.

Hub wajib melakukan authorization ulang pada setiap operasi berdasarkan token instance: source, target, dan resource harus berada pada `group_id` badan hukum yang sama. ID cabang dari caller tidak boleh dianggap authoritative tanpa lookup lisensi hub.

## REST hub → instance

Base path: `/api/v1/grup/relay`, dilindungi middleware signature di atas.

- `GET /patients?q=&page=&per_page=` mencari pasien lokal.
- `GET /patients/{numeric_id}` mengambil snapshot klinis lokal.
- `GET /referrals/{uuid}` mengambil rujukan yang source/destination-nya melibatkan instance lokal.
- `POST /notifications` adalah fallback delivery bertanda tangan untuk payload notifikasi yang sama dengan Reverb.

## Reverb

- Reverb di-host hub; instance menjalankan proses supervisor `php artisan grup:listen`.
- Protokol kompatibel Pusher v7, private channel `private-grup.instance.{instance_id}`.
- Nama event `grup.notification`.
- Payload maksimal dan non-PHI: `{event_id:uuid,type:membership.updated|patient.updated|referral.created|referral.updated,resource_id:string|null,source_branch_id:uuid|null,version:int,occurred_at:ISO-8601}`.
- Event hanya menjadi sinyal invalidasi. Data pasien/rujukan selalu diambil ulang melalui REST relay. Event dideduplikasi persisten dengan `event_id`.
- Hub sebaiknya menyimpan event sampai acknowledgement/fallback berhasil; Reverb sendiri tidak dianggap jaminan delivery.

## Hal yang perlu dikunci bersama implementasi hub

- URL/path final, format error, pagination, retensi idempotency key, dan state machine rujukan.
- Mekanisme rotasi token/HMAC tanpa downtime dan kebijakan mTLS tambahan.
- Batas payload/rate-limit, audit access PHI, consent/legal basis, data-retention, serta versi schema (`contract_version`).
- Apakah fallback notification memakai webhook ini atau durable pull cursor. Durable pull cursor lebih disukai untuk recovery setelah listener offline.
