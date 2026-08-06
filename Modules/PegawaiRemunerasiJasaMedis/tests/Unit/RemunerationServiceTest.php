<?php

namespace Modules\PegawaiRemunerasiJasaMedis\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PegawaiRemunerasiJasaMedis\Services\RemunerationService;
use Tests\TestCase;

class RemunerationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculate_net_applies_percentage_then_fixed_deduction(): void
    {
        $service = new RemunerationService();

        $net = $service->calculateNet([
            'gross_amount' => 2000000,
            'deduction_percentage' => 15,
            'fixed_deduction' => 100000,
        ]);

        // 2.000.000 - (2.000.000 * 15%) - 100.000 = 1.600.000
        $this->assertEquals(1600000.0, $net);
    }

    public function test_calculate_net_aborts_when_gross_amount_is_zero(): void
    {
        $service = new RemunerationService();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $service->calculateNet([
            'gross_amount' => 0,
            'deduction_percentage' => 10,
        ]);
    }
}
