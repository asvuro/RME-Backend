<?php

namespace Modules\LayananEarlyWarningScore\Services;

use Illuminate\Support\Facades\DB;
use Modules\LayananEarlyWarningScore\Models\VitalSignObservation;

/**
 * Satu-satunya jalur penulisan observasi tanda vital. Controller dilarang
 * memanggil VitalSignObservation::create() sendiri: total_score/risk_level
 * adalah kolom turunan yang HARUS lahir dari EwsCalculatorService di dalam
 * service ini, bukan dari payload klien (kalau tidak, skor bisa dipalsukan).
 */
class VitalSignObservationService
{
    public function __construct(protected EwsCalculatorService $calculator) {}

    /**
     * Simpan satu observasi + hitung NEWS2 sekaligus dalam satu transaksi.
     *
     * Asumsi desain:
     * - recorded_at opsional; kosong berarti "dicatat sekarang" (now()) —
     *   alat ukur pasien umumnya diinput real-time.
     * - Tidak ada gerbang status kunjungan (mis. tolak visit discharged):
     *   spesifikasi modul tidak memintanya dan pengukuran vital tetap sah
     *   dilakukan hingga akhir masa perawatan. Gerbang ditambahkan nanti
     *   hanya jika kebijakan klinis menuntut.
     */
    public function store(array $data): VitalSignObservation
    {
        return DB::transaction(function () use ($data) {
            // Pertahanan kedua setelah FormRequest: skor tak pernah dari klien.
            unset($data['total_score'], $data['risk_level']);
            $data['recorded_at'] ??= now();

            $observation = new VitalSignObservation($data);

            $result = $this->calculator->calculate($observation);
            $observation->total_score = $result['totalScore'];
            $observation->risk_level = $result['riskLevel'];

            $observation->save();

            return $observation;
        });
    }
}
