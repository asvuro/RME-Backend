<?php

namespace Modules\LayananEarlyWarningScore\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\LayananEarlyWarningScore\Models\VitalSignObservation;
use Modules\LayananEarlyWarningScore\Services\EwsCalculatorService;
use Tests\TestCase;

/**
 * Test matriks skoring NEWS2: setiap batas band diuji dengan nilai tepat di
 * garis batasnya supaya off-by-one (mis. 12 terbaca 1 padahal harusnya 0)
 * ketahuan. Observasi dibuat langsung sebagai instance model tanpa menyentuh
 * DB — kalkulator memang murni fungsi.
 */
class EwsCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    private EwsCalculatorService $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new EwsCalculatorService;
    }

    /** Semua parameter pada band skor 0 (baseline aman). */
    private function observation(array $overrides = []): VitalSignObservation
    {
        return new VitalSignObservation(array_merge([
            'respiratory_rate' => 14,
            'spo2' => 98,
            'systolic_bp' => 120,
            'pulse_rate' => 72,
            'consciousness_level' => 'alert',
            'temperature_celsius' => 36.8,
        ], $overrides));
    }

    public function test_scoring_table_matches_news2_specification(): void
    {
        $cases = [
            // respiratory_rate: <=8=>3 | 9-11=>1 | 12-20=>0 | 21-24=>2 | >=25=>3
            ['respiratory_rate', 8, 3],
            ['respiratory_rate', 9, 1],
            ['respiratory_rate', 11, 1],
            ['respiratory_rate', 12, 0],
            ['respiratory_rate', 20, 0],
            ['respiratory_rate', 21, 2],
            ['respiratory_rate', 24, 2],
            ['respiratory_rate', 25, 3],
            // spo2: <=91=>3 | 92-93=>2 | 94-95=>1 | >=96=>0
            ['spo2', 91, 3],
            ['spo2', 92, 2],
            ['spo2', 93, 2],
            ['spo2', 94, 1],
            ['spo2', 95, 1],
            ['spo2', 96, 0],
            // systolic_bp: <=90=>3 | 91-100=>2 | 101-110=>1 | 111-219=>0 | >=220=>3
            ['systolic_bp', 90, 3],
            ['systolic_bp', 91, 2],
            ['systolic_bp', 100, 2],
            ['systolic_bp', 101, 1],
            ['systolic_bp', 110, 1],
            ['systolic_bp', 111, 0],
            ['systolic_bp', 219, 0],
            ['systolic_bp', 220, 3],
            // pulse_rate: <=40=>3 | 41-50=>1 | 51-90=>0 | 91-110=>1 | 111-130=>2 | >=131=>3
            ['pulse_rate', 40, 3],
            ['pulse_rate', 41, 1],
            ['pulse_rate', 50, 1],
            ['pulse_rate', 51, 0],
            ['pulse_rate', 90, 0],
            ['pulse_rate', 91, 1],
            ['pulse_rate', 110, 1],
            ['pulse_rate', 111, 2],
            ['pulse_rate', 130, 2],
            ['pulse_rate', 131, 3],
        ];

        foreach ($cases as [$field, $value, $expectedScore]) {
            $result = $this->calculator->calculate($this->observation([$field => $value]));

            $this->assertSame(
                $expectedScore,
                $result['breakdown'][$field],
                "{$field}={$value} harus bernilai {$expectedScore}, dapat {$result['breakdown'][$field]}.",
            );
        }
    }

    public function test_consciousness_only_alert_scores_zero(): void
    {
        $this->assertSame(0, $this->calculator->calculate($this->observation(['consciousness_level' => 'alert']))['breakdown']['consciousness_level']);

        foreach (['voice', 'pain', 'unresponsive'] as $level) {
            $result = $this->calculator->calculate($this->observation(['consciousness_level' => $level]));

            $this->assertSame(3, $result['breakdown']['consciousness_level'], "Kesadaran {$level} harus skor 3.");
        }
    }

    public function test_temperature_band_boundaries_are_exact(): void
    {
        $cases = [
            [35.0, 3], // <= 35.0
            [34.5, 3],
            [35.1, 1], // 35.1 - 36.0
            [36.0, 1],
            [36.1, 0], // 36.1 - 38.0
            [38.0, 0],
            [38.1, 1], // 38.1 - 39.0
            [39.0, 1],
            [39.1, 2], // >= 39.1
        ];

        foreach ($cases as [$temperature, $expectedScore]) {
            $result = $this->calculator->calculate($this->observation(['temperature_celsius' => $temperature]));

            $this->assertSame(
                $expectedScore,
                $result['breakdown']['temperature_celsius'],
                "Suhu {$temperature} harus bernilai {$expectedScore}.",
            );
        }
    }

    public function test_risk_level_bands_follow_total_score(): void
    {
        $scenarios = [
            // [override, total yang diharapkan, risk level yang diharapkan]
            [[], 0, 'rendah'],
            [['respiratory_rate' => 22, 'temperature_celsius' => 38.5], 3, 'sedang'],
            [['respiratory_rate' => 22, 'pulse_rate' => 95, 'systolic_bp' => 105], 4, 'sedang'],
            [['respiratory_rate' => 25, 'spo2' => 93], 5, 'tinggi'],
        ];

        foreach ($scenarios as [$overrides, $expectedTotal, $expectedRisk]) {
            $result = $this->calculator->calculate($this->observation($overrides));

            $this->assertSame($expectedTotal, $result['totalScore']);
            $this->assertSame($expectedRisk, $result['riskLevel']);
        }
    }

    /**
     * Aturan red flag NEWS2: satu parameter saja yang mencapai skor 3 harus
     * langsung "tinggi", walau totalnya masih masuk band sedang (contoh: satu
     * SpO2 91 => total 3 tapi wajib eskalasi).
     */
    public function test_single_parameter_with_score_three_forces_high_risk(): void
    {
        $result = $this->calculator->calculate($this->observation(['spo2' => 91]));

        $this->assertSame(3, $result['totalScore']);
        $this->assertSame('tinggi', $result['riskLevel']);

        $result = $this->calculator->calculate($this->observation(['systolic_bp' => 88]));

        $this->assertSame(3, $result['totalScore']);
        $this->assertSame('tinggi', $result['riskLevel']);
    }
}
