<?php

namespace Modules\InventoryDietOrder\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\GeneralEmployee\Models\Employee;
use Modules\InventoryDietOrder\Models\DietOrder;
use Modules\InventoryDietOrder\Services\DietOrderService;
use Modules\PendaftaranVisit\Models\Visit;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class DietOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    /** Gerbang state machine: ordered -> prepared -> delivered adalah alur sah. */
    public function test_it_allows_the_full_happy_path_transition(): void
    {
        $dietOrder = DietOrder::factory()->create(['status' => DietOrder::STATUS_ORDERED]);
        $service = app(DietOrderService::class);

        $prepared = $service->transitionStatus($dietOrder, DietOrder::STATUS_PREPARED);
        $this->assertSame(DietOrder::STATUS_PREPARED, $prepared->status);

        $delivered = $service->transitionStatus($prepared, DietOrder::STATUS_DELIVERED);
        $this->assertSame(DietOrder::STATUS_DELIVERED, $delivered->status);
    }

    /** Gerbang state machine: status delivered adalah terminal, tidak boleh pindah lagi. */
    public function test_it_rejects_transition_out_of_a_terminal_status(): void
    {
        $dietOrder = DietOrder::factory()->create(['status' => DietOrder::STATUS_DELIVERED]);
        $service = app(DietOrderService::class);

        $this->expectException(HttpException::class);

        $service->transitionStatus($dietOrder, DietOrder::STATUS_CANCELLED);
    }

    /** Gerbang state machine: prepared boleh dibatalkan (belum sampai ke pasien). */
    public function test_it_allows_cancelling_a_prepared_order(): void
    {
        $dietOrder = DietOrder::factory()->create(['status' => DietOrder::STATUS_PREPARED]);
        $service = app(DietOrderService::class);

        $cancelled = $service->transitionStatus($dietOrder, DietOrder::STATUS_CANCELLED);

        $this->assertSame(DietOrder::STATUS_CANCELLED, $cancelled->status);
    }
}
