<?php

namespace Modules\AuditIncidentReport\Tests\Unit;

use Modules\AuditIncidentReport\Services\IncidentReportService;
use Tests\TestCase;

/**
 * Matriks risiko 5x5 lengkap (baris = impact, kolom = probability).
 * Boundary: 1-3 BIRU, 4-6 HIJAU kecuali impact >= 4 → KUNING,
 * 8-12 KUNING, 15-25 MERAH.
 */
class RiskGradeMatrixTest extends TestCase
{
    public function test_full_5x5_matrix_grades(): void
    {
        $expected = [
            // p=1      p=2       p=3       p=4       p=5
            ['BIRU', 'BIRU', 'BIRU', 'HIJAU', 'HIJAU'],   // impact 1
            ['BIRU', 'HIJAU', 'HIJAU', 'KUNING', 'KUNING'], // impact 2
            ['BIRU', 'HIJAU', 'KUNING', 'KUNING', 'MERAH'], // impact 3
            ['KUNING', 'KUNING', 'KUNING', 'MERAH', 'MERAH'], // impact 4
            ['KUNING', 'KUNING', 'MERAH', 'MERAH', 'MERAH'],  // impact 5
        ];

        foreach ($expected as $i => $row) {
            foreach ($row as $j => $grade) {
                $this->assertSame(
                    $grade,
                    IncidentReportService::gradeFromScores($i + 1, $j + 1),
                    "Matriks ".($i + 1)."x".($j + 1)." harus {$grade}.",
                );
            }
        }
    }
}
