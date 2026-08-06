<?php

namespace Modules\LayananEarlyWarningScore\Services;

use Modules\LayananEarlyWarningScore\Models\VitalSignObservation;

/**
 * Kalkulator skor NEWS2 (National Early Warning Score 2) — port tabel skoring
 * EWS/NEWS2. Murni fungsi atas satu observasi tanda vital: tidak membaca DB,
 * tidak menulis DB, sehingga bisa diuji tanpa database dan aman dipanggil
 * berulang (mis. re-skor saat observasi lama ditampilkan ulang).
 *
 * Tabel skoring PERSIS mengikuti spesifikasi proyek (band inklusif di kedua
 * ujung):
 * - respiratory_rate : <=8=>3 | 9-11=>1  | 12-20=>0   | 21-24=>2  | >=25=>3
 * - spo2             : <=91=>3 | 92-93=>2  | 94-95=>1   | >=96=>0
 * - systolic_bp      : <=90=>3 | 91-100=>2 | 101-110=>1 | 111-219=>0| >=220=>3
 * - pulse_rate       : <=40=>3 | 41-50=>1  | 51-90=>0   | 91-110=>1 | 111-130=>2 | >=131=>3
 * - consciousness    : alert=>0 | voice/pain/unresponsive=>3
 * - temperature      : <=35.0=>3 | 35.1-36.0=>1 | 36.1-38.0=>0 | 38.1-39.0=>1 | >=39.1=>2
 */
class EwsCalculatorService
{
    public function calculate(VitalSignObservation $obs): array
    {
        $breakdown = [
            'respiratory_rate' => $this->scoreRespiratoryRate((int) $obs->respiratory_rate),
            'spo2' => $this->scoreSpo2((int) $obs->spo2),
            'systolic_bp' => $this->scoreSystolicBp((int) $obs->systolic_bp),
            'pulse_rate' => $this->scorePulseRate((int) $obs->pulse_rate),
            'consciousness_level' => $this->scoreConsciousnessLevel((string) $obs->consciousness_level),
            'temperature_celsius' => $this->scoreTemperatureCelsius((float) $obs->temperature_celsius),
        ];

        $totalScore = array_sum($breakdown);

        return [
            'totalScore' => $totalScore,
            'breakdown' => $breakdown,
            'riskLevel' => $this->resolveRiskLevel($totalScore, $breakdown),
        ];
    }

    private function scoreRespiratoryRate(int $value): int
    {
        return match (true) {
            $value <= 8 => 3,
            $value <= 11 => 1,
            $value <= 20 => 0,
            $value <= 24 => 2,
            default => 3,
        };
    }

    private function scoreSpo2(int $value): int
    {
        return match (true) {
            $value <= 91 => 3,
            $value <= 93 => 2,
            $value <= 95 => 1,
            default => 0,
        };
    }

    private function scoreSystolicBp(int $value): int
    {
        return match (true) {
            $value <= 90 => 3,
            $value <= 100 => 2,
            $value <= 110 => 1,
            $value <= 219 => 0,
            default => 3,
        };
    }

    private function scorePulseRate(int $value): int
    {
        return match (true) {
            $value <= 40 => 3,
            $value <= 50 => 1,
            $value <= 90 => 0,
            $value <= 110 => 1,
            $value <= 130 => 2,
            default => 3,
        };
    }

    /**
     * AVPU: hanya "alert" yang bernilai 0. Segala tingkat penurunan kesadaran
     * (voice/pain/unresponsive) langsung 3 — nilai tunggal 3 otomatis
     * memompakan risk level ke "tinggi" lewat resolveRiskLevel().
     */
    private function scoreConsciousnessLevel(string $level): int
    {
        return $level === VitalSignObservation::CONSCIOUSNESS_ALERT ? 0 : 3;
    }

    /**
     * Suhu dibandingkan dalam satuan per-sepuluh derajat (integer) supaya batas
     * band seperti 35.1 / 38.0 / 39.1 tidak tersandung presisi biner float.
     */
    private function scoreTemperatureCelsius(float $value): int
    {
        $tenth = (int) round($value * 10);

        return match (true) {
            $tenth <= 350 => 3, // <= 35.0
            $tenth <= 360 => 1, // 35.1 - 36.0
            $tenth <= 380 => 0, // 36.1 - 38.0
            $tenth <= 390 => 1, // 38.1 - 39.0
            default => 2,       // >= 39.1
        };
    }

    /**
     * Rendah: total 0-2. Sedang: total 3-4. Tinggi: total >=5 ATAU ada satu saja
     * parameter yang mencapai skor 3 (aturan "red flag" NEWS2 — satu parameter
     * kritis harusnya tetap memicu eskalasi meskipun totalnya kecil).
     */
    private function resolveRiskLevel(int $totalScore, array $breakdown): string
    {
        if ($totalScore >= 5 || in_array(3, $breakdown, true)) {
            return VitalSignObservation::RISK_TINGGI;
        }

        if ($totalScore >= 3) {
            return VitalSignObservation::RISK_SEDANG;
        }

        return VitalSignObservation::RISK_RENDAH;
    }
}
